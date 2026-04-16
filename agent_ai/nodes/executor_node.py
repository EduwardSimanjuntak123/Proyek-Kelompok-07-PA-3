import json
import logging
import traceback
import re
from datetime import date, datetime
from decimal import Decimal
import html
from tools.grouping import create_group
from tools.dosen_tools import get_dosen_by_dosen_context
from tools.mahasiswa_tools import get_mahasiswa_by_dosen_context, get_mahasiswa_without_kelompok_by_context
from tools.kelompok_tools import (
    check_existing_kelompok_by_context,
    delete_kelompok_by_context,
    get_anggota_kelompok_by_nomor,
    get_kelompok_by_dosen_context,
    get_kelompok_with_anggota_by_context,
)
from tools.matakuliah_tools import get_matakuliah_list
from tools.prodi_tools import get_prodi_list
from tools.pembimbing_tools import (
    check_existing_pembimbing_by_context,
    generate_pembimbing_assignments_by_context,
    get_kelompok_with_one_pembimbing_by_context,
    get_kelompok_with_two_pembimbing_by_context,
    get_kelompok_without_pembimbing_by_context,
    get_pembimbing_assignments_by_context,
    get_pembimbing_by_dosen_name,
    get_pembimbing_list,
    get_pembimbing_of_kelompok,
)
from tools.penguji_tools import (
    check_existing_penguji_by_context,
    generate_penguji_assignments_by_context,
    get_penguji_assignments_by_context,
    get_penguji_of_kelompok,
)
from tools.dosen_role_tools import get_dosen_roles_by_dosen_context
from tools.jadwal_tools import get_jadwal_by_dosen_context
from tools.nilai_mahasiswa_tools import get_nilai_akhir_by_dosen_context
from tools.academic_tools import get_kategori_pa_list, get_tahun_ajaran_list, get_ruangan_list
from tools.roles_tools import get_roles_list
from tools.excel_tools import generate_excel_by_context

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)


def format_query_result(title: str, data_list: list, fields: list = None) -> str:
    """Format hasil query menjadi HTML Table"""
    if not data_list:
        return f"<p>Tidak ada data untuk <strong>{title}</strong></p>"
    
    # Tentukan field yang akan ditampilkan
    if fields is None and isinstance(data_list[0], dict):
        fields = list(data_list[0].keys())
    
    # Build HTML table
    html = f"<h2>Ditemukan {len(data_list)} {title}</h2>\n"
    html += "<table border='1' cellpadding='10' cellspacing='0' style='width:100%; border-collapse:collapse;'>\n"
    
    # Table header
    html += "  <thead style='background-color:#f2f2f2;'>\n"
    html += "    <tr>\n"
    for field in fields:
        field_name = field.replace('_', ' ').title()
        html += f"      <th style='padding:10px; text-align:left;'>{field_name}</th>\n"
    html += "    </tr>\n"
    html += "  </thead>\n"
    
    # Table body
    html += "  <tbody>\n"
    for idx, item in enumerate(data_list, 1):
        html += "    <tr>\n"
        if isinstance(item, dict):
            for field in fields:
                value = _format_cell_value(item.get(field, "-"))
                html += f"      <td style='padding:10px;'>{value}</td>\n"
        html += "    </tr>\n"
    html += "  </tbody>\n"
    html += "</table>\n"
    
    return html


def _format_cell_value(value):
    """Format nilai sel agar konsisten dan aman ditampilkan di HTML."""
    if value is None:
        return "-"

    if isinstance(value, bool):
        return "Ya" if value else "Tidak"

    if isinstance(value, (datetime, date)):
        return value.strftime("%Y-%m-%d %H:%M") if isinstance(value, datetime) else value.strftime("%Y-%m-%d")

    if isinstance(value, Decimal):
        return f"{float(value):.2f}"

    if isinstance(value, float):
        return f"{value:.2f}"

    if isinstance(value, (list, dict)):
        return html.escape(json.dumps(value, ensure_ascii=False))

    text = str(value).strip()
    return html.escape(text) if text else "-"


def format_grouping_result(grouping_result: dict) -> str:
    """Format hasil rekomendasi pembentukan kelompok menjadi HTML."""
    status = grouping_result.get("status")
    if status != "success":
        return f"<p>{html.escape(grouping_result.get('message', 'Gagal membuat kelompok.'))}</p>"

    summary = grouping_result.get("summary", {})
    instruction = grouping_result.get("instruction", {})
    groups = grouping_result.get("groups", [])

    result_html = ""
    result_html += "<h2>Rekomendasi Pembagian Kelompok</h2>"
    result_html += "<ul>"
    result_html += f"<li>Total mahasiswa kandidat: <strong>{summary.get('total_candidates', 0)}</strong></li>"
    result_html += f"<li>Total kelompok terbentuk: <strong>{summary.get('total_groups', 0)}</strong></li>"
    result_html += f"<li>Ukuran per kelompok: <strong>{', '.join(str(size) for size in summary.get('group_sizes', []))}</strong></li>"
    result_html += f"<li>Mahasiswa yang sudah punya kelompok (dikecualikan): <strong>{summary.get('excluded_existing_members', 0)}</strong></li>"
    result_html += "</ul>"

    result_html += "<h3>Parameter Instruksi</h3>"
    result_html += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
    result_html += "<tbody>"
    result_html += f"<tr><td><strong>Jumlah Kelompok</strong></td><td>{_format_cell_value(instruction.get('group_count'))}</td></tr>"
    result_html += f"<tr><td><strong>Target Ukuran</strong></td><td>{_format_cell_value(instruction.get('group_size'))}</td></tr>"
    result_html += f"<tr><td><strong>Minimal Anggota</strong></td><td>{_format_cell_value(instruction.get('min_size'))}</td></tr>"
    result_html += f"<tr><td><strong>Maksimal Anggota</strong></td><td>{_format_cell_value(instruction.get('max_size'))}</td></tr>"
    result_html += f"<tr><td><strong>Mode Acak</strong></td><td>{'Ya' if instruction.get('randomize') else 'Tidak'}</td></tr>"
    result_html += "</tbody>"
    result_html += "</table>"

    for group in groups:
        result_html += f"<h3>Kelompok {group.get('group_number')}</h3>"
        result_html += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
        result_html += "<thead style='background-color:#f2f2f2;'><tr><th>NIM</th><th>Nama</th><th>Angkatan</th><th>User ID</th></tr></thead><tbody>"
        for member in group.get("members", []):
            result_html += "<tr>"
            result_html += f"<td>{_format_cell_value(member.get('nim'))}</td>"
            result_html += f"<td>{_format_cell_value(member.get('nama'))}</td>"
            result_html += f"<td>{_format_cell_value(member.get('angkatan'))}</td>"
            result_html += f"<td>{_format_cell_value(member.get('user_id'))}</td>"
            result_html += "</tr>"
        result_html += "</tbody></table>"

    return result_html


def format_existing_check_result(check_result: dict) -> str:
    """Format hasil cek existing kelompok menjadi HTML."""
    if check_result.get("status") != "success":
        return f"<p>{html.escape(check_result.get('message', 'Gagal mengecek data kelompok.'))}</p>"

    total = check_result.get("total", 0)
    if total == 0:
        return "<p>Belum ada kelompok pada konteks ini. Anda bisa lanjut generate dan simpan kelompok baru.</p>"

    result_html = f"<h2>Ditemukan {total} kelompok yang sudah tersimpan</h2>"
    result_html += "<p>Jika ingin mengganti hasil lama, lakukan konfirmasi hapus/replace saat menyimpan.</p>"
    result_html += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
    result_html += "<thead style='background-color:#f2f2f2;'><tr><th>ID</th><th>Nomor Kelompok</th><th>Status</th></tr></thead><tbody>"

    for row in check_result.get("data", []):
        result_html += "<tr>"
        result_html += f"<td>{_format_cell_value(row.get('id'))}</td>"
        result_html += f"<td>{_format_cell_value(row.get('nomor_kelompok'))}</td>"
        result_html += f"<td>{_format_cell_value(row.get('status'))}</td>"
        result_html += "</tr>"

    result_html += "</tbody></table>"
    return result_html


def format_anggota_kelompok_result(result: dict) -> str:
    """Format hasil query anggota kelompok menjadi HTML."""
    if result.get("status") != "success":
        return f"<p>{html.escape(result.get('message', 'Gagal mengambil anggota kelompok.'))}</p>"

    group = result.get("group", {})
    data = result.get("data", [])

    if not data:
        return f"<p>Kelompok <strong>{_format_cell_value(group.get('nomor_kelompok'))}</strong> ditemukan, tetapi belum memiliki anggota.</p>"

    result_html = f"<h2>Anggota Kelompok { _format_cell_value(group.get('nomor_kelompok')) }</h2>"
    result_html += "<ul>"
    result_html += f"<li>ID Kelompok: <strong>{_format_cell_value(group.get('id'))}</strong></li>"
    result_html += f"<li>Status: <strong>{_format_cell_value(group.get('status'))}</strong></li>"
    result_html += f"<li>Total Anggota: <strong>{len(data)}</strong></li>"
    result_html += "</ul>"
    result_html += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
    result_html += "<thead style='background-color:#f2f2f2;'><tr><th>NIM</th><th>Nama</th><th>Email</th><th>Angkatan</th><th>Status</th></tr></thead><tbody>"

    for member in data:
        result_html += "<tr>"
        result_html += f"<td>{_format_cell_value(member.get('nim'))}</td>"
        result_html += f"<td>{_format_cell_value(member.get('nama'))}</td>"
        result_html += f"<td>{_format_cell_value(member.get('email'))}</td>"
        result_html += f"<td>{_format_cell_value(member.get('angkatan'))}</td>"
        result_html += f"<td>{_format_cell_value(member.get('status'))}</td>"
        result_html += "</tr>"

    result_html += "</tbody></table>"
    return result_html


def format_kelompok_detailed_result(kelompok_rows: list, include_relations: bool = False) -> str:
    """Format daftar kelompok beserta anggota, opsional pembimbing & penguji."""
    if not kelompok_rows:
        return "<p>Tidak ada kelompok pada konteks ini.</p>"

    title = "Daftar Kelompok dan Mahasiswa"
    if include_relations:
        title = "Daftar Kelompok, Mahasiswa, Pembimbing, dan Penguji"

    html_result = f"<h2>{title}</h2>"
    html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
    html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Nomor Kelompok</th><th>Status</th><th>Mahasiswa</th>"
    if include_relations:
        html_result += "<th>Pembimbing</th><th>Penguji</th>"
    html_result += "</tr></thead><tbody>"

    for row in kelompok_rows:
        anggota = row.get("anggota", []) or []
        if anggota:
            anggota_html = "<ol style='margin:0; padding-left:18px;'>"
            for m in anggota:
                nim = _format_cell_value(m.get("nim"))
                nama = _format_cell_value(m.get("nama"))
                anggota_html += f"<li>{nim} - {nama}</li>"
            anggota_html += "</ol>"
        else:
            anggota_html = "-"

        pembimbing_html = "-"
        penguji_html = "-"
        if include_relations:
            pembimbing_names = row.get("pembimbing_nama", []) or []
            penguji_names = row.get("penguji_nama", []) or []
            pembimbing_html = "<br>".join(_format_cell_value(name) for name in pembimbing_names) if pembimbing_names else "-"
            penguji_html = "<br>".join(_format_cell_value(name) for name in penguji_names) if penguji_names else "-"

        html_result += "<tr>"
        html_result += f"<td>{_format_cell_value(row.get('nomor_kelompok'))}</td>"
        html_result += f"<td>{_format_cell_value(row.get('status'))}</td>"
        html_result += f"<td>{anggota_html}</td>"
        if include_relations:
            html_result += f"<td>{pembimbing_html}</td>"
            html_result += f"<td>{penguji_html}</td>"
        html_result += "</tr>"

    html_result += "</tbody></table>"
    return html_result


def format_generate_pembimbing_result(result: dict) -> str:
    """Format hasil generate pembimbing menjadi HTML."""
    if result.get("status") != "success":
        return f"<p>{html.escape(result.get('message', 'Gagal generate pembimbing kelompok.'))}</p>"

    summary = result.get("summary", {})
    groups = result.get("groups", [])
    dosen_loads = result.get("dosen_loads", [])

    html_result = "<h2>Generate Pembimbing Kelompok Berhasil</h2>"
    html_result += "<ul>"
    html_result += f"<li>Total kelompok: <strong>{_format_cell_value(summary.get('total_kelompok'))}</strong></li>"
    html_result += f"<li>Total kandidat pembimbing: <strong>{_format_cell_value(summary.get('total_kandidat_pembimbing'))}</strong></li>"
    html_result += f"<li>Total assignment hasil generate: <strong>{_format_cell_value(summary.get('total_assignments'))}</strong></li>"
    html_result += "</ul>"

    html_result += "<h3>Distribusi Per Kelompok</h3>"
    html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
    html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Kelompok</th><th>Pembimbing 1</th><th>Pembimbing 2</th></tr></thead><tbody>"
    for group in groups:
        pembimbing_rows = group.get("pembimbing", []) or []
        pembimbing_1 = pembimbing_rows[0].get("dosen_nama", "-") if len(pembimbing_rows) >= 1 else "-"
        pembimbing_2 = pembimbing_rows[1].get("dosen_nama", "-") if len(pembimbing_rows) >= 2 else "-"

        html_result += "<tr>"
        html_result += f"<td>{_format_cell_value(group.get('nomor_kelompok'))}</td>"
        html_result += f"<td>{_format_cell_value(pembimbing_1)}</td>"
        html_result += f"<td>{_format_cell_value(pembimbing_2)}</td>"
        html_result += "</tr>"
    html_result += "</tbody></table>"

    if dosen_loads:
        html_result += "<h3>Beban Pembimbing per Dosen</h3>"
        html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
        html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Nama Dosen</th><th>Jabatan Akademik</th><th>Assigned Groups</th><th>Kapasitas</th></tr></thead><tbody>"
        for row in dosen_loads:
            html_result += "<tr>"
            html_result += f"<td>{_format_cell_value(row.get('dosen_nama'))}</td>"
            html_result += f"<td>{_format_cell_value(row.get('jabatan_akademik_desc'))}</td>"
            html_result += f"<td>{_format_cell_value(row.get('assigned_groups'))}</td>"
            html_result += f"<td>{_format_cell_value(row.get('capacity'))}</td>"
            html_result += "</tr>"
        html_result += "</tbody></table>"

        # Fallback tombol aksi agar tetap tampil pada hasil generate pembimbing
        # meskipun action-card terpisah dari frontend tidak ter-render.
        html_result += """
<div style='margin-top:12px; border:1px solid #fcd34d; background:#fffbeb; border-radius:10px; padding:10px;'>
    <h6 style='margin:0 0 8px 0;'>Aksi Hasil Generate Pembimbing</h6>
    <div style='display:flex; gap:8px; flex-wrap:wrap;'>
        <button type='button' class='btn btn-sm btn-primary save-generated-pembimbing-btn' style='cursor:pointer; pointer-events:auto; position:relative; z-index:2;' onclick='if(window.__savePembimbingInline){window.__savePembimbingInline(event);} return false;'>
            <i class='fas fa-database'></i> Simpan Pembimbing ke Database
        </button>
        <button type='button' class='btn btn-sm btn-warning regenerate-pembimbing-btn' style='cursor:pointer; pointer-events:auto; position:relative; z-index:2;' onclick='if(window.__regeneratePembimbingInline){window.__regeneratePembimbingInline(event);} return false;'>
            <i class='fas fa-random'></i> Acak Ulang Pembimbing
        </button>
    </div>
</div>
"""

    return html_result


def format_generate_penguji_result(result: dict) -> str:
    """Format hasil generate penguji menjadi HTML."""
    if result.get("status") != "success":
        return f"<p>{html.escape(result.get('message', 'Gagal generate penguji kelompok.'))}</p>"

    summary = result.get("summary", {})
    groups = result.get("groups", [])
    dosen_loads = result.get("dosen_loads", [])

    html_result = "<h2>Generate Penguji Kelompok Berhasil</h2>"
    html_result += "<ul>"
    html_result += f"<li>Total kelompok: <strong>{_format_cell_value(summary.get('total_kelompok'))}</strong></li>"
    html_result += f"<li>Total kandidat penguji: <strong>{_format_cell_value(summary.get('total_kandidat_penguji'))}</strong></li>"
    html_result += f"<li>Total assignment hasil generate: <strong>{_format_cell_value(summary.get('total_assignments'))}</strong></li>"
    html_result += "<li><strong>Aturan:</strong> Pembimbing kelompok yang sama tidak boleh menjadi penguji.</li>"
    html_result += "</ul>"

    html_result += "<h3>Distribusi Per Kelompok</h3>"
    html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
    html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Kelompok</th><th>Penguji 1</th><th>Penguji 2</th></tr></thead><tbody>"
    for group in groups:
        penguji_rows = group.get("penguji", []) or []
        penguji_1 = penguji_rows[0].get("dosen_nama", "-") if len(penguji_rows) >= 1 else "-"
        penguji_2 = penguji_rows[1].get("dosen_nama", "-") if len(penguji_rows) >= 2 else "-"

        html_result += "<tr>"
        html_result += f"<td>{_format_cell_value(group.get('nomor_kelompok'))}</td>"
        html_result += f"<td>{_format_cell_value(penguji_1)}</td>"
        html_result += f"<td>{_format_cell_value(penguji_2)}</td>"
        html_result += "</tr>"
    html_result += "</tbody></table>"

    if dosen_loads:
        html_result += "<h3>Beban Penguji per Dosen</h3>"
        html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
        html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Nama Dosen</th><th>Jabatan Akademik</th><th>Assigned Groups</th><th>Kapasitas</th></tr></thead><tbody>"
        for row in dosen_loads:
            html_result += "<tr>"
            html_result += f"<td>{_format_cell_value(row.get('dosen_nama'))}</td>"
            html_result += f"<td>{_format_cell_value(row.get('jabatan_akademik_desc'))}</td>"
            html_result += f"<td>{_format_cell_value(row.get('assigned_groups'))}</td>"
            html_result += f"<td>{_format_cell_value(row.get('capacity'))}</td>"
            html_result += "</tr>"
        html_result += "</tbody></table>"

    html_result += """
<div style='margin-top:12px; border:1px solid #86efac; background:#f0fdf4; border-radius:10px; padding:10px;'>
    <h6 style='margin:0 0 8px 0;'>Aksi Hasil Generate Penguji</h6>
    <div style='display:flex; gap:8px; flex-wrap:wrap;'>
        <button type='button' class='btn btn-sm btn-success save-generated-penguji-btn' style='cursor:pointer; pointer-events:auto; position:relative; z-index:2;' onclick='if(window.__savePengujiInline){window.__savePengujiInline(event);} return false;'>
            <i class='fas fa-database'></i> Simpan Penguji ke Database
        </button>
        <button type='button' class='btn btn-sm btn-warning regenerate-penguji-btn' style='cursor:pointer; pointer-events:auto; position:relative; z-index:2;' onclick='if(window.__regeneratePengujiInline){window.__regeneratePengujiInline(event);} return false;'>
            <i class='fas fa-random'></i> Acak Ulang Penguji
        </button>
    </div>
</div>
"""

    return html_result


def executor_node(state):
    try:
        user_id = state.get("user_id", "default")
        state["grouping_payload"] = None
        state["grouping_meta"] = None
        state["pembimbing_payload"] = None
        state["pembimbing_meta"] = None
        state["penguji_payload"] = None
        state["penguji_meta"] = None
        
        plan = state.get("plan", {})
        action = plan.get("action")
        
        # Extract dosen context dari state (payload dari UI)
        context = state.get("context", {})
        dosen_context_list = context.get("dosen_context", [])
        dosen_context = dosen_context_list[0] if dosen_context_list else None

        if action == "query_dosen":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_dosen (daftar dosen)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()
                prodi_id = dosen_context.get("prodi_id")
                logger.info(f"[{user_id}] 📍 Query dosen dengan context: prodi_id={prodi_id}")

                # Disambiguasi: "daftar dosen pembimbing" harus beda dari "daftar dosen".
                if "pembimbing" in prompt_lower:
                    result = get_pembimbing_assignments_by_context(
                        prodi_id=prodi_id,
                        kategori_pa_id=dosen_context.get("kategori_pa"),
                        angkatan_id=dosen_context.get("angkatan"),
                    )

                    if result.get("status") == "success":
                        unique = {}
                        for row in result.get("data", []):
                            key = row.get("user_id") or row.get("dosen_nama")
                            if key not in unique:
                                unique[key] = {
                                    "dosen_nama": row.get("dosen_nama"),
                                    "dosen_email": row.get("dosen_email"),
                                    "dosen_prodi": row.get("dosen_prodi"),
                                    "jabatan_akademik_desc": row.get("jabatan_akademik_desc"),
                                }

                        state["result"] = format_query_result(
                            "dosen pembimbing",
                            list(unique.values()),
                            fields=["dosen_nama", "dosen_email", "dosen_prodi", "jabatan_akademik_desc"],
                        )
                        logger.info(f"[{user_id}] ✓ {len(unique)} dosen pembimbing ditemukan")
                    else:
                        state["result"] = result.get("message", "Belum ada data dosen pembimbing")
                        logger.warning(f"[{user_id}] ✗ query dosen pembimbing gagal")
                    return state

                result = get_dosen_by_dosen_context(prodi_id=prodi_id)
                
                if result.get("status") == "success":
                    state["result"] = format_query_result(
                        "dosen",
                        result['data'],
                        fields=['nama', 'email', 'prodi', 'jabatan_akademik']
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} dosen ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data dosen")
                    logger.warning(f"[{user_id}] ✗ query_dosen gagal")
        
        elif action == "query_mahasiswa":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_mahasiswa (daftar mahasiswa)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()
                prodi_id = dosen_context.get("prodi_id")
                angkatan_id = dosen_context.get("angkatan")
                logger.info(f"[{user_id}] 📍 Query mahasiswa dengan context: prodi_id={prodi_id}, angkatan_id={angkatan_id}")

                ask_without_group = any(
                    phrase in prompt_lower
                    for phrase in [
                        "belum punya kelompok",
                        "belum memiliki kelompok",
                        "tanpa kelompok",
                        "belum berkelompok",
                        "belum ada kelompok",
                    ]
                )

                if ask_without_group:
                    result = get_mahasiswa_without_kelompok_by_context(prodi_id=prodi_id, angkatan_id=angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            "mahasiswa belum punya kelompok",
                            result["data"],
                            fields=["nama", "nim", "email", "prodi", "angkatan", "status"],
                        )
                        logger.info(f"[{user_id}] ✓ {len(result['data'])} mahasiswa belum punya kelompok ditemukan")
                    else:
                        state["result"] = result.get("message", "Tidak ada mahasiswa yang belum punya kelompok")
                        logger.info(f"[{user_id}] ℹ️ mahasiswa belum punya kelompok: {result.get('status')}")
                    return state

                result = get_mahasiswa_by_dosen_context(prodi_id=prodi_id, angkatan_id=angkatan_id)
                
                if result.get("status") == "success":
                    state["result"] = format_query_result(
                        "mahasiswa",
                        result['data'],
                        fields=['nama', 'nim', 'email', 'prodi', 'angkatan', 'status']
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} mahasiswa ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data mahasiswa")
                    logger.warning(f"[{user_id}] ✗ query_mahasiswa gagal")
        
        elif action == "query_kelompok":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_kelompok (daftar kelompok)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()
                prodi_id = dosen_context.get("prodi_id")
                kategori_pa_id = dosen_context.get("kategori_pa")
                angkatan_id = dosen_context.get("angkatan")
                include_relations = ("pembimbing" in prompt_lower and "penguji" in prompt_lower)

                logger.info(f"[{user_id}] 📍 Query kelompok dengan context: prodi_id={prodi_id}, kategori_pa_id={kategori_pa_id}, angkatan_id={angkatan_id}")
                result = get_kelompok_with_anggota_by_context(
                    prodi_id=prodi_id,
                    kategori_pa_id=kategori_pa_id,
                    angkatan_id=angkatan_id,
                )
                
                if result.get("status") == "success":
                    kelompok_rows = result.get("data", [])

                    if include_relations:
                        pb_result = get_pembimbing_assignments_by_context(prodi_id, kategori_pa_id, angkatan_id)
                        pj_result = get_penguji_assignments_by_context(prodi_id, kategori_pa_id, angkatan_id)

                        pb_map = {}
                        if pb_result.get("status") == "success":
                            for item in pb_result.get("data", []):
                                nomor = str(item.get("nomor_kelompok"))
                                pb_map.setdefault(nomor, [])
                                nama = item.get("dosen_nama")
                                if nama and nama not in pb_map[nomor]:
                                    pb_map[nomor].append(nama)

                        pj_map = {}
                        if pj_result.get("status") == "success":
                            for item in pj_result.get("data", []):
                                nomor = str(item.get("nomor_kelompok"))
                                pj_map.setdefault(nomor, [])
                                nama = item.get("dosen_nama")
                                if nama and nama not in pj_map[nomor]:
                                    pj_map[nomor].append(nama)

                        for row in kelompok_rows:
                            nomor = str(row.get("nomor_kelompok"))
                            row["pembimbing_nama"] = pb_map.get(nomor, [])
                            row["penguji_nama"] = pj_map.get(nomor, [])

                    state["result"] = format_kelompok_detailed_result(kelompok_rows, include_relations=include_relations)
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} kelompok ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data kelompok")
                    logger.warning(f"[{user_id}] ✗ query_kelompok gagal")

        elif action == "query_anggota_kelompok":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_anggota_kelompok (anggota kelompok)")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                nomor_kelompok = plan.get("nomor_kelompok")

                if not nomor_kelompok:
                    match = re.search(r"(?:siapa\s+|lihat\s+|tampilkan\s+|cek\s+)?anggot?a\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt.lower())
                    if match:
                        nomor_kelompok = match.group(1)

                if not nomor_kelompok:
                    state["result"] = "<p>Nomor kelompok tidak ditemukan. Contoh: <strong>siapa anggota kelompok 3</strong></p>"
                    logger.warning(f"[{user_id}] ✗ nomor kelompok tidak ditemukan di prompt")
                else:
                    result = get_anggota_kelompok_by_nomor(
                        nomor_kelompok=nomor_kelompok,
                        prodi_id=dosen_context.get("prodi_id"),
                        kategori_pa_id=dosen_context.get("kategori_pa"),
                        angkatan_id=dosen_context.get("angkatan"),
                    )
                    state["result"] = format_anggota_kelompok_result(result)
                    if result.get("status") == "success":
                        logger.info(f"[{user_id}] ✓ query_anggota_kelompok nomor={nomor_kelompok} total={result.get('total', 0)}")
                    else:
                        logger.warning(f"[{user_id}] ✗ query_anggota_kelompok gagal")
        
        elif action == "query_matakuliah":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_matakuliah (daftar mata kuliah)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prodi_id = dosen_context.get("prodi_id")
                logger.info(f"[{user_id}] 📍 Query matakuliah dengan context: prodi_id={prodi_id}")
                # Ambil semua matakuliah kemudian filter berdasarkan prodi_id
                result = get_matakuliah_list()
                if result.get("status") == "success" and prodi_id:
                    result['data'] = [m for m in result['data'] if m.get('prodi_id') == prodi_id]
                
                if result.get("status") == "success" and result['data']:
                    state["result"] = format_query_result(
                        "matakuliah",
                        result['data'],
                        fields=['kode_mk', 'nama_matkul', 'sks', 'semester', 'prodi_id']
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} mata kuliah ditemukan")
                elif result.get("status") == "success":
                    state["result"] = f"Tidak ada mata kuliah untuk prodi_id={prodi_id}"
                    logger.info(f"[{user_id}] ℹ️ Tidak ada matakuliah ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data matakuliah")
                    logger.warning(f"[{user_id}] ✗ query_matakuliah gagal")
        
        elif action == "query_prodi":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_prodi (daftar program studi)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prodi_id = dosen_context.get("prodi_id")
                logger.info(f"[{user_id}] 📍 Query prodi dengan context: prodi_id={prodi_id}")
                result = get_prodi_list()
                if result.get("status") == "success" and prodi_id:
                    result['data'] = [p for p in result['data'] if p.get('id') == prodi_id]
                
                if result.get("status") == "success" and result['data']:
                    state["result"] = format_query_result(
                        "program studi",
                        result['data'],
                        fields=['id', 'nama', 'maks_project']
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} prodi ditemukan")
                elif result.get("status") == "success":
                    state["result"] = f"Tidak ada prodi dengan id={prodi_id}"
                    logger.info(f"[{user_id}] ℹ️ Prodi tidak ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data prodi")
                    logger.warning(f"[{user_id}] ✗ query_prodi gagal")
        
        elif action == "query_nilai":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_nilai (daftar nilai)")
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prodi_id = dosen_context.get("prodi_id")
                logger.info(f"[{user_id}] 📍 Query nilai akhir dengan context: prodi_id={prodi_id}")
                result = get_nilai_akhir_by_dosen_context(prodi_id=prodi_id)

                if result.get("status") == "success":
                    state["result"] = format_query_result(
                        "nilai akhir mahasiswa",
                        result["data"],
                        fields=["nim", "nama", "nilai_akhir", "kelompok_id", "prodi_id"]
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} nilai akhir ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data nilai akhir")
                    logger.warning(f"[{user_id}] ✗ query_nilai gagal")

        elif action == "query_dosen_role":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_dosen_role (role dosen)")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                result = get_dosen_roles_by_dosen_context(
                    user_id=dosen_context.get("user_id"),
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa")
                )

                if result.get("status") == "success":
                    state["result"] = format_query_result(
                        "role dosen",
                        result["data"],
                        fields=["user_id", "role", "kategori_pa", "prodi", "tahun_masuk", "tahun_ajaran", "status"]
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} role dosen ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data role dosen")
                    logger.warning(f"[{user_id}] ✗ query_dosen_role gagal")

        elif action == "query_jadwal":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_jadwal (jadwal sidang/bimbingan)")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                result = get_jadwal_by_dosen_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa")
                )

                if result.get("status") == "success":
                    state["result"] = format_query_result(
                        "jadwal",
                        result["data"],
                        fields=["nomor_kelompok", "waktu_mulai", "waktu_selesai", "ruangan", "prodi_id", "kategori_pa_id"]
                    )
                    logger.info(f"[{user_id}] ✓ {len(result['data'])} jadwal ditemukan")
                else:
                    state["result"] = result.get("message", "Error mengambil data jadwal")
                    logger.warning(f"[{user_id}] ✗ query_jadwal gagal")

        elif action == "query_kategori_pa":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_kategori_pa (daftar kategori PA)")
            result = get_kategori_pa_list()

            if result.get("status") == "success":
                state["result"] = format_query_result(
                    "kategori PA",
                    result["data"],
                    fields=["id", "kategori_pa"]
                )
                logger.info(f"[{user_id}] ✓ {len(result['data'])} kategori PA ditemukan")
            else:
                state["result"] = result.get("message", "Error mengambil data kategori PA")
                logger.warning(f"[{user_id}] ✗ query_kategori_pa gagal")

        elif action == "query_roles":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_roles (daftar role dan relasi dosen_roles)")
            result = get_roles_list()

            if result.get("status") == "success":
                state["result"] = format_query_result(
                    "roles",
                    result["data"],
                    fields=["id", "role_name", "total_dosen_roles", "active_dosen_roles"]
                )
                logger.info(f"[{user_id}] ✓ {len(result['data'])} role ditemukan")
            else:
                state["result"] = result.get("message", "Error mengambil data roles")
                logger.warning(f"[{user_id}] ✗ query_roles gagal")

        elif action == "query_tahun_ajaran":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_tahun_ajaran (daftar tahun ajaran)")
            result = get_tahun_ajaran_list()

            if result.get("status") == "success":
                state["result"] = format_query_result(
                    "tahun ajaran",
                    result["data"],
                    fields=["id", "tahun_mulai", "tahun_selesai", "status"]
                )
                logger.info(f"[{user_id}] ✓ {len(result['data'])} tahun ajaran ditemukan")
            else:
                state["result"] = result.get("message", "Error mengambil data tahun ajaran")
                logger.warning(f"[{user_id}] ✗ query_tahun_ajaran gagal")

        elif action == "query_ruangan":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_ruangan (daftar ruangan)")
            result = get_ruangan_list()

            if result.get("status") == "success":
                state["result"] = format_query_result(
                    "ruangan",
                    result["data"],
                    fields=["id", "ruangan"]
                )
                logger.info(f"[{user_id}] ✓ {len(result['data'])} ruangan ditemukan")
            else:
                state["result"] = result.get("message", "Error mengambil data ruangan")
                logger.warning(f"[{user_id}] ✗ query_ruangan gagal")
        
        elif action == "query_pembimbing":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_pembimbing")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()
                prodi_id = dosen_context.get("prodi_id")
                kategori_pa_id = dosen_context.get("kategori_pa")
                angkatan_id = dosen_context.get("angkatan")

                nomor_match = re.search(r"pembimbing\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)
                if not nomor_match:
                    nomor_match = re.search(r"siapa\s+pembimbing\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)

                nomor_generik_match = re.search(r"kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)
                ask_both = ("pembimbing" in prompt_lower) and ("penguji" in prompt_lower) and bool(nomor_generik_match)

                nama_dosen_match = re.search(r"(?:nama\s+dosen|dosen)\s+([a-zA-Z .'-]{3,})", prompt, re.IGNORECASE)

                if ask_both:
                    nomor_kelompok = nomor_generik_match.group(1)
                    result_pb = get_pembimbing_of_kelompok(nomor_kelompok, prodi_id, kategori_pa_id, angkatan_id)
                    result_pj = get_penguji_of_kelompok(nomor_kelompok, prodi_id, kategori_pa_id, angkatan_id)

                    html_parts = [f"<h2>Pembimbing dan Penguji Kelompok {nomor_kelompok}</h2>"]

                    if result_pb.get("status") == "success":
                        html_parts.append(format_query_result(
                            f"pembimbing kelompok {nomor_kelompok}",
                            result_pb.get("data", []),
                            fields=["dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        ))
                    else:
                        html_parts.append(f"<p>{_format_cell_value(result_pb.get('message', 'Belum ada pembimbing'))}</p>")

                    if result_pj.get("status") == "success":
                        html_parts.append(format_query_result(
                            f"penguji kelompok {nomor_kelompok}",
                            result_pj.get("data", []),
                            fields=["dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        ))
                    else:
                        html_parts.append(f"<p>{_format_cell_value(result_pj.get('message', 'Belum ada penguji'))}</p>")

                    state["result"] = "\n".join(html_parts)

                elif "belum" in prompt_lower and "pembimbing" in prompt_lower and "kelompok" in prompt_lower:
                    result = get_kelompok_without_pembimbing_by_context(prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            "kelompok tanpa pembimbing",
                            result.get("data", []),
                            fields=["id", "nomor_kelompok", "status"],
                        )
                    else:
                        state["result"] = result.get("message", "Tidak ada kelompok tanpa pembimbing")

                elif "2 pembimbing" in prompt_lower or "dua pembimbing" in prompt_lower:
                    result = get_kelompok_with_two_pembimbing_by_context(prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            "kelompok dengan 2 pembimbing",
                            result.get("data", []),
                            fields=["id", "nomor_kelompok", "status", "pembimbing_count"],
                        )
                    else:
                        state["result"] = result.get("message", "Tidak ada kelompok dengan 2 pembimbing")

                elif "1 pembimbing" in prompt_lower or "satu pembimbing" in prompt_lower:
                    result = get_kelompok_with_one_pembimbing_by_context(prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            "kelompok dengan 1 pembimbing",
                            result.get("data", []),
                            fields=["id", "nomor_kelompok", "status", "pembimbing_count"],
                        )
                    else:
                        state["result"] = result.get("message", "Tidak ada kelompok dengan 1 pembimbing")

                elif nomor_match:
                    nomor_kelompok = nomor_match.group(1)
                    result = get_pembimbing_of_kelompok(nomor_kelompok, prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            f"pembimbing kelompok {nomor_kelompok}",
                            result.get("data", []),
                            fields=["dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        )
                    else:
                        state["result"] = result.get("message", f"Belum ada pembimbing untuk kelompok {nomor_kelompok}")

                elif nama_dosen_match and ("berdasarkan nama" in prompt_lower or "nama dosen" in prompt_lower):
                    nama_dosen = nama_dosen_match.group(1).strip()
                    result = get_pembimbing_by_dosen_name(nama_dosen, prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            f"pembimbing berdasarkan dosen {nama_dosen}",
                            result.get("data", []),
                            fields=["nomor_kelompok", "dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        )
                    else:
                        state["result"] = result.get("message", f"Tidak ada data pembimbing untuk dosen {nama_dosen}")

                else:
                    result = get_pembimbing_assignments_by_context(prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            "pembimbing kelompok",
                            result.get("data", []),
                            fields=["nomor_kelompok", "dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        )
                    else:
                        state["result"] = result.get("message", "Belum ada assignment pembimbing pada konteks ini")

        elif action == "query_penguji":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_penguji")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()
                prodi_id = dosen_context.get("prodi_id")
                kategori_pa_id = dosen_context.get("kategori_pa")
                angkatan_id = dosen_context.get("angkatan")

                nomor_match = re.search(r"penguji\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)
                if not nomor_match:
                    nomor_match = re.search(r"siapa\s+penguji\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)

                if nomor_match:
                    nomor_kelompok = nomor_match.group(1)
                    result = get_penguji_of_kelompok(nomor_kelompok, prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            f"penguji kelompok {nomor_kelompok}",
                            result.get("data", []),
                            fields=["dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        )
                    else:
                        state["result"] = result.get("message", f"Belum ada penguji untuk kelompok {nomor_kelompok}")
                else:
                    result = get_penguji_assignments_by_context(prodi_id, kategori_pa_id, angkatan_id)
                    if result.get("status") == "success":
                        state["result"] = format_query_result(
                            "penguji kelompok",
                            result.get("data", []),
                            fields=["nomor_kelompok", "dosen_nama", "dosen_email", "jabatan_akademik_desc"],
                        )
                    else:
                        state["result"] = result.get("message", "Belum ada assignment penguji pada konteks ini")

        elif action == "check_pembimbing":
            logger.info(f"[{user_id}] ⚙️  TOOLS: check_pembimbing")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                result = check_existing_pembimbing_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                )

                if result.get("status") == "success":
                    state["result"] = (
                        f"<h2>Status Pembimbing Kelompok</h2>"
                        f"<ul>"
                        f"<li>Sudah ada pembimbing: <strong>{'Ya' if result.get('exists') else 'Belum'}</strong></li>"
                        f"<li>Total assignment pembimbing: <strong>{_format_cell_value(result.get('total_assignments'))}</strong></li>"
                        f"<li>Total kelompok: <strong>{_format_cell_value(result.get('total_kelompok'))}</strong></li>"
                        f"<li>Kelompok sudah punya pembimbing: <strong>{_format_cell_value(result.get('kelompok_with_pembimbing'))}</strong></li>"
                        f"<li>Kelompok belum punya pembimbing: <strong>{_format_cell_value(result.get('kelompok_without_pembimbing'))}</strong></li>"
                        f"</ul>"
                    )
                else:
                    state["result"] = result.get("message", "Gagal mengecek status pembimbing")

        elif action == "check_penguji":
            logger.info(f"[{user_id}] ⚙️  TOOLS: check_penguji")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                result = check_existing_penguji_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                )

                if result.get("status") == "success":
                    state["result"] = (
                        f"<h2>Status Penguji Kelompok</h2>"
                        f"<ul>"
                        f"<li>Sudah ada penguji: <strong>{'Ya' if result.get('exists') else 'Belum'}</strong></li>"
                        f"<li>Total assignment penguji: <strong>{_format_cell_value(result.get('total_assignments'))}</strong></li>"
                        f"<li>Total kelompok: <strong>{_format_cell_value(result.get('total_kelompok'))}</strong></li>"
                        f"<li>Kelompok sudah punya penguji: <strong>{_format_cell_value(result.get('kelompok_with_penguji'))}</strong></li>"
                        f"<li>Kelompok belum punya penguji: <strong>{_format_cell_value(result.get('kelompok_without_penguji'))}</strong></li>"
                        f"</ul>"
                    )
                else:
                    state["result"] = result.get("message", "Gagal mengecek status penguji")

        elif action == "generate_pembimbing":
            logger.info(f"[{user_id}] ⚙️  TOOLS: generate_pembimbing")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()

                one_only = "1 pembimbing" in prompt_lower or "satu pembimbing" in prompt_lower
                max_per_group = 1 if one_only else 2

                result = generate_pembimbing_assignments_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                    min_per_group=1,
                    max_per_group=max_per_group,
                    replace_existing=True,
                    persist=False,
                )
                state["result"] = format_generate_pembimbing_result(result)
                if result.get("status") == "success":
                    existing_pembimbing_check = check_existing_pembimbing_by_context(
                        prodi_id=dosen_context.get("prodi_id"),
                        kategori_pa_id=dosen_context.get("kategori_pa"),
                        angkatan_id=dosen_context.get("angkatan"),
                    )

                    state["pembimbing_payload"] = {
                        "summary": result.get("summary", {}),
                        "groups": result.get("groups", []),
                        "dosen_loads": result.get("dosen_loads", []),
                        "filters": result.get("filters", {}),
                    }
                    state["pembimbing_meta"] = {
                        "prodi_id": dosen_context.get("prodi_id"),
                        "kategori_pa_id": dosen_context.get("kategori_pa"),
                        "angkatan_id": dosen_context.get("angkatan"),
                        "existing_assignments_count": existing_pembimbing_check.get("total_assignments", 0)
                        if existing_pembimbing_check.get("status") == "success"
                        else 0,
                        "prompt": prompt,
                    }
                    logger.info(f"[{user_id}] ✓ generate_pembimbing preview siap disimpan")

        elif action == "generate_penguji":
            logger.info(f"[{user_id}] ⚙️  TOOLS: generate_penguji")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()

                result = generate_penguji_assignments_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                    min_per_group=2,
                    max_per_group=2,
                    replace_existing=True,
                    persist=False,
                )

                state["result"] = format_generate_penguji_result(result)
                if result.get("status") == "success":
                    existing_penguji_check = check_existing_penguji_by_context(
                        prodi_id=dosen_context.get("prodi_id"),
                        kategori_pa_id=dosen_context.get("kategori_pa"),
                        angkatan_id=dosen_context.get("angkatan"),
                    )

                    state["penguji_payload"] = {
                        "summary": result.get("summary", {}),
                        "groups": result.get("groups", []),
                        "dosen_loads": result.get("dosen_loads", []),
                        "filters": result.get("filters", {}),
                    }
                    state["penguji_meta"] = {
                        "prodi_id": dosen_context.get("prodi_id"),
                        "kategori_pa_id": dosen_context.get("kategori_pa"),
                        "angkatan_id": dosen_context.get("angkatan"),
                        "existing_assignments_count": existing_penguji_check.get("total_assignments", 0)
                        if existing_penguji_check.get("status") == "success"
                        else 0,
                        "prompt": prompt,
                    }
                    logger.info(f"[{user_id}] ✓ generate_penguji preview siap disimpan")

        elif action == "create_group":
            logger.info(f"[{user_id}] ⚙️  TOOLS: create_group (buat kelompok)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                
                # Check if user intent is to RECREATE (acak kembali, buat ulang, ganti)
                recreate_keywords = ["acak kembali", "acak ulang", "buat ulang", "ganti"]
                is_recreate_intent = any(kw in prompt.lower() for kw in recreate_keywords)
                
                # Cek existing groups pada konteks ini
                existing_check = check_existing_kelompok_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                )
                
                if is_recreate_intent and existing_check.get("status") == "success" and existing_check.get("total", 0) > 0:
                    logger.info(f"[{user_id}] 🔄 RECREATE INTENT DETECTED: {existing_check.get('total')} existing groups found")
                    
                    # Store prompt as escaped JSON for data attribute
                    escaped_prompt = html.escape(prompt)
                    
                    confirmation_html = f"""
<div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:8px; padding:16px; margin-bottom:16px;">
  <h3 style="margin-top:0; color:#b45309;">⚠️ Konfirmasi: Kelompok Sudah Ada</h3>
  <p>Ditemukan <strong>{existing_check.get('total', 0)}</strong> kelompok pada konteks ini.</p>
  <p>Untuk membuat kelompok baru, harus menghapus data kelompok yang ada terlebih dahulu.</p>
  <p style="margin-bottom:12px;"><strong>Pilih aksi berikut:</strong></p>
  <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-warning confirm-recreate-groups" style="padding:8px 16px;" data-recreate-prompt="{escaped_prompt}" onclick="if(window.__confirmRecreateGroupsFromInline){{window.__confirmRecreateGroupsFromInline(event);}}"> 
      <i class="fas fa-sync"></i> Hapus Lama & Buat Baru
    </button>
    <button type="button" class="btn btn-secondary cancel-recreate" style="padding:8px 16px;">
      Batal
    </button>
  </div>
</div>
"""
                    
                    state["result"] = confirmation_html
                    state["recreate_confirmation_pending"] = True
                    state["recreate_context"] = {
                        "prodi_id": dosen_context.get("prodi_id"),
                        "kategori_pa_id": dosen_context.get("kategori_pa"),
                        "angkatan_id": dosen_context.get("angkatan"),
                        "prompt": prompt,
                    }
                    logger.info(f"[{user_id}] ✓ Konfirmasi recreate ditampilkan, menunggu user confirmation")
                    return state
                
                grouping_result = create_group(
                    prompt=prompt,
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                    exclude_existing=True,
                )
                state["result"] = format_grouping_result(grouping_result)
                if grouping_result.get("status") == "success":
                    existing_check_after_create = {
                        "status": "success",
                        "total": 0
                    }

                    state["grouping_payload"] = {
                        "instruction": grouping_result.get("instruction", {}),
                        "summary": grouping_result.get("summary", {}),
                        "groups": grouping_result.get("groups", []),
                    }
                    state["grouping_meta"] = {
                        "prodi_id": dosen_context.get("prodi_id"),
                        "kategori_pa_id": dosen_context.get("kategori_pa"),
                        "angkatan_id": dosen_context.get("angkatan"),
                        "existing_groups_count": existing_check_after_create.get("total", 0) if existing_check_after_create.get("status") == "success" else 0,
                    }
                    logger.info(f"[{user_id}] ✓ {grouping_result.get('summary', {}).get('total_groups', 0)} kelompok direkomendasikan")
                else:
                    logger.warning(f"[{user_id}] ✗ create_group gagal: {grouping_result.get('message')}")

        elif action == "check_kelompok":
            logger.info(f"[{user_id}] ⚙️  TOOLS: check_kelompok (cek kelompok existing)")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                check_result = check_existing_kelompok_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                )
                state["result"] = format_existing_check_result(check_result)
                if check_result.get("status") == "success":
                    logger.info(f"[{user_id}] ✓ check_kelompok total={check_result.get('total', 0)}")
                else:
                    logger.warning(f"[{user_id}] ✗ check_kelompok gagal")

        elif action == "delete_kelompok":
            logger.info(f"[{user_id}] ⚙️  TOOLS: delete_kelompok (hapus kelompok existing)")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                delete_result = delete_kelompok_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                )
                if delete_result.get("status") == "success":
                    state["result"] = (
                        f"<p>{html.escape(delete_result.get('message', 'Berhasil menghapus kelompok.'))}</p>"
                        "<p>Silakan kirim instruksi generate kelompok baru lalu simpan ke database.</p>"
                    )
                    logger.info(f"[{user_id}] ✓ delete_kelompok berhasil")
                elif delete_result.get("status") == "empty":
                    state["result"] = f"<p>{html.escape(delete_result.get('message', 'Tidak ada data untuk dihapus.'))}</p>"
                    logger.info(f"[{user_id}] ℹ️ delete_kelompok tidak ada data")
                else:
                    state["result"] = f"<p>{html.escape(delete_result.get('message', 'Gagal menghapus kelompok.'))}</p>"
                    logger.warning(f"[{user_id}] ✗ delete_kelompok gagal")
        
        elif action == "generate_excel":
            logger.info(f"[{user_id}] ⚙️  TOOLS: generate_excel (buat spreadsheet)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                # Generate Excel dengan konteks yang ada
                result = generate_excel_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    tahun_ajaran_id=dosen_context.get("tahun_ajaran"),
                    tahun_masuk=dosen_context.get("angkatan")
                )
                
                if result.get("success"):
                    file_path = result.get("file_path")
                    filename = result.get("filename")
                    row_count = result.get("row_count", 0)
                    
                    # Build HTML dengan tombol download
                    state["result"] = f"""
                    <h2>✅ Excel berhasil dibuat</h2>
                    <ul>
                        <li><strong>Jumlah Kelompok:</strong> {row_count}</li>
                        <li><strong>File:</strong> {html.escape(filename)}</li>
                        <li><strong>Status:</strong> Siap diunduh</li>
                    </ul>
                    <button class="btn btn-primary download-excel-btn" onclick="downloadExcel('{html.escape(filename)}')">
                        <i class="fa fa-download"></i> Unduh Excel
                    </button>
                    """
                    
                    # Store file path dan filename untuk diakses oleh Laravel
                    state["excel_file_path"] = file_path
                    state["excel_filename"] = filename
                    logger.info(f"[{user_id}] ✓ generate_excel success: {row_count} groups, file: {filename}")
                else:
                    error_msg = result.get("error", "Unknown error")
                    state["result"] = f"<p>❌ Gagal membuat Excel: {html.escape(error_msg)}</p>"
                    logger.warning(f"[{user_id}] ✗ generate_excel gagal: {error_msg}")
        
        else:
            logger.info(f"[{user_id}] 📄 CHAT: (tanpa tools / action tidak dikenali)")
            state["result"] = None

        return state
    
    except Exception as e:
        user_id = state.get("user_id", "unknown")
        logger.error(f"[{user_id}] ❌ ERROR IN EXECUTOR_NODE")
        logger.error(f"Traceback:\n{traceback.format_exc()}")
        state["result"] = f"Error: {str(e)}"
        return state
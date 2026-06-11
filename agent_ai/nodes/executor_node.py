import json
import logging
import traceback
import re
import math
from datetime import date, datetime
from decimal import Decimal
import html
from tools.grouping import create_group
from tools.grouping_by_grades import create_group_by_grades, calculate_student_average_grades
from tools.grouping_hybrid import create_group_hybrid
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
    _extract_dosen_constraints_from_prompt,
)
from tools.penguji_tools import (
    check_existing_penguji_by_context,
    generate_penguji_assignments_by_context,
    get_penguji_assignments_by_context,
    get_penguji_of_kelompok,
)
from tools.dosen_role_tools import get_dosen_roles_by_dosen_context
from tools.jadwal_seminar import get_jadwal_by_dosen_context, check_existing_jadwal_by_context
from tools.nilai_mahasiswa_tools import (
    get_nilai_akhir_by_dosen_context,
    get_nilai_permatkul_by_mahasiswa,
    get_nilai_persemester_by_mahasiswa,
    get_combined_analisis_nilai,
    get_nilai_permatkul_by_dosen_context,
    get_nilai_persemester_by_dosen_context,
    get_combined_analisis_by_dosen_context
)
from tools.academic_tools import get_kategori_pa_list, get_tahun_ajaran_list, get_ruangan_list
from tools.roles_tools import get_roles_list
from tools.excel_tools import generate_excel_by_context
from tools.jadwal_seminar import JadwalSeminarTools
from nodes.grouping_form_handler import GroupingFormHandler

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

try:
    from RAG import retriever as rag_retriever
except Exception:
    rag_retriever = None

EXECUTABLE_ACTIONS = {
    "query_dosen",
    "query_mahasiswa",
    "query_kelompok",
    "query_anggota_kelompok",
    "query_matakuliah",
    "query_prodi",
    "query_nilai",
    "query_dosen_role",
    "query_jadwal",
    "query_kategori_pa",
    "query_roles",
    "query_tahun_ajaran",
    "query_ruangan",
    "query_rag",
    "query_pembimbing",
    "query_penguji",
    "check_pembimbing",
    "check_penguji",
    "generate_pembimbing",
    "generate_penguji",
    "clarify_group_requirements",
    "process_grouping_form",
    "create_group",
    "create_group_hybrid",
    "create_group_by_grades",
    "check_kelompok",
    "delete_kelompok",
    "generate_excel",
    "generate_jadwal_seminar",
    "save_jadwal",
    "query_jadwal_kelompok",
}


def _infer_action_from_prompt(prompt_lower: str):
    infer_rules = [
        ("query_rag", ["pedoman", "panduan", "sop", "aturan", "dokumen", "manual", "pedoman pa", "pedoman_pa"]),
        ("query_jadwal_kelompok", ["kapan kelompok", "jadwal kelompok", "kelompok maju", "maju kapan"]),
        ("generate_jadwal_seminar", ["jadwal seminar", "buat jadwal", "jadwal presentasi", "schedule seminar"]),
        ("query_anggota_kelompok", ["anggota kelompok", "siapa anggota kelompok"]),
        ("generate_pembimbing", ["generate pembimbing", "assign pembimbing", "buat pembimbing"]),
        ("generate_penguji", ["generate penguji", "assign penguji", "buat penguji"]),
        ("create_group_hybrid", ["harus satu", "satu kelompok", "satu grup"]),
        ("create_group_by_grades", ["berdasarkan nilai", "grouping nilai", "grade-based"]),
        ("create_group", ["buat kelompok", "bagi kelompok", "kelompokkan", "acak ulang", "buat ulang"]),
        ("check_kelompok", ["cek kelompok", "status kelompok", "sudah ada kelompok"]),
        ("delete_kelompok", ["hapus kelompok", "delete kelompok", "kosongkan kelompok"]),
        ("query_pembimbing", ["dosen pembimbing", "daftar pembimbing", "siapa pembimbing"]),
        ("query_penguji", ["dosen penguji", "daftar penguji", "siapa penguji"]),
        ("query_dosen", ["daftar dosen", "list dosen", "siapa dosen"]),
        ("query_mahasiswa", ["daftar mahasiswa", "list mahasiswa", "nim", "mahasiswa"]),
        ("query_kelompok", ["daftar kelompok", "list kelompok", "data kelompok"]),
        ("query_nilai", ["nilai", "grade", "ipk"]),
        ("query_jadwal", ["jadwal", "schedule"]),
        ("generate_excel", ["excel", "spreadsheet", "export excel"]),
    ]

    for action, keywords in infer_rules:
        if any(keyword in prompt_lower for keyword in keywords):
            return action

    return None


def _resolve_action(plan: dict, prompt: str):
    raw_action = (plan or {}).get("action")
    if raw_action in EXECUTABLE_ACTIONS:
        return raw_action, "plan.action"

    alias_map = {
        "query_group": "query_kelompok",
        "query_groups": "query_kelompok",
        "query_students": "query_mahasiswa",
        "query_lecturer": "query_dosen",
        "generate_group": "create_group",
        "grouping": "create_group",
    }

    if raw_action in alias_map and alias_map[raw_action] in EXECUTABLE_ACTIONS:
        return alias_map[raw_action], "plan.alias"

    alternatives = (plan or {}).get("alternatives") or (plan or {}).get("candidates") or []
    for candidate in alternatives:
        action = candidate.get("action") if isinstance(candidate, dict) else candidate
        if action in EXECUTABLE_ACTIONS:
            return action, "plan.candidate"

    prompt_lower = (prompt or "").lower()
    inferred = _infer_action_from_prompt(prompt_lower)
    if inferred in EXECUTABLE_ACTIONS:
        return inferred, "prompt.inference"

    return raw_action, "unknown"


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

        # ── Restore jadwal_meta / jadwal_entries dari request jika state kosong ──
        # Frontend mengirim jadwal_meta & jadwal_entries bersama setiap request,
        # sehingga meskipun Python state di-reset antar request, data preview
        # tetap tersedia untuk aksi save_jadwal.
        request_data = state.get("request_data") or {}
        if request_data.get("jadwal_meta") and not state.get("jadwal_meta"):
            state["jadwal_meta"] = request_data["jadwal_meta"]
            logger.info(f"[{user_id}] 🔄 Restored jadwal_meta from request_data")
        if request_data.get("jadwal_entries") and not state.get("jadwal_entries"):
            state["jadwal_entries"] = request_data["jadwal_entries"]
            logger.info(f"[{user_id}] 🔄 Restored jadwal_entries from request_data ({len(request_data['jadwal_entries'])} entries)")
        # If jadwal_meta is present (restored or existing) and stage is unknown,
        # treat it as a resumed preview so save_jadwal can proceed.
        if state.get("jadwal_meta") and not state.get("jadwal_stage"):
            state["jadwal_stage"] = "preview"

        plan = state.get("plan", {})
        prompt = state.get("messages", [{}])[-1].get("content", "")
        prompt_lower = prompt.lower()
        plan_params = plan.get("params", {}) if isinstance(plan, dict) else {}
        action, action_source = _resolve_action(plan, prompt)
        state["execution_meta"] = {
            "action_source": action_source,
            "resolved_action": action,
            "planner_action": plan.get("action") if isinstance(plan, dict) else None,
            "planner_confidence": plan.get("confidence") if isinstance(plan, dict) else None,
        }
        if action_source != "plan.action":
            logger.info(f"[{user_id}] ⚙️  ACTION RESOLVER: {plan.get('action') if isinstance(plan, dict) else None} -> {action} ({action_source})")
        
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
                prodi_id = dosen_context.get("prodi_id")
                angkatan_id = dosen_context.get("angkatan")
                logger.info(f"[{user_id}] 📍 Query mahasiswa dengan context: prodi_id={prodi_id}, angkatan_id={angkatan_id}")

                ask_without_group = bool(plan_params.get("ask_without_group")) or any(
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
                prodi_id = dosen_context.get("prodi_id")
                kategori_pa_id = dosen_context.get("kategori_pa")
                angkatan_id = dosen_context.get("angkatan")
                include_relations = bool(plan_params.get("include_relations")) or ("pembimbing" in prompt_lower and "penguji" in prompt_lower)

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
                nomor_kelompok = plan.get("nomor_kelompok") or plan_params.get("nomor_kelompok")

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

        elif action == "query_jadwal_kelompok":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_jadwal_kelompok (jadwal kelompok)")

            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                # Extract nomor kelompok dari prompt
                # Pattern: "kapan kelompok 1", "jadwal kelompok 2", "kelompok 1 maju"
                nomor_match = re.search(r"kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)
                nomor_kelompok = nomor_match.group(1) if nomor_match else None

                if not nomor_kelompok:
                    state["result"] = "<p>Nomor kelompok tidak ditemukan. Contoh: <strong>kapan kelompok 1 maju seminar</strong></p>"
                    logger.warning(f"[{user_id}] ✗ nomor kelompok tidak ditemukan di prompt")
                else:
                    result = JadwalSeminarTools.get_jadwal_kelompok_detail(
                        kelompok_nomor=int(nomor_kelompok),
                        dosen_context=[dosen_context]
                    )
                    state["result"] = result.get("message", "Gagal mengambil detail jadwal kelompok")
                    if result.get("status") == "success":
                        logger.info(f"[{user_id}] ✓ query_jadwal_kelompok nomor={nomor_kelompok}")
                    else:
                        logger.warning(f"[{user_id}] ✗ query_jadwal_kelompok gagal: {result.get('message')}")

        elif action == "query_rag":
            logger.info(f"[{user_id}] ⚙️  TOOLS: query_rag (retrieval from document store)")
            # Use simple keyword retriever. If retriever not available, return helpful message.
            if rag_retriever is None:
                state["result"] = (
                    "<p>RAG module belum tersedia. Untuk mengaktifkan RAG, pasang dependensi dan indeks dokumen:</p>"
                    "<ol>"
                    "<li>pip install PyPDF2</li>"
                    "<li>jalankan Python: from RAG import retriever; retriever.index_documents()</li>"
                    "</ol>"
                )
            else:
                try:
                    hits = rag_retriever.query(prompt, top_k=3)
                except ImportError as e:
                    state["result"] = f"<p>Dependency error: {html.escape(str(e))}</p>"
                    return state
                except Exception as e:
                    logger.exception(f"[{user_id}] ✗ RAG query failed: {e}")
                    state["result"] = "<p>Terjadi kesalahan saat mengambil dokumen. Periksa log server.</p>"
                    return state

                if not hits:
                    state["result"] = "<p>Tidak ditemukan informasi relevan pada dokumen yang terindeks.</p>"
                else:
                    # Format simple list of hits with excerpt
                    html_result = f"<h2>Hasil Pencarian Dokumen ({len(hits)} terbaik)</h2>"
                    for h in hits:
                        src = html.escape(h.get("source") or "")
                        excerpt = html.escape((h.get("chunk") or "")[0:800])
                        score = h.get("score", 0)
                        html_result += f"<div style='margin-bottom:12px; padding:8px; border:1px solid #eee; border-radius:6px;'>"
                        html_result += f"<h4 style='margin:0 0 6px 0;'>Sumber: {src} — skor: {score}</h4>"
                        html_result += f"<p style='margin:0; white-space:pre-wrap;'>{excerpt}</p>"
                        html_result += "</div>"
                    html_result += "<p><em>Catatan: ini adalah pencarian kata kunci sederhana. Untuk hasil yang lebih baik, pasang embeddings dan lakukan indexing menggunakan retriever berbasis vektor.</em></p>"
                    state["result"] = html_result
        
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
                prompt = state.get("messages", [{}])[-1].get("content", "")
                prompt_lower = prompt.lower()
                prodi_id = dosen_context.get("prodi_id")
                
                # Extract nama mahasiswa dari prompt
                # Pattern: "nilai [nama mahasiswa]"
                nama_match = re.search(r"nilai\s+([A-Za-z\s]+?)(?:\s+semester|\s+ta|\s+tahun|$)", prompt_lower)
                nama = None
                if nama_match:
                    nama = nama_match.group(1).strip()
                    if nama and len(nama) > 3:  # Minimal 4 karakter untuk nama valid
                        nama = re.sub(r"\s+", " ", nama)  # Clean multiple spaces
                    else:
                        nama = None
                
                # Extract semester dari prompt
                semester = None
                semester_match = re.search(r"semester\s+(\d+)", prompt_lower)
                if semester_match:
                    semester = int(semester_match.group(1))
                
                logger.info(f"[{user_id}] 📍 Query nilai dengan context: prodi_id={prodi_id}, nama={nama}, semester={semester}")
                
                # Determine query type based on what user asked for
                if nama:
                    # Query by mahasiswa name
                    if "semester" in prompt_lower or semester:
                        # Get persemester data for specific student
                        result = get_nilai_persemester_by_mahasiswa(nama=nama, prodi_id=prodi_id)
                        if result.get("status") == "success":
                            mahasiswa_list = result.get("mahasiswa_list", [])
                            if mahasiswa_list:
                                # Format response with per-semester data
                                html_result = f"<h2>Nilai Per Semester: {nama}</h2>"
                                for mhs in mahasiswa_list:
                                    mhs_info = mhs.get("mahasiswa", {})
                                    semesters = mhs.get("nilai_persemester", [])
                                    html_result += f"<h3>{mhs_info.get('nim')} - {mhs_info.get('nama')}</h3>"
                                    html_result += f"<p>Program Studi: {mhs_info.get('prodi_name')} | <strong>Rata-rata Nilai Kumulatif: {mhs.get('cumulative_gpa')}</strong></p>"
                                    
                                    for sem in semesters:
                                        html_result += f"<h4>{sem.get('semester_label')}</h4>"
                                        html_result += f"<p><strong>Rata-rata Nilai Semester: {sem.get('gpa_semester')}</strong> | Total Courses: {sem.get('total_courses')}</p>"
                                        
                                        if sem.get('courses'):
                                            html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
                                            html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Nama Matakuliah</th><th>Nilai Angka</th></tr></thead><tbody>"
                                            for course in sem.get('courses', []):
                                                html_result += f"<tr><td>{course.get('nama_matkul')}</td><td>{course.get('nilai_angka')}</td></tr>"
                                            html_result += "</tbody></table><br>"
                                
                                state["result"] = html_result
                                logger.info(f"[{user_id}] ✓ Nilai persemester ditemukan untuk {nama}")
                            else:
                                state["result"] = f"<p>Tidak ada data nilai untuk mahasiswa: {nama}</p>"
                                logger.info(f"[{user_id}] ℹ️ Tidak ada nilai persemester untuk {nama}")
                        else:
                            state["result"] = result.get("message", f"Tidak ada data nilai untuk {nama}")
                            logger.warning(f"[{user_id}] ✗ query_nilai persemester gagal untuk {nama}")
                    else:
                        # Get permatkul data for specific student
                        result = get_nilai_permatkul_by_mahasiswa(nama=nama, prodi_id=prodi_id)
                        if result.get("status") == "success":
                            mahasiswa_list = result.get("mahasiswa_list", [])
                            if mahasiswa_list:
                                # Format response with per-course data
                                html_result = f"<h2>Nilai Mahasiswa: {nama}</h2>"
                                for mhs in mahasiswa_list:
                                    mhs_info = mhs.get("mahasiswa", {})
                                    courses = mhs.get("nilai_permatkul", [])
                                    html_result += f"<h3>{mhs_info.get('nim')} - {mhs_info.get('nama')}</h3>"
                                    html_result += f"<p>Program Studi: {mhs_info.get('prodi_name')}</p>"
                                    html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
                                    html_result += "<thead style='background-color:#f2f2f2;'><tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai Angka</th><th>Nilai Huruf</th><th>Bobot</th></tr></thead><tbody>"
                                    for course in courses:
                                        html_result += f"<tr><td>{course.get('kode_mk')}</td><td>{course.get('nama_matkul')}</td><td>{course.get('sks')}</td><td>{course.get('nilai_angka')}</td><td>{course.get('nilai_huruf')}</td><td>{course.get('bobot_nilai')}</td></tr>"
                                    html_result += "</tbody></table>"
                                    html_result += f"<p><strong>Rata-rata Nilai: {mhs.get('rata_rata_nilai')}</strong></p><br>"
                                state["result"] = html_result
                                logger.info(f"[{user_id}] ✓ Nilai permatkul ditemukan untuk {nama}")
                            else:
                                state["result"] = f"<p>Tidak ada data nilai untuk mahasiswa: {nama}</p>"
                                logger.info(f"[{user_id}] ℹ️ Tidak ada nilai permatkul untuk {nama}")
                        else:
                            state["result"] = result.get("message", f"Tidak ada data nilai untuk {nama}")
                            logger.warning(f"[{user_id}] ✗ query_nilai permatkul gagal untuk {nama}")
                else:
                    # Query all students in prodi (dosen context)
                    if "semester" in prompt_lower or semester:
                        # Get persemester for all students
                        result = get_nilai_persemester_by_dosen_context(prodi_id=prodi_id, semester=semester)
                        if result.get("status") == "success":
                            semesters = result.get("semesters", [])
                            html_result = f"<h2>Nilai Per Semester - {result['prodi']['prodi_name']}</h2>"
                            for sem in semesters:
                                html_result += f"<h3>{sem.get('semester_label')}</h3>"
                                html_result += f"<p>Total Mahasiswa: {sem.get('total_mahasiswa')} | Rata-rata GPA: {sem.get('rata_rata_gpa')}</p>"
                                html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
                                html_result += "<thead style='background-color:#f2f2f2;'><tr><th>NIM</th><th>Nama</th><th>GPA</th><th>Total Courses</th></tr></thead><tbody>"
                                for mhs in sem.get('mahasiswa_list', []):
                                    html_result += f"<tr><td>{mhs.get('nim')}</td><td>{mhs.get('nama')}</td><td>{mhs.get('gpa')}</td><td>{mhs.get('total_courses')}</td></tr>"
                                html_result += "</tbody></table><br>"
                            state["result"] = html_result
                            logger.info(f"[{user_id}] ✓ Nilai persemester untuk semua mahasiswa ditemukan")
                        else:
                            state["result"] = result.get("message", "Tidak ada data nilai persemester")
                            logger.warning(f"[{user_id}] ✗ query_nilai persemester dosen context gagal")
                    else:
                        # Get permatkul for all students
                        result = get_nilai_permatkul_by_dosen_context(prodi_id=prodi_id)
                        if result.get("status") == "success":
                            courses = result.get("courses", [])
                            html_result = f"<h2>Nilai Per Matakuliah - {result['prodi']['prodi_name']}</h2>"
                            for course in courses:
                                html_result += f"<h3>{course.get('kode_mk')} - {course.get('nama_matkul')} ({course.get('sks')} SKS)</h3>"
                                html_result += f"<p>Total Mahasiswa: {course.get('total_mahasiswa')} | Rata-rata: {course.get('rata_rata_nilai')}</p>"
                                html_result += "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse;'>"
                                html_result += "<thead style='background-color:#f2f2f2;'><tr><th>NIM</th><th>Nama</th><th>Nilai Angka</th><th>Nilai Huruf</th></tr></thead><tbody>"
                                for mhs in course.get('mahasiswa_list', []):
                                    html_result += f"<tr><td>{mhs.get('nim')}</td><td>{mhs.get('nama')}</td><td>{mhs.get('nilai_angka')}</td><td>{mhs.get('nilai_huruf')}</td></tr>"
                                html_result += "</tbody></table><br>"
                            state["result"] = html_result
                            logger.info(f"[{user_id}] ✓ Nilai permatkul untuk semua mahasiswa ditemukan")
                        else:
                            state["result"] = result.get("message", "Tidak ada data nilai permatkul")
                            logger.warning(f"[{user_id}] ✗ query_nilai permatkul dosen context gagal")

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
                
                # Extract constraints dari prompt
                constraints = _extract_dosen_constraints_from_prompt(prompt)
                
                logger.info(f"[{user_id}] 📋 Constraints extracted: {constraints}")

                result = generate_pembimbing_assignments_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                    min_per_group=1,
                    max_per_group=max_per_group,
                    replace_existing=True,
                    persist=False,
                    exclude_disrecommended=True,
                    constraints=constraints,
                )
                
                # Check if error is due to no groups or no candidates
                if result.get("status") == "empty":
                    error_msg = result.get("message", "")
                    if "Tidak ada kelompok" in error_msg:
                        state["result"] = f"""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Tidak Ada Kelompok</h3>
  <p style="margin:0 0 8px 0;">{error_msg}</p>
  <p style="margin:0; color:#374151;"><strong>Solusi:</strong> Silakan buat kelompok terlebih dahulu sebelum membuat pembimbing. Gunakan perintah seperti:</p>
  <ul style="margin:8px 0 0 20px; color:#374151;">
    <li>"Buat 5 orang per kelompok berdasarkan nilai"</li>
    <li>"Buatkan kelompok dengan 6 anggota"</li>
  </ul>
</div>
"""
                        logger.info(f"[{user_id}] ℹ️  Tidak ada kelompok untuk pembimbing - user perlu buat kelompok dulu")
                    elif "dosen" in error_msg.lower() or "pembimbing" in error_msg.lower():
                        state["result"] = f"""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Tidak Ada Dosen Pembimbing</h3>
  <p style="margin:0 0 8px 0;">{error_msg}</p>
  <p style="margin:0; color:#374151;"><strong>Solusi:</strong> Silakan tambahkan dosen pembimbing yang tersedia sebelum membuat assignment pembimbing.</p>
</div>
"""
                        logger.info(f"[{user_id}] ℹ️  Tidak ada dosen pembimbing kandidat")
                    else:
                        state["result"] = format_generate_pembimbing_result(result)
                        logger.info(f"[{user_id}] ℹ️  Error pembimbing: {error_msg}")
                else:
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
                        "constraints": constraints,
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

                # Check if error is due to no groups or no candidates
                if result.get("status") == "empty":
                    error_msg = result.get("message", "")
                    if "Tidak ada kelompok" in error_msg:
                        state["result"] = f"""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Tidak Ada Kelompok</h3>
  <p style="margin:0 0 8px 0;">{error_msg}</p>
  <p style="margin:0; color:#374151;"><strong>Solusi:</strong> Silakan buat kelompok terlebih dahulu sebelum membuat penguji. Gunakan perintah seperti:</p>
  <ul style="margin:8px 0 0 20px; color:#374151;">
    <li>"Buat 5 orang per kelompok berdasarkan nilai"</li>
    <li>"Buatkan kelompok dengan 6 anggota"</li>
  </ul>
</div>
"""
                        logger.info(f"[{user_id}] ℹ️  Tidak ada kelompok untuk penguji - user perlu buat kelompok dulu")
                    elif "dosen" in error_msg.lower() or "penguji" in error_msg.lower():
                        state["result"] = f"""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Tidak Ada Dosen Penguji</h3>
  <p style="margin:0 0 8px 0;">{error_msg}</p>
  <p style="margin:0; color:#374151;"><strong>Solusi:</strong> Silakan tambahkan dosen penguji yang tersedia sebelum membuat assignment penguji.</p>
</div>
"""
                        logger.info(f"[{user_id}] ℹ️  Tidak ada dosen penguji kandidat")
                    else:
                        state["result"] = format_generate_penguji_result(result)
                        logger.info(f"[{user_id}] ℹ️  Error penguji: {error_msg}")
                else:
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

        elif action == "clarify_group_requirements":
            logger.info(f"[{user_id}] ⚙️  TOOLS: clarify_group_requirements (show interactive form)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                context = {
                    "prodi_id": dosen_context.get("prodi_id"),
                    "kategori_pa": dosen_context.get("kategori_pa", "Unknown PA"),
                }
                
                form_html = GroupingFormHandler.generate_form_html(context)
                state["result"] = form_html
                state["grouping_form_shown"] = True
                state["grouping_form_context"] = context
                logger.info(f"[{user_id}] ✓ Form grouping ditampilkan, menunggu user input")
        
        elif action == "process_grouping_form":
            logger.info(f"[{user_id}] ⚙️  TOOLS: process_grouping_form (process form submission)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                # Extract form data dari request
                form_data = state.get("request_data", {}).get("form_data", {})
                
                if not form_data:
                    state["result"] = "❌ Tidak ada data form yang dikirimkan."
                    logger.error(f"[{user_id}] ✗ Form data kosong")
                    return state
                
                # Parse form submission
                try:
                    form_spec = GroupingFormHandler.parse_form_submission(form_data)
                    logger.info(f"[{user_id}] 📋 Form parsed: method={form_spec.get('method')}, size_mode={form_spec.get('size_mode')}")
                    
                    # Build prompt dari form
                    generated_prompt = GroupingFormHandler.build_grouping_prompt(form_spec)
                    logger.info(f"[{user_id}] 🎯 Generated prompt:\n{generated_prompt}")
                    
                    # Store form spec untuk context
                    state["grouping_form_spec"] = form_spec
                    
                    method = form_spec.get("method", "auto")
                    constraints = form_spec.get("constraints", [])
                    
                    if method == "by_grades":
                        # Calculate group_count
                        try:
                            grade_result = calculate_student_average_grades(
                                prodi_id=dosen_context.get("prodi_id"),
                                kategori_pa_id=dosen_context.get("kategori_pa"),
                                angkatan_id=dosen_context.get("angkatan"),
                                exclude_existing=True
                            )
                            if grade_result.get("status") == "success":
                                available_students = len(grade_result.get("student_grades", []))
                                if form_spec.get("size_mode") == "exact":
                                    members_per_group = form_spec.get("exact_size") or 5
                                else:
                                    members_per_group = form_spec.get("max_size") or 6
                                group_count = math.ceil(available_students / members_per_group)
                            else:
                                group_count = 6
                        except Exception as e:
                            group_count = 6
                            logger.error(f"Error calculating group count in process_grouping_form: {e}")

                        grouping_result = create_group_by_grades(
                            prodi_id=dosen_context.get("prodi_id"),
                            kategori_pa_id=dosen_context.get("kategori_pa"),
                            group_count=group_count,
                            angkatan_id=dosen_context.get("angkatan"),
                            exclude_existing=True,
                            randomize_ties=False,
                            constraints=constraints,
                        )
                        
                        if grouping_result.get("status") == "success":
                            groups = grouping_result.get("groups", [])
                            class_stats = grouping_result.get("class_statistics", {})
                            group_stats = grouping_result.get("group_statistics", {})
                            breakdown = grouping_result.get("breakdown", {})
                            acceptable_range = group_stats.get("acceptable_range", {})
                            
                            # Display constraint warnings if any
                            warnings_html = ""
                            if grouping_result.get("constraint_warnings"):
                                warnings_list = "".join(f"<li style='margin-bottom:4px;'>{html.escape(w)}</li>" for w in grouping_result.get("constraint_warnings"))
                                warnings_html = f"""
  <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; padding:12px; margin-bottom:16px; color:#991b1b;">
    <h4 style="margin-top:0; margin-bottom:8px; color:#7f1d1d; font-size:14px;">⚠️ Peringatan Constraint:</h4>
    <ul style="margin:0; padding-left:20px; font-size:13px; color:#7f1d1d;">
      {warnings_list}
    </ul>
  </div>
"""
                            
                            html_result = f"""
<div style="background:#f3f4f6; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#1f2937;">✅ Kelompok Berdasarkan Nilai Berhasil Dibuat</h3>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px;">
    <p style="margin:0 0 8px 0;"><strong>📊 Kategori PA:</strong> {grouping_result.get('pa_category', 'N/A')}</p>
    <p style="margin:0 0 8px 0;"><strong>📚 Semester yang Digunakan:</strong> {', '.join(map(str, grouping_result.get('semesters_used', [])))}</p>
    <p style="margin:0;"><strong>🎯 Jumlah Kelompok:</strong> {len(groups)}</p>
  </div>
  
  {warnings_html}
  
  <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:6px; padding:12px; margin-bottom:16px;">
    <h4 style="margin-top:0; color:#b45309;">📋 Breakdown Mahasiswa</h4>
    <table style="width:100%; border-collapse:collapse;">
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Total Mahasiswa dalam Prodi:</strong></td>
        <td style="padding:8px;">{breakdown.get('total_mahasiswa_dalam_prodi', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Sudah dalam Kelompok (Excluded):</strong></td>
        <td style="padding:8px;">{breakdown.get('sudah_dalam_kelompok_excluded', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Kandidat untuk Grouping:</strong></td>
        <td style="padding:8px;"><strong>{breakdown.get('kandidat_untuk_grouping', 0)}</strong></td>
      </tr>
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Dengan Data Nilai (Nilai Aktual):</strong></td>
        <td style="padding:8px;"><strong style="color:#10b981;">{breakdown.get('dengan_data_nilai_semesters', 0)} ✓</strong></td>
      </tr>
      <tr>
        <td style="padding:8px;"><strong>Tanpa Data Nilai (Nilai Default 0):</strong></td>
        <td style="padding:8px;"><strong style="color:#3b82f6;">{breakdown.get('tanpa_data_nilai_semesters', 0)} ✓</strong></td>
      </tr>
    </table>
    <p style="margin:12px 0 0 0; font-size:12px; color:#1f2937; font-style:italic;">
      <strong>💡 Catatan:</strong> Semua {breakdown.get('kandidat_untuk_grouping', 0)} mahasiswa kandidat digunakan dalam grouping. 
      {breakdown.get('catatan', '')}
    </p>
  </div>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px;">
    <h4 style="margin-top:0; color:#374151;">📈 Statistik Kelas</h4>
    <table style="width:100%; border-collapse:collapse;">
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Total Mahasiswa:</strong></td>
        <td style="padding:8px;">{class_stats.get('total_students', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Rata-rata Nilai Kelas:</strong></td>
        <td style="padding:8px;">{class_stats.get('mean', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Standar Deviasi:</strong></td>
        <td style="padding:8px;">{class_stats.get('std_dev', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Range Nilai:</strong></td>
        <td style="padding:8px;">{class_stats.get('min_grade', 0)} - {class_stats.get('max_grade', 0)}</td>
      </tr>
    </table>
  </div>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px;">
    <h4 style="margin-top:0; color:#374151;">👥 Detail Kelompok</h4>
"""
                            for group in groups:
                                members_html = "<ul style='margin:8px 0; padding-left:20px;'>"
                                for member in group.get("members", []):
                                    members_html += f"<li>{member.get('nim', 'N/A')} - {member.get('nama', 'N/A')} (Nilai: {member.get('average_grade', 0)})</li>"
                                members_html += "</ul>"
                                
                                within_range = group.get("within_acceptable_range", False)
                                status_icon = "✅" if within_range else "⚠️"
                                status_color = "#059669" if within_range else "#d97706"
                                
                                html_result += f"""
    <div style="border:1px solid #d1d5db; border-radius:4px; padding:12px; margin-bottom:12px; background:#f9fafb;">
      <p style="margin:0 0 8px 0;"><strong>Kelompok {group.get('group_number')}:</strong> {group.get('member_count')} anggota</p>
      <p style="margin:0 0 8px 0; color:{status_color};"><strong>{status_icon} Rata-rata Nilai: {group.get('group_average')} (Deviasi: {abs(group.get('deviation_from_mean', 0))})</strong></p>
      <p style="margin:0 0 8px 0; font-size:12px; color:#6b7280;">Anggota Kelompok:</p>
      {members_html}
    </div>
"""
                            
                            html_result += f"""
  </div>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px;">
    <h4 style="margin-top:0; color:#374151;">✔️ Verifikasi Keseimbangan</h4>
    <p style="margin:0 0 8px 0;"><strong>Range Nilai Acceptable:</strong> {acceptable_range.get('min', 0)} - {acceptable_range.get('max', 0)} (Center: {acceptable_range.get('center', 0)} ± {acceptable_range.get('std_dev', 0)})</p>
    <p style="margin:0;"><strong>Status:</strong> {'✅ Semua kelompok seimbang' if group_stats.get('all_within_range') else '⚠️ Beberapa kelompok menyimpang dari range'}</p>
  </div>
</div>
"""
                            state["result"] = html_result
                            state["grouping_payload"] = {
                                "groups": groups,
                                "class_statistics": class_stats,
                                "group_statistics": group_stats,
                            }
                            state["grouping_meta"] = {
                                "prodi_id": dosen_context.get("prodi_id"),
                                "kategori_pa_id": dosen_context.get("kategori_pa"),
                                "angkatan_id": dosen_context.get("angkatan"),
                                "method": "by_grades",
                                "form_spec": form_spec,
                                "generated_prompt": generated_prompt,
                            }
                            logger.info(f"[{user_id}] ✓ {len(groups)} kelompok berdasarkan nilai berhasil dibuat dari form")
                        else:
                            state["result"] = f"<p style='color:#dc2626;'><strong>❌ Gagal membuat kelompok:</strong> {grouping_result.get('message', 'Error tidak diketahui')}</p>"
                            logger.warning(f"[{user_id}] ✗ create_group_by_grades dari form gagal: {grouping_result.get('message')}")
                    
                    elif method == "auto" and constraints:
                        # Call hybrid grouping function
                        hybrid_result = create_group_hybrid(
                            prompt=generated_prompt,
                            prodi_id=dosen_context.get("prodi_id"),
                            kategori_pa_id=dosen_context.get("kategori_pa"),
                            angkatan_id=dosen_context.get("angkatan"),
                            exclude_existing=True,
                        )
                        
                        # Format result for display
                        if hybrid_result.get("status") == "success":
                            summary = hybrid_result.get("summary", {})
                            groups = hybrid_result.get("groups", [])
                            
                            # Build HTML result
                            html_result = f"""
<div style="background:#d1fae5; border:1px solid #10b981; border-radius:8px; padding:16px; margin-bottom:16px;">
  <h3 style="margin-top:0; color:#065f46;">✓ Kelompok Hybrid Berhasil Dibuat</h3>
  <ul style="margin:0; padding-left:20px;">
    <li><strong>Total Kelompok:</strong> {summary.get('total_groups', 0)}</li>
    <li><strong>Anggota Terkontrol:</strong> {summary.get('constrained_members', 0)}</li>
    <li><strong>Anggota Otomatis (By Grades):</strong> {summary.get('auto_grouped_members', 0)}</li>
    <li><strong>Total Mahasiswa:</strong> {summary.get('total_candidates', 0)}</li>
    <li><strong>Rata Kelas:</strong> {summary.get('rata_kelas', 0)}</li>
    <li><strong>Jarak Deviasi:</strong> {summary.get('jarak_deviasi', 0)}</li>
  </ul>
"""
                            
                            if summary.get("constraint_errors"):
                                html_result += f"""
  <div style="margin-top:12px; padding:10px; background:#fee2e2; border-radius:4px; color:#7f1d1d;">
    <strong>⚠️ Peringatan:</strong><br/>
    {", ".join(summary.get('constraint_errors', []))}
  </div>
"""
                            
                            html_result += """
  <div style="margin-top:12px;">
    <strong>📋 Detail Kelompok:</strong>
    <table style="width:100%; border-collapse:collapse; margin-top:8px; font-size:12px;">
      <tr style="background:#e0f2fe;">
        <th style="border:1px solid #94e2f0; padding:6px;">Kelompok</th>
        <th style="border:1px solid #94e2f0; padding:6px;">NIM</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Nama</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Nilai</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Rata-rata Kelompok</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Std Dev</th>
      </tr>
"""
                            
                            for group in groups:
                                group_avg = group.get('rata_rata_kelompok', 0)
                                group_std = group.get('std_dev_kelompok', 0)
                                member_count = len(group["members"])
                                
                                for idx, member in enumerate(group["members"]):
                                    # Show group number only on first row
                                    if idx == 0:
                                        html_result += f"""
      <tr>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center; vertical-align:middle; background:#f0f9ff;" rowspan="{member_count}"><strong>{group['group_number']}</strong></td>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nim']}</td>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nama']}</td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center;">{member.get('nilai', 0)}</td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center; vertical-align:middle; background:#f0f9ff;" rowspan="{member_count}"><strong>{group_avg}</strong></td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center; vertical-align:middle; background:#f0f9ff;" rowspan="{member_count}"><strong>±{group_std}</strong></td>
      </tr>
"""
                                    else:
                                        html_result += f"""
      <tr>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nim']}</td>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nama']}</td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center;">{member.get('nilai', 0)}</td>
      </tr>
"""
                            
                            html_result += """
    </table>
  </div>
</div>
"""
                            state["result"] = html_result
                            state["grouping_payload"] = {
                                "summary": summary,
                                "groups": groups,
                            }
                            state["grouping_meta"] = {
                                "prodi_id": dosen_context.get("prodi_id"),
                                "kategori_pa_id": dosen_context.get("kategori_pa"),
                                "angkatan_id": dosen_context.get("angkatan"),
                                "form_spec": form_spec,
                                "generated_prompt": generated_prompt,
                            }
                            logger.info(f"[{user_id}] ✓ {len(groups)} kelompok hybrid dari form siap")
                        else:
                            state["result"] = f"❌ {hybrid_result.get('message', 'Gagal membuat kelompok hybrid')}"
                            logger.warning(f"[{user_id}] ✗ create_group_hybrid dari form gagal: {hybrid_result.get('message')}")
                    
                    else:
                        # Fallback to default create_group
                        grouping_result = create_group(
                            prompt=generated_prompt,
                            prodi_id=dosen_context.get("prodi_id"),
                            kategori_pa_id=dosen_context.get("kategori_pa"),
                            angkatan_id=dosen_context.get("angkatan"),
                            exclude_existing=True,
                        )
                        state["result"] = format_grouping_result(grouping_result)
                        
                        if grouping_result.get("status") == "success":
                            state["grouping_payload"] = {
                                "instruction": grouping_result.get("instruction", {}),
                                "summary": grouping_result.get("summary", {}),
                                "groups": grouping_result.get("groups", []),
                            }
                            state["grouping_meta"] = {
                                "prodi_id": dosen_context.get("prodi_id"),
                                "kategori_pa_id": dosen_context.get("kategori_pa"),
                                "angkatan_id": dosen_context.get("angkatan"),
                                "form_spec": form_spec,
                                "generated_prompt": generated_prompt,
                            }
                            logger.info(f"[{user_id}] ✓ {grouping_result.get('summary', {}).get('total_groups', 0)} kelompok direkomendasikan dari form")
                        else:
                            logger.warning(f"[{user_id}] ✗ Grouping dari form gagal: {grouping_result.get('message')}")
                
                except Exception as e:
                    logger.error(f"[{user_id}] ✗ Error processing form: {e}", exc_info=True)
                    state["result"] = f"❌ Error memproses form: {str(e)}"

        elif action == "create_group":
            logger.info(f"[{user_id}] ⚙️  TOOLS: create_group (buat kelompok)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                
                # Check if user intent is to RECREATE (acak kembali, buat ulang, ganti)
                recreate_keywords = ["acak kembali", "acak ulang", "buat ulang", "ganti"]
                is_recreate_intent = bool(plan_params.get("is_recreate_intent")) or any(kw in prompt_lower for kw in recreate_keywords)
                
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

        elif action == "create_group_hybrid":
            logger.info(f"[{user_id}] ⚙️  TOOLS: create_group_hybrid (constraint + auto by grades)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                
                # Call hybrid grouping function
                hybrid_result = create_group_hybrid(
                    prompt=prompt,
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                    exclude_existing=True,
                )
                
                # Format result for display
                if hybrid_result.get("status") == "success":
                    summary = hybrid_result.get("summary", {})
                    groups = hybrid_result.get("groups", [])
                    
                    # Build HTML result
                    html_result = f"""
<div style="background:#d1fae5; border:1px solid #10b981; border-radius:8px; padding:16px; margin-bottom:16px;">
  <h3 style="margin-top:0; color:#065f46;">✓ Kelompok Hybrid Berhasil Dibuat</h3>
  <ul style="margin:0; padding-left:20px;">
    <li><strong>Total Kelompok:</strong> {summary.get('total_groups', 0)}</li>
    <li><strong>Anggota Terkontrol:</strong> {summary.get('constrained_members', 0)}</li>
    <li><strong>Anggota Otomatis (By Grades):</strong> {summary.get('auto_grouped_members', 0)}</li>
    <li><strong>Total Mahasiswa:</strong> {summary.get('total_candidates', 0)}</li>
    <li><strong>Rata Kelas:</strong> {summary.get('rata_kelas', 0)}</li>
    <li><strong>Jarak Deviasi:</strong> {summary.get('jarak_deviasi', 0)}</li>
  </ul>
"""
                    
                    if summary.get("constraint_errors"):
                        html_result += f"""
  <div style="margin-top:12px; padding:10px; background:#fee2e2; border-radius:4px; color:#7f1d1d;">
    <strong>⚠️ Peringatan:</strong><br/>
    {", ".join(summary.get('constraint_errors', []))}
  </div>
"""
                    
                    html_result += """
  <div style="margin-top:12px;">
    <strong>📋 Detail Kelompok:</strong>
    <table style="width:100%; border-collapse:collapse; margin-top:8px; font-size:12px;">
      <tr style="background:#e0f2fe;">
        <th style="border:1px solid #94e2f0; padding:6px;">Kelompok</th>
        <th style="border:1px solid #94e2f0; padding:6px;">NIM</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Nama</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Nilai</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Rata-rata Kelompok</th>
        <th style="border:1px solid #94e2f0; padding:6px;">Std Dev</th>
      </tr>
"""
                    
                    for group in groups:
                        group_avg = group.get('rata_rata_kelompok', 0)
                        group_std = group.get('std_dev_kelompok', 0)
                        member_count = len(group["members"])
                        
                        for idx, member in enumerate(group["members"]):
                            # Show group number only on first row
                            if idx == 0:
                                html_result += f"""
      <tr>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center; vertical-align:middle; background:#f0f9ff;" rowspan="{member_count}"><strong>{group['group_number']}</strong></td>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nim']}</td>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nama']}</td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center;">{member.get('nilai', 0)}</td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center; vertical-align:middle; background:#f0f9ff;" rowspan="{member_count}"><strong>{group_avg}</strong></td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center; vertical-align:middle; background:#f0f9ff;" rowspan="{member_count}"><strong>±{group_std}</strong></td>
      </tr>
"""
                            else:
                                html_result += f"""
      <tr>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nim']}</td>
        <td style="border:1px solid #94e2f0; padding:6px;">{member['nama']}</td>
        <td style="border:1px solid #94e2f0; padding:6px; text-align:center;">{member.get('nilai', 0)}</td>
      </tr>
"""
                    
                    html_result += """
    </table>
  </div>
</div>
"""
                    
                    state["result"] = html_result
                    state["grouping_payload"] = {
                        "summary": summary,
                        "groups": groups,
                    }
                    state["grouping_meta"] = {
                        "prodi_id": dosen_context.get("prodi_id"),
                        "kategori_pa_id": dosen_context.get("kategori_pa"),
                        "angkatan_id": dosen_context.get("angkatan"),
                    }
                    logger.info(f"[{user_id}] ✓ {groups.__len__()} kelompok hybrid siap (constraint: {summary.get('constrained_members')}, auto: {summary.get('auto_grouped_members')})")
                else:
                    error_msg = hybrid_result.get('message', 'Gagal membuat kelompok hybrid')
                    
                    # Check if error is due to all students already grouped or empty context
                    if "Tidak ada mahasiswa" in error_msg or hybrid_result.get("status") == "empty":
                        escaped_prompt = html.escape(prompt)
                        error_html = f"""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px; margin-bottom:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Semua Mahasiswa Sudah Dalam Kelompok</h3>
  <p style="margin:0 0 12px 0;">{error_msg}</p>
  <p style="margin:0 0 12px 0; color:#374151;">Untuk membuat kelompok baru, perlu menghapus kelompok yang sudah ada terlebih dahulu.</p>
  <div style="display:flex; gap:8px; flex-wrap:wrap;">
    <button type="button" class="btn btn-danger confirm-recreate-groups" style="padding:8px 16px;" data-recreate-prompt="{escaped_prompt}" onclick="if(window.__confirmRecreateGroupsFromInline){{window.__confirmRecreateGroupsFromInline(event);}}"> 
      <i class="fas fa-trash"></i> Hapus Kelompok Lama & Buat Baru
    </button>
    <button type="button" class="btn btn-secondary cancel-recreate" style="padding:8px 16px;">
      Batal
    </button>
  </div>
</div>
"""
                        state["result"] = error_html
                        logger.warning(f"[{user_id}] ✗ Semua mahasiswa sudah dalam kelompok: {error_msg}")
                    else:
                        state["result"] = f"❌ {error_msg}"
                        logger.warning(f"[{user_id}] ✗ create_group_hybrid gagal: {error_msg}")

        elif action == "create_group_by_grades":
            logger.info(f"[{user_id}] ⚙️  TOOLS: create_group_by_grades (buat kelompok berdasarkan nilai)")
            
            if not dosen_context:
                state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
            else:
                prompt = state.get("messages", [{}])[-1].get("content", "")
                
                # Check if this is a CONFIRMED recreate request
                is_confirm_recreate = prompt.upper().startswith("[CONFIRM_RECREATE]")
                if is_confirm_recreate:
                    # Strip the marker from prompt for further processing
                    prompt = prompt.replace("[CONFIRM_RECREATE]", "", 1).strip()
                    logger.info(f"[{user_id}] 🔐 CONFIRM_RECREATE marker detected, proceeding to delete + recreate")
                    
                    # Delete existing groups for this context
                    delete_result = delete_kelompok_by_context(
                        prodi_id=dosen_context.get("prodi_id"),
                        kategori_pa_id=dosen_context.get("kategori_pa"),
                        angkatan_id=dosen_context.get("angkatan"),
                    )
                    
                    if delete_result.get("status") == "success":
                        logger.info(f"[{user_id}] ✓ Deleted {delete_result.get('count', 0)} existing groups")
                    else:
                        logger.warning(f"[{user_id}] ⚠️  Failed to delete groups: {delete_result.get('message')}")
                
                # Check if user intent is to RECREATE
                recreate_keywords = ["acak kembali", "acak ulang", "buat ulang", "ganti"]
                local_prompt_lower = prompt.lower()
                is_recreate_intent = bool(plan_params.get("is_recreate_intent")) or any(kw in local_prompt_lower for kw in recreate_keywords)
                
                # Cek existing groups pada konteks ini
                existing_check = check_existing_kelompok_by_context(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    angkatan_id=dosen_context.get("angkatan"),
                )
                
                if not is_confirm_recreate and is_recreate_intent and existing_check.get("status") == "success" and existing_check.get("total", 0) > 0:
                    logger.info(f"[{user_id}] 🔄 RECREATE INTENT DETECTED: {existing_check.get('total')} existing groups found")
                    
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
                
               # ── Parse members_per_group dengan benar ──────────────────────────────
                # Priority 1: "minimal X orang, maksimal Y orang" → pakai MAX sebagai target
                # Priority 2: "X orang per kelompok"
                # Priority 3: "kelompok dengan X orang"
                # Priority 4: "X kelompok" (fallback)

                group_count      = None
                members_per_group = None

                # Priority 1: Range min-max
                range_match = re.search(
                    r'minimal\s+(\d+)\s+orang[,\s]+maksimal\s+(\d+)\s+orang',
                    local_prompt_lower
                )
                if not range_match:
                    range_match = re.search(
                        r'min(?:imal)?\s+(\d+)[,\s]+max(?:imal)?\s+(\d+)',
                        local_prompt_lower
                    )
                if range_match:
                    min_size          = int(range_match.group(1))
                    max_size          = int(range_match.group(2))
                    members_per_group = max_size   # gunakan MAX agar kelompok tidak terlalu banyak
                    logger.info(f"[{user_id}] 👥 Range pattern: min={min_size}, max={max_size} → members_per_group={members_per_group}")

                # Priority 2: "X orang per kelompok"
                if not members_per_group:
                    m = re.search(r'(\d+)\s+orang\s+per\s?kelompok', local_prompt_lower)
                    if m:
                        members_per_group = int(m.group(1))
                        logger.info(f"[{user_id}] 👥 'orang per kelompok' pattern: {members_per_group}")

                # Priority 3: "kelompok dengan X orang"
                if not members_per_group:
                    m = re.search(r'kelompok\s+dengan\s+(\d+)\s+orang', local_prompt_lower)
                    if m:
                        members_per_group = int(m.group(1))
                        logger.info(f"[{user_id}] 👥 'kelompok dengan X orang' pattern: {members_per_group}")

                # Priority 4: "X kelompok" (fallback)
                if not members_per_group:
                    m = re.search(r'(\d+)\s+kelompok', local_prompt_lower)
                    if m:
                        members_per_group = int(m.group(1))
                        logger.info(f"[{user_id}] 👥 'X kelompok' fallback: {members_per_group} members/group")

                # Hitung group_count dari members_per_group
                if members_per_group:
                    try:
                        grade_result = calculate_student_average_grades(
                            prodi_id=dosen_context.get("prodi_id"),
                            kategori_pa_id=dosen_context.get("kategori_pa"),
                            angkatan_id=dosen_context.get("angkatan"),
                            exclude_existing=True
                        )
                        if grade_result.get("status") == "success":
                            available_students = len(grade_result.get("student_grades", []))
                            group_count = math.ceil(available_students / members_per_group)
                            logger.info(
                                f"[{user_id}] ℹ️ mahasiswa={available_students}, "
                                f"members_per_group={members_per_group} → group_count={group_count}"
                            )
                        else:
                            group_count = 6
                    except Exception as e:
                        group_count = 6
                        logger.warning(f"[{user_id}] ⚠️ Error: {e}, default group_count=6")

                if not group_count:
                    group_count = 6
                    logger.info(f"[{user_id}] ℹ️ Default group_count=6")
                constraints = GroupingFormHandler._parse_constraints(prompt)
                logger.info(f"[{user_id}] 📍 Create group by grades: prodi_id={dosen_context.get('prodi_id')}, kategori_pa_id={dosen_context.get('kategori_pa')}, group_count={group_count}, constraints={len(constraints) if constraints else 0}")
                
                grouping_result = create_group_by_grades(
                    prodi_id=dosen_context.get("prodi_id"),
                    kategori_pa_id=dosen_context.get("kategori_pa"),
                    group_count=group_count,
                    angkatan_id=dosen_context.get("angkatan"),
                    exclude_existing=True,
                    randomize_ties=is_recreate_intent,
                    constraints=constraints,
                )
                
                if grouping_result.get("status") == "success":
                    # Format result
                    groups = grouping_result.get("groups", [])
                    class_stats = grouping_result.get("class_statistics", {})
                    group_stats = grouping_result.get("group_statistics", {})
                    breakdown = grouping_result.get("breakdown", {})
                    
                    acceptable_range = group_stats.get("acceptable_range", {})
                    
                    # Display constraint warnings if any
                    warnings_html = ""
                    if grouping_result.get("constraint_warnings"):
                        warnings_list = "".join(f"<li style='margin-bottom:4px;'>{html.escape(w)}</li>" for w in grouping_result.get("constraint_warnings"))
                        warnings_html = f"""
  <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; padding:12px; margin-bottom:16px; color:#991b1b;">
    <h4 style="margin-top:0; margin-bottom:8px; color:#7f1d1d; font-size:14px;">⚠️ Peringatan Constraint:</h4>
    <ul style="margin:0; padding-left:20px; font-size:13px; color:#7f1d1d;">
      {warnings_list}
    </ul>
  </div>
"""
                    
                    html_result = f"""
<div style="background:#f3f4f6; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#1f2937;">✅ Kelompok Berdasarkan Nilai Berhasil Dibuat</h3>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px;">
    <p style="margin:0 0 8px 0;"><strong>📊 Kategori PA:</strong> {grouping_result.get('pa_category', 'N/A')}</p>
    <p style="margin:0 0 8px 0;"><strong>📚 Semester yang Digunakan:</strong> {', '.join(map(str, grouping_result.get('semesters_used', [])))}</p>
    <p style="margin:0;"><strong>🎯 Jumlah Kelompok:</strong> {len(groups)}</p>
  </div>
  
  {warnings_html}
  
  <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:6px; padding:12px; margin-bottom:16px;">
    <h4 style="margin-top:0; color:#b45309;">📋 Breakdown Mahasiswa</h4>
    <table style="width:100%; border-collapse:collapse;">
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Total Mahasiswa dalam Prodi:</strong></td>
        <td style="padding:8px;">{breakdown.get('total_mahasiswa_dalam_prodi', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Sudah dalam Kelompok (Excluded):</strong></td>
        <td style="padding:8px;">{breakdown.get('sudah_dalam_kelompok_excluded', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Kandidat untuk Grouping:</strong></td>
        <td style="padding:8px;"><strong>{breakdown.get('kandidat_untuk_grouping', 0)}</strong></td>
      </tr>
      <tr style="border-bottom:1px solid #f59e0b;">
        <td style="padding:8px;"><strong>Dengan Data Nilai (Nilai Aktual):</strong></td>
        <td style="padding:8px;"><strong style="color:#10b981;">{breakdown.get('dengan_data_nilai_semesters', 0)} ✓</strong></td>
      </tr>
      <tr>
        <td style="padding:8px;"><strong>Tanpa Data Nilai (Nilai Default 0):</strong></td>
        <td style="padding:8px;"><strong style="color:#3b82f6;">{breakdown.get('tanpa_data_nilai_semesters', 0)} ✓</strong></td>
      </tr>
    </table>
    <p style="margin:12px 0 0 0; font-size:12px; color:#1f2937; font-style:italic;">
      <strong>💡 Catatan:</strong> Semua {breakdown.get('kandidat_untuk_grouping', 0)} mahasiswa kandidat digunakan dalam grouping. 
      {breakdown.get('catatan', '')}
    </p>
  </div>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px;">
    <h4 style="margin-top:0; color:#374151;">📈 Statistik Kelas</h4>
    <table style="width:100%; border-collapse:collapse;">
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Total Mahasiswa:</strong></td>
        <td style="padding:8px;">{class_stats.get('total_students', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Rata-rata Nilai Kelas:</strong></td>
        <td style="padding:8px;">{class_stats.get('mean', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Standar Deviasi:</strong></td>
        <td style="padding:8px;">{class_stats.get('std_dev', 0)}</td>
      </tr>
      <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:8px;"><strong>Range Nilai:</strong></td>
        <td style="padding:8px;">{class_stats.get('min_grade', 0)} - {class_stats.get('max_grade', 0)}</td>
      </tr>
    </table>
  </div>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px;">
    <h4 style="margin-top:0; color:#374151;">👥 Detail Kelompok</h4>
"""
                    
                    acceptable_range = group_stats.get("acceptable_range", {})
                    for group in groups:
                        members_html = "<ul style='margin:8px 0; padding-left:20px;'>"
                        for member in group.get("members", []):
                            members_html += f"<li>{member.get('nim', 'N/A')} - {member.get('nama', 'N/A')} (Nilai: {member.get('average_grade', 0)})</li>"
                        members_html += "</ul>"
                        
                        within_range = group.get("within_acceptable_range", False)
                        status_icon = "✅" if within_range else "⚠️"
                        status_color = "#059669" if within_range else "#d97706"
                        
                        html_result += f"""
    <div style="border:1px solid #d1d5db; border-radius:4px; padding:12px; margin-bottom:12px; background:#f9fafb;">
      <p style="margin:0 0 8px 0;"><strong>Kelompok {group.get('group_number')}:</strong> {group.get('member_count')} anggota</p>
      <p style="margin:0 0 8px 0; color:{status_color};"><strong>{status_icon} Rata-rata Nilai: {group.get('group_average')} (Deviasi: {abs(group.get('deviation_from_mean', 0))})</strong></p>
      <p style="margin:0 0 8px 0; font-size:12px; color:#6b7280;">Anggaran Kelompok:</p>
      {members_html}
    </div>
"""
                    
                    html_result += f"""
  </div>
  
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:12px;">
    <h4 style="margin-top:0; color:#374151;">✔️ Verifikasi Keseimbangan</h4>
    <p style="margin:0 0 8px 0;"><strong>Range Nilai Acceptable:</strong> {acceptable_range.get('min', 0)} - {acceptable_range.get('max', 0)} (Center: {acceptable_range.get('center', 0)} ± {acceptable_range.get('std_dev', 0)})</p>
    <p style="margin:0;"><strong>Status:</strong> {'✅ Semua kelompok seimbang' if group_stats.get('all_within_range') else '⚠️ Beberapa kelompok menyimpang dari range'}</p>
  </div>
</div>
"""
                    
                    state["result"] = html_result
                    state["grouping_payload"] = {
                        "groups": groups,
                        "class_statistics": class_stats,
                        "group_statistics": group_stats,
                    }
                    state["grouping_meta"] = {
                        "prodi_id": dosen_context.get("prodi_id"),
                        "kategori_pa_id": dosen_context.get("kategori_pa"),
                        "angkatan_id": dosen_context.get("angkatan"),
                        "method": "by_grades",
                        "randomized": is_recreate_intent,
                    }
                    logger.info(f"[{user_id}] ✓ {len(groups)} kelompok berdasarkan nilai berhasil dibuat")
                else:
                    error_msg = grouping_result.get("message", "Error tidak diketahui")
                    
                    # Check if error is due to all students already grouped
                    if "Tidak ada mahasiswa pada konteks" in error_msg:
                        escaped_prompt = html.escape(prompt)
                        error_html = f"""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px; margin-bottom:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Semua Mahasiswa Sudah Dalam Kelompok</h3>
  <p style="margin:0 0 12px 0;">{error_msg}</p>
  <p style="margin:0 0 12px 0; color:#374151;">Untuk membuat kelompok baru, perlu menghapus kelompok yang sudah ada terlebih dahulu.</p>
  <div style="display:flex; gap:8px; flex-wrap:wrap;">
    <button type="button" class="btn btn-danger confirm-recreate-groups" style="padding:8px 16px;" data-recreate-prompt="{escaped_prompt}" onclick="if(window.__confirmRecreateGroupsFromInline){{window.__confirmRecreateGroupsFromInline(event);}}"> 
      <i class="fas fa-trash"></i> Hapus Kelompok Lama & Buat Baru
    </button>
    <button type="button" class="btn btn-secondary cancel-recreate" style="padding:8px 16px;">
      Batal
    </button>
  </div>
</div>
"""
                        state["result"] = error_html
                        logger.warning(f"[{user_id}] ✗ Semua mahasiswa sudah dalam kelompok: {error_msg}")
                    else:
                        state["result"] = f"<p style='color:#dc2626;'><strong>❌ Gagal membuat kelompok:</strong> {error_msg}</p>"
                        logger.warning(f"[{user_id}] ✗ create_group_by_grades gagal: {error_msg}")

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
        
        elif action in ("generate_jadwal_seminar", "save_jadwal"):
            logger.info(f"[{user_id}] ⚙️  TOOLS: {action} (buat jadwal seminar)")
            
            prompt = state.get("messages", [{}])[-1].get("content", "")
            prompt_lower = prompt.lower()
            is_jadwal_submission = (
                "[jadwal]" in prompt_lower
                and "tanggal" in prompt_lower
                and "ruangan" in prompt_lower
            )
            
            # STEP 1: Check if this is the initial request (ask for form)
            # Also reset if completed, so user can create another jadwal
            if (not state.get("jadwal_stage") or state.get("jadwal_stage") == "completed") and not is_jadwal_submission and action != "save_jadwal":
                # ── CEK APAKAH JADWAL SUDAH ADA DI DATABASE ──────────────────
                existing_check = check_existing_jadwal_by_context(user_id)
                if existing_check.get("exists"):
                    total     = existing_check.get("total", 0)
                    tgl_mulai = existing_check.get("tgl_mulai", "-")
                    tgl_selesai = existing_check.get("tgl_selesai", "-")
                    logger.info(f"[{user_id}] ℹ️  Jadwal sudah ada ({total} entries), memberitahu user")
                    state["result"] = (
                        f"<div style='padding:12px;border:1px solid #bbf7d0;border-radius:12px;"
                        f"background:#f0fdf4;'>"
                        f"<div style='display:flex;align-items:center;gap:8px;margin-bottom:8px;'>"
                        f"<span style='font-size:20px;'>✅</span>"
                        f"<strong style='color:#166534;font-size:15px;'>Jadwal Seminar Sudah Terbuat</strong>"
                        f"</div>"
                        f"<p style='margin:0 0 6px 0;color:#15803d;'>"
                        f"Sudah terdapat <strong>{total} jadwal seminar</strong> yang tersimpan "
                        f"di database untuk konteks Anda.</p>"
                        f"<p style='margin:0 0 10px 0;color:#4b5563;font-size:13px;'>"
                        f"📅 Periode: <strong>{tgl_mulai}</strong> s/d <strong>{tgl_selesai}</strong></p>"
                        f"<p style='margin:0;color:#6b7280;font-size:12px;'>"
                        f"Jika ingin membuat jadwal baru, silakan hapus jadwal yang sudah ada terlebih dahulu "
                        f"melalui halaman Data Jadwal Seminar.</p>"
                        f"</div>"
                    )
                    state["jadwal_stage"] = "completed"
                    state["sidebar_update"] = {"step": "jadwal", "status": "success"}
                    return state

                # Jadwal belum ada — tampilkan form input
                current_stage = state.get("jadwal_stage")
                logger.info(f"[{user_id}] ℹ️  jadwal_stage={current_stage}, showing input form")
                form_result = JadwalSeminarTools.get_form_jadwal()
                state["result"] = form_result.get("message")
                state["jadwal_stage"] = "input_form"
                state["ruangan_list"] = form_result.get("ruangan_list", [])
                logger.info(f"[{user_id}] ✓ Jadwal form displayed")
                return state
            
            # STEP 2: User submitted form, parse input dan buat jadwal
            elif state.get("jadwal_stage") in ("input_form", "preview") or is_jadwal_submission or action == "save_jadwal":
                logger.info(f"[{user_id}] ℹ️  parsing jadwal submission (stage={state.get('jadwal_stage')}, explicit_submit={is_jadwal_submission}, action={action})")
                
                if not dosen_context:
                    state["result"] = "❌ Dosen context tidak tersedia. Harap login terlebih dahulu."
                    logger.error(f"[{user_id}] ✗ Dosen context tidak ditemukan")
                    return state
                
                # Extract tanggal dari prompt (format: "[jadwal] tanggal: 15 mei 2026")
                tanggal_match = re.search(r'tanggal[:\s]+([^|]*?)(?:\||$)', prompt_lower)
                tanggal_str = tanggal_match.group(1).strip() if tanggal_match else None
                
                # Extract ruangan dari prompt (format: "[jadwal] ruangan: 1,2,3" atau "1")
                ruangan_match = re.search(r'ruangan[:\s]*([0-9,]+)', prompt_lower)
                ruangan_str = ruangan_match.group(1) if ruangan_match else None
                ruangan_list = [int(r.strip()) for r in ruangan_str.split(',') if r.strip().isdigit()] if ruangan_str else []
                
                # Extract durasi dari prompt (format: "[jadwal] durasi: 110") atau gunakan default
                durasi_match = re.search(r'durasi[:\s]*(\d+)', prompt_lower)
                durasi_menit = int(durasi_match.group(1)) if durasi_match else 110


                # ── FAST PATH: "Simpan ke Database" button from frontend ──────────────
                # action == "save_jadwal" comes BEFORE any tanggal/ruangan parsing
                # because at this point tanggal_mulai / kelompok_list are not yet set.
                if action == "save_jadwal":
                    # ── PRIMARY: parse semua data langsung dari prompt ──────────
                    # Frontend menyertakan order:, tanggal:, ruangan:, durasi:
                    # dalam string prompt saat tombol Simpan ditekan.
                    # Ini tidak bergantung pada state Python sama sekali.

                    # Parse kelompok_order dari prompt (format: "order: 7,9,2,5,...")
                    _order_match_prompt = re.search(r'order[:\s]*([0-9,]+)', prompt_lower)
                    _kelompok_order_from_prompt = (
                        [int(x.strip()) for x in _order_match_prompt.group(1).split(',') if x.strip().isdigit()]
                        if _order_match_prompt else []
                    )

                    # Parse tanggal dari prompt
                    _tanggal_match_save = re.search(r'tanggal[:\s]+([^|]*?)(?:\||$)', prompt_lower)
                    _tanggal_str_from_prompt = _tanggal_match_save.group(1).strip() if _tanggal_match_save else None

                    # Parse ruangan dari prompt
                    _ruangan_match_save = re.search(r'ruangan[:\s]*([0-9,]+)', prompt_lower)
                    _ruangan_list_from_prompt = (
                        [int(r.strip()) for r in _ruangan_match_save.group(1).split(',') if r.strip().isdigit()]
                        if _ruangan_match_save else []
                    )

                    # Parse durasi dari prompt
                    _durasi_match_save = re.search(r'durasi[:\s]*(\d+)', prompt_lower)
                    _durasi_from_prompt = int(_durasi_match_save.group(1)) if _durasi_match_save else None

                    # ── FALLBACK: gunakan jadwal_meta dari state / request_data ─
                    preview_meta = state.get("jadwal_meta") or {}

                    _tanggal_str_save   = _tanggal_str_from_prompt  or preview_meta.get("tanggal")
                    _ruangan_list_save  = _ruangan_list_from_prompt  or preview_meta.get("ruangan_list") or []
                    _durasi_menit_save  = _durasi_from_prompt        or preview_meta.get("durasi_menit") or 110
                    _kelompok_order_save = _kelompok_order_from_prompt or preview_meta.get("kelompok_order") or []

                    _tanggal_mulai_save = JadwalSeminarTools.parse_tanggal_input(_tanggal_str_save) if _tanggal_str_save else None

                    logger.info(
                        f"[{user_id}] [SAVE] kelompok_order={_kelompok_order_save}, "
                        f"tanggal={_tanggal_str_save}, ruangan={_ruangan_list_save}"
                    )

                    if not _tanggal_mulai_save or not _ruangan_list_save:
                        state["result"] = "❌ Data preview tidak ditemukan. Silakan buat jadwal preview terlebih dahulu."
                        logger.warning(f"[{user_id}] ✗ save_jadwal: missing preview metadata")
                        return state

                    _kelompok_list_save = JadwalSeminarTools.get_kelompok_for_jadwal(user_id, [dosen_context])
                    if not _kelompok_list_save:
                        state["result"] = "❌ Tidak ada kelompok yang sesuai untuk dijadwalkan."
                        logger.warning(f"[{user_id}] ✗ save_jadwal: no kelompok found")
                        return state

                    result = JadwalSeminarTools.generate_jadwal_seminar(
                        user_id=user_id,
                        tanggal_mulai=_tanggal_mulai_save,
                        durasi_menit=_durasi_menit_save,
                        ruangan_list=_ruangan_list_save,
                        kelompok_list=_kelompok_list_save,
                        dosen_context=[dosen_context],
                        persist=True,
                        shuffle_groups=False,
                        kelompok_order=_kelompok_order_save if _kelompok_order_save else None,
                    )

                    state["result"] = result.get("message")
                    if result.get("success"):
                        state["jadwal_stage"] = "completed"
                        state["jadwal_entries"] = result.get("jadwal_entries", [])
                        state["sidebar_update"] = {"step": "jadwal", "status": "success"}
                        logger.info(f"[{user_id}] ✓ save_jadwal persisted {result.get('total')} entries")
                    else:
                        logger.warning(f"[{user_id}] ✗ save_jadwal gagal: {result.get('message')}")
                    return state

                # ── Normal flow: parse prompt inputs ─────────────────────────────────
                # Default
                persist = False
                shuffle_groups = True
                kelompok_order = None

                # Kata kunci
                shuffle_keywords = [
                    "acak",
                    "acak ulang",
                    "shuffle",
                    "random"
                ]

                save_keywords = [
                    "simpan",
                    "save",
                    "persist"
                ]

                # Parse order explicit
                order_match = re.search(
                    r'order[:\s]*([0-9,]+)',
                    prompt_lower
                )

                if order_match:
                    kelompok_order = [
                        int(x.strip())
                        for x in order_match.group(1).split(',')
                        if x.strip().isdigit()
                    ]

                # Acak ulang
                if any(k in prompt_lower for k in shuffle_keywords):
                    shuffle_groups = True

                # Simpan via kata kunci teks
                if any(k in prompt_lower for k in save_keywords):
                    persist = True
                    shuffle_groups = False

                # Reuse preview metadata if the user is saving or reshuffling a previewed schedule.
                preview_meta = state.get("jadwal_meta") or {}
                if preview_meta:
                    if not tanggal_str:
                        tanggal_str = preview_meta.get("tanggal")
                    if not ruangan_list:
                        ruangan_list = preview_meta.get("ruangan_list") or []
                    if not durasi_menit:
                        durasi_menit = preview_meta.get("durasi_menit") or 110
                    if not kelompok_order:
                        kelompok_order = preview_meta.get("kelompok_order") or None

                logger.info(f"[{user_id}] Parsed: tanggal={tanggal_str}, ruangan_list={ruangan_list}, durasi={durasi_menit}")
                
                if not tanggal_str or not ruangan_list:
                    state["result"] = "❌ Tanggal dan minimal 1 ruangan harus dipilih. Mohon coba lagi."
                    logger.warning(f"[{user_id}] ✗ Missing required fields: tanggal={tanggal_str}, ruangan_list={ruangan_list}")
                    return state
                
                # Parse tanggal
                tanggal_mulai = JadwalSeminarTools.parse_tanggal_input(tanggal_str)
                if not tanggal_mulai:
                    state["result"] = f"❌ Format tanggal tidak valid: '{tanggal_str}'. Gunakan format: 15 mei 2026"
                    logger.warning(f"[{user_id}] ✗ Failed to parse tanggal: {tanggal_str}")
                    return state
                
                # Get kelompok yang sesuai dengan dosen context
                kelompok_list = JadwalSeminarTools.get_kelompok_for_jadwal(user_id, [dosen_context])
                if not kelompok_list:
                    state["result"] = "❌ Tidak ada kelompok yang sesuai untuk dijadwalkan pada konteks ini."
                    logger.warning(f"[{user_id}] ✗ No matching kelompok found")
                    return state
                
                logger.info(
                    f"[{user_id}] Generating jadwal: kelompok_order={kelompok_order}, "
                    f"shuffle={shuffle_groups}, persist={persist}"
                )
                    
                # Generate jadwal untuk setiap ruangan
                result = JadwalSeminarTools.generate_jadwal_seminar(
                    user_id=user_id,
                    tanggal_mulai=tanggal_mulai,
                    durasi_menit=durasi_menit,
                    ruangan_list=ruangan_list,
                    kelompok_list=kelompok_list,
                    dosen_context=[dosen_context],
                    persist=persist,
                    shuffle_groups=shuffle_groups,
                    kelompok_order=kelompok_order
                )
                
                state["result"] = result.get("message")
                if result.get("success"):
                    state["jadwal_meta"] = result.get("meta")
                    state["jadwal_actions"] = result.get("actions", state.get("jadwal_actions"))
                    # If not persisted, mark as preview so user can review / reshuffle / save
                    if result.get("persisted"):
                        state["jadwal_stage"] = "completed"
                        state["sidebar_update"] = {"step": "jadwal", "status": "success"}
                        logger.info(f"[{user_id}] ✓ generate_jadwal_seminar persisted: {result.get('total')} entries created")
                    else:
                        state["jadwal_stage"] = "preview"
                        state["sidebar_update"] = {"step": "jadwal", "status": "preview"}
                        # Hint to frontend it can allow save/acak actions
                        state["jadwal_actions"] = {"can_save": True, "can_acak": True}
                        logger.info(f"[{user_id}] ✓ generate_jadwal_seminar preview: {result.get('total')} entries generated (not persisted)")

                    state["jadwal_entries"] = result.get("jadwal_entries", [])
                else:
                    logger.warning(f"[{user_id}] ✗ generate_jadwal_seminar gagal: {result.get('message')}")
                    state["jadwal_stage"] = None  # Reset untuk coba lagi
                    state["jadwal_meta"] = None
                    state["sidebar_update"] = {"step": "jadwal", "status": "warning"}
                
                return state
        
        else:
            logger.info(f"[{user_id}] ⚠️  ACTION tidak dikenali: {action}")

            fallback_attempted = state.get("_executor_fallback_attempted", False)
            fallback_action = _infer_action_from_prompt(prompt_lower)
            if not fallback_attempted and fallback_action and fallback_action != action:
                logger.info(f"[{user_id}] 🔁 FALLBACK EXECUTION: retry with action={fallback_action}")
                state["_executor_fallback_attempted"] = True
                patched_plan = dict(plan) if isinstance(plan, dict) else {}
                patched_plan["action"] = fallback_action
                patched_plan.setdefault("source", "executor_fallback")
                state["plan"] = patched_plan
                return executor_node(state)

            logger.info(f"[{user_id}] 📄 CHAT: (tanpa tools / action tidak dikenali)")
            state["result"] = None

        state.pop("_executor_fallback_attempted", None)

        return state
    
    except Exception as e:
        user_id = state.get("user_id", "unknown")
        logger.error(f"[{user_id}] ❌ ERROR IN EXECUTOR_NODE")
        logger.error(f"Traceback:\n{traceback.format_exc()}")
        state["result"] = f"Error: {str(e)}"
        return state
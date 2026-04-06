from tools.db_tool import get_dosen_by_prodi_id, log as db_log


def assign_dosen(groups, dosen_list):

    result = []

    for i, g in enumerate(groups):
        dosen = dosen_list[i % len(dosen_list)]

        result.append({
            "kelompok": i+1,
            "dosen": dosen,
            "members": g
        })

    return result


# =========================
# GET DOSEN LIST BY PRODI_ID
# =========================
def get_dosen_list_by_prodi(prodi_id):
    """Get formatted list of dosen by prodi_id
    
    Returns:
        - If found: List of dosen with formatted structure for display
        - If not found: Error dict
    """
    
    dosen_list = get_dosen_by_prodi_id(prodi_id)
    
    if not dosen_list:
        return {
            "success": False,
            "message": f"Tidak ada dosen ditemukan untuk prodi_id={prodi_id}",
            "data": []
        }
    
    # Format for display
    formatted_dosen = []
    for dosen in dosen_list:
        formatted_dosen.append({
            "id": dosen.get("id"),
            "nama": dosen.get("nama"),
            "email": dosen.get("email", "-"),
            "prodi": dosen.get("prodi", "-"),
            "jabatan_akademik_desc": dosen.get("jabatan_akademik_desc", "-"),
            "nip": dosen.get("nip", "-"),
            "nidn": dosen.get("nidn", "-"),
            "jenjang_pendidikan": dosen.get("jenjang_pendidikan", "-"),
        })
    
    return {
        "success": True,
        "message": f"Ditemukan {len(formatted_dosen)} dosen",
        "total": len(formatted_dosen),
        "prodi_id": prodi_id,
        "data": formatted_dosen
    }


def format_dosen_for_response(dosen_data):
    """Format dosen list untuk response HTML dengan table sederhana"""
    
    if not dosen_data.get("success"):
        return f"""<div class="alert alert-warning" role="alert">Data Dosen Tidak Ditemukan: {dosen_data.get('message', 'Tidak ada data dosen ditemukan')}</div>"""
    
    dosen_list = dosen_data.get("data", [])
    
    if not dosen_list:
        return f"""<div class="alert alert-info" role="alert">{dosen_data.get('message', 'Tidak ada dosen ditemukan')}</div>"""
    
    # Build simple table
    html = f"""<div style="margin-bottom: 20px;"><p><strong>Daftar Dosen (Total: {dosen_data.get('total')})</strong></p>
<table class="table table-sm table-bordered" style="font-size: 13px; margin-bottom: 0;">
<thead><tr>
<th style="width: 5%;">#</th>
<th style="width: 20%;">Nama</th>
<th style="width: 25%;">Email</th>
<th style="width: 20%;">Jabatan</th>
<th style="width: 10%;">NIP</th>
<th style="width: 10%;">NIDN</th>
<th style="width: 10%;">Pendidikan</th>
</tr></thead>
<tbody>"""
    
    for idx, dosen in enumerate(dosen_list, 1):
        nama = dosen.get("nama", "-")
        email = dosen.get("email", "-")
        jabatan = dosen.get("jabatan_akademik_desc", "-")
        nip = dosen.get("nip", "-")
        nidn = dosen.get("nidn", "-")
        pendidikan = dosen.get("jenjang_pendidikan", "-")
        
        html += f"""<tr>
<td style="text-align: center;">{idx}</td>
<td>{nama}</td>
<td style="word-break: break-word; font-size: 12px;">{email}</td>
<td style="font-size: 12px;">{jabatan}</td>
<td style="font-size: 11px;">{nip}</td>
<td style="font-size: 11px;">{nidn}</td>
<td style="font-size: 12px;">{pendidikan}</td>
</tr>"""
    
    html += """</tbody>
</table>
</div>"""
    
    return html
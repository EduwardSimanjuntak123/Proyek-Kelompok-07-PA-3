"""HTML formatter untuk hasil grouping yang ditampilkan ke UI"""
from typing import List, Dict, Any, Optional


def format_messages(messages: List[str]) -> str:
    """
    Format list of messages menjadi HTML alert divs
    
    Args:
        messages: List of messages dengan emoji prefix (✅, ⚠️, ❌, 📈, 🔀)
        
    Returns:
        HTML string
    """
    if not messages:
        return ""
    
    html = "<div style='margin-bottom: 20px;'>"
    for msg in messages:
        if msg.startswith("✅"):
            css_class = "alert-success"
        elif msg.startswith("⚠️"):
            css_class = "alert-warning"
        elif msg.startswith("❌"):
            css_class = "alert-danger"
        elif msg.startswith("📈"):
            css_class = "alert-info"
        elif msg.startswith("🔀"):
            css_class = "alert-info"
        else:
            css_class = "alert-info"
        
        html += f"<div class='alert {css_class}' role='alert'>{msg}</div>"
    
    html += "</div>"
    return html


def format_score_based_grouping(
    groups: List[Dict[str, Any]],
    class_stats: Dict[str, Any]
) -> str:
    """Format score-based grouping result dengan statistik kelas"""
    html = ""
    
    # Display class statistics
    html += "<div style='background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>"
    html += "<h5>📈 Statistik Kelas:</h5>"
    html += f"<p><strong>Nilai Rata-rata Kelas:</strong> {class_stats.get('class_average', 0):.2f}</p>"
    html += f"<p><strong>Standar Deviasi:</strong> {class_stats.get('std_dev', 0):.2f}</p>"
    html += f"<p><strong>Range Nilai:</strong> {class_stats.get('min_score', 0):.2f} - {class_stats.get('max_score', 0):.2f}</p>"
    html += f"<p><strong>Total Mahasiswa:</strong> {class_stats.get('total_students', 0)}</p>"
    html += "</div>"
    
    html += "<h4>📊 Hasil Pembagian Kelompok Berdasarkan Nilai:</h4><br>"

    for group in groups:
        group_num = group.get("kelompok", 0)
        group_avg = group.get("group_average", 0)
        deviation = group.get("deviation_from_class", 0)
        deviation_color = "green" if abs(deviation) <= 0.5 else "orange"
        
        html += f"<h5>👥 Kelompok {group_num} (Nilai Rata-rata: {group_avg:.2f}, Deviasi: <span style='color:{deviation_color};'>{deviation:+.2f}</span>):</h5>"
        html += "<table class='table table-sm table-bordered'>"
        html += "<thead><tr><th>#</th><th>Nama</th><th>NIM</th><th>Nilai Rata-rata</th><th>Semester(s)</th></tr></thead><tbody>"

        for i, member in enumerate(group.get("members", []), 1):
            nama = member.get("nama", "-")
            nim = member.get("nim", "-")
            nilai = member.get("nilai_rata_rata", "-")
            semesters = ", ".join(map(str, member.get("semesters", [])))
            html += f"<tr><td>{i}</td><td>{nama}</td><td>{nim}</td><td>{nilai:.2f}</td><td>{semesters}</td></tr>"

        html += "</tbody></table><br>"

    return html


def format_regular_grouping(groups: List[Dict[str, Any]]) -> str:
    """Format regular grouping result tanpa score statistics"""
    html = "<h4>📊 Hasil Pembagian Kelompok:</h4><br>"

    for group in groups:
        html += f"<h5>👥 Kelompok {group['kelompok']}:</h5>"
        html += "<table class='table table-sm table-bordered'>"
        html += "<thead><tr><th>#</th><th>Nama</th><th>NIM</th></tr></thead><tbody>"

        for i, m in enumerate(group["members"], 1):
            if isinstance(m, dict):
                nama = m.get('nama', '-')
                nim = m.get('nim', '-')
            else:
                nama = str(m)
                nim = '-'
            html += f"<tr><td>{i}</td><td>{nama}</td><td>{nim}</td></tr>"

        html += "</tbody></table><br>"

    return html


def format_mahasiswa_list(data: List[Dict[str, Any]]) -> str:
    """Format daftar mahasiswa ke table HTML"""
    html = "<h4>📋 Daftar Mahasiswa:</h4><br>"
    html += "<table class='table table-sm table-bordered'>"
    html += "<thead><tr><th>#</th><th>Nama</th><th>NIM</th></tr></thead><tbody>"

    for i, m in enumerate(data, 1):
        if isinstance(m, dict):
            nama = m.get('nama', '-')
            nim = m.get('nim', '-')
        else:
            nama = str(m)
            nim = '-'
        html += f"<tr><td>{i}</td><td>{nama}</td><td>{nim}</td></tr>"

    html += "</tbody></table>"

    return html


def format_count(count: int) -> str:
    """Format jumlah mahasiswa"""
    return f"<p>🔢 Jumlah mahasiswa: <strong>{count}</strong></p>"


def format_result(data: Optional[Any], messages: Optional[List[str]] = None) -> str:
    """
    Main formatter yang route ke formatter spesifik sesuai data type
    
    Args:
        data: Result data dari executor
        messages: List of feedback messages
        
    Returns:
        HTML string representation
    """
    if messages is None:
        messages = []

    html = ""

    # Format messages first
    if messages:
        html += format_messages(messages)

    if data is None:
        html += "<p>Tidak ada data</p>"
        return html

    # Greeting response
    if isinstance(data, dict) and data.get("type") == "greeting":
        greeting_msg = data.get("message", "Halo! Ada yang bisa saya bantu?")
        html += f"<div class='alert alert-info' role='alert'><strong>🤖 {greeting_msg}</strong></div>"
        return html

    # Score-based grouping result (dict dengan 'groups' dan 'class_stats')
    if isinstance(data, dict) and "groups" in data and "class_stats" in data:
        html += format_score_based_grouping(
            groups=data.get("groups", []),
            class_stats=data.get("class_stats", {})
        )
        return html

    # Regular grouping result (list of groups with members)
    if isinstance(data, list) and data and "members" in data[0]:
        html += format_regular_grouping(data)
        return html

    # Mahasiswa list
    if isinstance(data, list):
        html += format_mahasiswa_list(data)
        return html

    # Count result
    if isinstance(data, int):
        html += format_count(data)
        return html

    # Fallback
    html += f"<p>{str(data)}</p>"
    return html

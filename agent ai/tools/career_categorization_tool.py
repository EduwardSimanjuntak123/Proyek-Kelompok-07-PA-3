"""
Tool untuk kategorisasi matakuliah dan scoring berdasarkan keahlian
Menggunakan LLM untuk menentukan kategori matakuliah dan assign mahasiswa ke keahlian
"""

from openai import OpenAI
from app.config import OPENAI_API_KEY
import json

client = OpenAI(api_key=OPENAI_API_KEY)

# Expertise categories (Jenis Keahlian)
EXPERTISE_CATEGORIES = ["Backend", "Frontend", "UI/UX", "Data AI"]


def categorize_subjects(subject_codes):
    """
    Gunakan LLM untuk kategorisasi matakuliah ke Backend, Frontend, UI/UX, atau Data AI
    
    Args:
        subject_codes: List of (kode_mk, nama_mk) tuples
    
    Returns:
        Dict: {subject_code: category}
    """
    
    if not subject_codes:
        return {}
    
    # Format subject codes for prompt
    subjects_str = "\n".join([f"- {code[0]}: {code[1]}" for code in subject_codes])
    
    prompt = f"""Kategorisasi matakuliah berikut ke salah satu kategori keahlian: Backend, Frontend, UI/UX, atau Data AI.

Panduan kategorisasi:
- Backend: Pengembangan API, Database, Server, sistem, arsitektur backend (Laravel, Node.js, Python, Java, dll)
- Frontend: Web Frontend, RPL, Mobile App, JavaScript, React, Vue, Flutter (Pengembangan Situs Web, Aplikasi Mobile)
- UI/UX: User Experience, Design, Interface, Prototyping, User Research
- Data AI: Machine Learning, Big Data, AI, Data Science, Analytics, Data Engineering

Matakuliah:
{subjects_str}

Berikan response dalam format JSON HANYA (tanpa penjelasan):
{{
    "KODE_MK1": "Backend",
    "KODE_MK2": "Frontend",
    ...
}}"""
    
    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "Anda adalah expert dalam kategorisasi matakuliah IT berdasarkan keahlian. Response hanya JSON."},
                {"role": "user", "content": prompt}
            ],
            temperature=0.3,
            max_tokens=1000
        )
        
        result_text = response.choices[0].message.content.strip()
        
        # Extract JSON dari response
        if result_text.startswith("```"):
            result_text = result_text.split("```")[1]
            if result_text.startswith("json"):
                result_text = result_text[4:]
        
        result_text = result_text.strip()
        categorization = json.loads(result_text)
        
        print(f"[CATEGORIZE] Matakuliah dikategorisasi ke {len(categorization)} keahlian")
        return categorization
        
    except json.JSONDecodeError as e:
        print(f"[ERROR] Failed to parse LLM response: {str(e)}")
        return {}
    except Exception as e:
        print(f"[ERROR] LLM categorization failed: {str(e)}")
        return {}


def score_student_by_expertise(student, subject_categorization):
    """
    Hitung skor mahasiswa per kategori keahlian berdasarkan nilai matakuliah
    
    Args:
        student: {mahasiswa_id, scores_by_semester, ...}
        subject_categorization: {kode_mk: category}
    
    Returns:
        {
            mahasiswa_id: str,
            nama: str,
            expertise_scores: {Backend: 85.5, Frontend: 78.3, UIUx: 88.2, DataAI: 72.1},
            primary_expertise: "Backend",
            average_score: 81.0
        }
    """
    
    if not student or not subject_categorization:
        return None
    
    student_data = student.get("student_data", {})
    scores_by_semester = student.get("scores_by_semester", {})
    
    # Hitung skor per kategori keahlian
    expertise_scores = {cat: {"nilai": [], "count": 0} for cat in EXPERTISE_CATEGORIES}
    
    for semester, sem_data in scores_by_semester.items():
        subject_codes = sem_data.get("kode_mk", [])
        values = sem_data.get("nilai", [])
        
        for code, value in zip(subject_codes, values):
            category = subject_categorization.get(code, "Data AI")  # Default ke Data AI
            expertise_scores[category]["nilai"].append(value)
            expertise_scores[category]["count"] += 1
    
    # Hitung rata-rata per kategori keahlian
    expertise_averages = {}
    for expertise, data in expertise_scores.items():
        if data["nilai"]:
            expertise_averages[expertise] = round(sum(data["nilai"]) / len(data["nilai"]), 2)
        else:
            expertise_averages[expertise] = 0
    
    # Tentukan primary expertise (nilai tertinggi)
    primary_expertise = max(expertise_averages, key=expertise_averages.get)
    
    return {
        "mahasiswa_id": student.get("mahasiswa_id"),
        "nama": student_data.get("nama", ""),
        "nim": student_data.get("nim", ""),
        "expertise_scores": expertise_averages,
        "primary_expertise": primary_expertise,
        "average_score": student.get("average_score", 0),
        "student_data": student_data
    }


def create_balanced_groups_by_expertise(students_with_expertise, group_size=4, avoid_pairs=None, must_pairs=None):
    """
    Buat kelompok seimbang berdasarkan kategori keahlian
    Setiap kelompok harus memiliki keberagaman keahlian
    
    Args:
        students_with_expertise: List of scored students by expertise
        group_size: Ukuran target kelompok
        avoid_pairs: List of [nama1, nama2] yang harus dipisah
        must_pairs: List of [nama1, nama2] yang harus bersama
    
    Returns:
        List of groups dengan balance info
    """
    
    if not students_with_expertise:
        return []
    
    from tools.grouping_tool import group_students_with_constraints
    
    # Group students by primary expertise
    expertise_groups = {cat: [] for cat in EXPERTISE_CATEGORIES}
    for student in students_with_expertise:
        primary = student.get("primary_expertise", "Data AI")
        expertise_groups[primary].append(student)
    
    print(f"[BALANCE] Mahasiswa dikelompokkan berdasarkan keahlian: Backend={len(expertise_groups['Backend'])}, Frontend={len(expertise_groups['Frontend'])}, UI/UX={len(expertise_groups['UI/UX'])}, Data AI={len(expertise_groups['Data AI'])}")
    
    # Calculate group count - ensure balanced distribution
    num_students = len(students_with_expertise)
    num_groups = max(1, num_students // group_size)
    
    # Calculate target group sizes
    base_group_size = num_students // num_groups
    remainder = num_students % num_groups
    
    print(f"[BALANCE] Membuat {num_groups} kelompok dari {num_students} mahasiswa: base_size={base_group_size}, remainder={remainder}")
    
    # Initialize groups dengan target size
    groups = [[] for _ in range(num_groups)]
    target_sizes = [base_group_size + (1 if i < remainder else 0) for i in range(num_groups)]
    
    print(f"[BALANCE] Ukuran target kelompok: {target_sizes}")
    
    # Interleave students dari setiap keahlian untuk diversity
    # Create a queue by cycling through expertise
    student_queue = []
    max_students_per_expertise = max(len(students) for students in expertise_groups.values())
    
    for round_idx in range(max_students_per_expertise):
        for expertise in EXPERTISE_CATEGORIES:
            if round_idx < len(expertise_groups[expertise]):
                student_queue.append(expertise_groups[expertise][round_idx])
    
    # Assign students ke groups menggunakan simple round-robin
    for student_idx, student in enumerate(student_queue):
        target_group = student_idx % num_groups
        groups[target_group].append(student)
    
    # Format output groups
    formatted_groups = []
    for idx, group in enumerate(groups, 1):
        if not group:  # Skip empty groups
            continue
            
        expertise_distribution = {cat: 0 for cat in EXPERTISE_CATEGORIES}
        group_data = []
        
        for student in group:
            primary_expertise = student.get("primary_expertise", "Data AI")
            expertise_distribution[primary_expertise] += 1
            
            group_data.append({
                "user_id": student.get("user_id"),
                "nama": student.get("nama"),
                "nim": student.get("nim"),
                "primary_expertise": primary_expertise,
                "expertise_scores": student.get("expertise_scores"),
                "average_score": student.get("average_score")
            })
        
        formatted_groups.append({
            "kelompok": len(formatted_groups) + 1,  # Renumber groups
            "members": group_data,
            "expertise_balance": expertise_distribution,
            "average_group_score": round(sum(s["average_score"] for s in group_data) / len(group_data), 2),
            "member_count": len(group_data)
        })
    
    return formatted_groups


def calculate_group_balance_metrics(groups):
    """
    Hitung metrik keseimbangan keahlian untuk semua kelompok
    
    Returns:
        {
            overall_balance_score: 0-100,
            expertise_distribution: {Backend: 5, Frontend: 5, ...},
            group_details: [...],
            homogeneous_groups: list of groups dengan hanya 1 keahlian
        }
    """
    
    if not groups:
        return None
    
    # Count expertise distribution across all groups
    total_expertise_distribution = {cat: 0 for cat in EXPERTISE_CATEGORIES}
    group_details = []
    homogeneous_groups = []  # Groups dengan hanya 1 keahlian
    
    for group in groups:
        balance = group.get("expertise_balance", {})
        expertise_in_group = [cat for cat in EXPERTISE_CATEGORIES if balance.get(cat, 0) > 0]
        
        # Check if group is homogeneous (only 1 expertise)
        if len(expertise_in_group) == 1:
            homogeneous_groups.append({
                "kelompok": group["kelompok"],
                "expertise": expertise_in_group[0],
                "member_count": group.get("member_count", 0)
            })
        
        for expertise in EXPERTISE_CATEGORIES:
            total_expertise_distribution[expertise] += balance.get(expertise, 0)
        
        group_details.append({
            "kelompok": group["kelompok"],
            "expertise_balance": balance,
            "expertise_diversity": len(expertise_in_group),  # Jumlah keahlian berbeda
            "avg_score": group.get("average_group_score", 0),
            "member_count": group.get("member_count", 0)
        })
    
    # Calculate balance score (0-100)
    # Penalti untuk homogeneous groups
    ideal_distribution = sum(total_expertise_distribution.values()) / len(EXPERTISE_CATEGORIES)
    variance = sum((v - ideal_distribution) ** 2 for v in total_expertise_distribution.values())
    balance_score = max(0, 100 - (variance * 5))
    
    # Tambahan penalti untuk setiap homogeneous group (-15 per group)
    homogeneity_penalty = len(homogeneous_groups) * 15
    balance_score = max(0, balance_score - homogeneity_penalty)
    
    warnings = []
    if homogeneous_groups:
        for hg in homogeneous_groups:
            warnings.append(f"[Peringatan] Kelompok {hg['kelompok']} hanya memiliki keahlian {hg['expertise']} ({hg['member_count']} mahasiswa)")
    
    return {
        "overall_balance_score": round(balance_score, 2),
        "total_students": sum(total_career_distribution.values()),
        "career_distribution": total_career_distribution,
        "group_details": group_details,
        "homogeneous_groups": homogeneous_groups,
        "warnings": warnings,
        "class_average": round(sum(g["avg_score"] for g in group_details) / len(group_details), 2)
    }

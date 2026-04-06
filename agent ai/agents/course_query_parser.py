"""
Course Query Detection and Parsing
"""
import re

def detect_course_query(prompt):
    """
    Detect jika user menanyakan tentang matakuliah/courses
    
    Examples:
    - "tampilkan matakuliah"
    - "lihat daftar matakuliah"
    - "apa aja matakuliah yang ada"
    - "berapa matakuliah"
    - "matakuliah semester 1"
    - "semester 2"
    """
    
    course_keywords = [
        "matakuliah", "matkuliah", "mata kuliah",
        "course", "courses", "subject", "subjects",
        "mk", "daftar mk",
        "kurikulum", "curriculum"
    ]
    
    action_keywords = [
        "tampilkan", "lihat", "show", "display",
        "apa aja", "apa saja", "ada apa",
        "berapa", "daftar", "semester"
    ]
    
    prompt_lower = prompt.lower().strip()
    
    # Check if has both course keyword and action keyword
    has_course = any(keyword in prompt_lower for keyword in course_keywords)
    has_action = any(keyword in prompt_lower for keyword in action_keywords)
    
    return has_course and has_action


def parse_course_query(prompt, context=None):
    """
    Parse course query and extract semester filter + show_rata_rata flag if present
    
    Returns:
        dict with action and context info
    """
    
    prompt_lower = prompt.lower()
    
    # Try to extract semester number (1-8)
    semester_match = re.search(r'semester\s+([1-8])', prompt_lower)
    semester_filter = None
    
    if semester_match:
        semester_filter = int(semester_match.group(1))
    
    # Check if user asked for rata-rata/nilai/average
    show_rata_rata_keywords = ['rata-rata', 'nilai', 'average', 'rata rata', 'ratarata']
    show_rata_rata = any(kw in prompt_lower for kw in show_rata_rata_keywords)
    
    return {
        "action": "get_courses",
        "type": "course_list",
        "context": context,
        "semester_filter": semester_filter,
        "show_rata_rata": show_rata_rata
    }

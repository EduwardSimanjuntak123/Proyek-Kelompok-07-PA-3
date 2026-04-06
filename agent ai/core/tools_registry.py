"""
Tool Registration Module - Registers all tool functions with the dynamic registry
"""

from core.registry import registry
from tools.db_tool import (
    get_mahasiswa_by_context,
    count_mahasiswa_by_context,
    save_groups_from_result
)
from tools.grouping_tool import grouping, group_students_with_constraints
from tools.grouping_enhanced import (
    group_by_score_with_size_constraint,
    group_by_scores_exact_size,
    validate_and_apply_constraints
)
from tools.score_tool import (
    get_student_scores_by_category,
    get_class_average,
    group_by_score_balance,
    group_by_score_balance_with_constraints
)
from tools.dosen_tool import get_dosen_list_by_prodi
from tools.pembimbing_assignment_tool import assign_pembimbing_automatically
from tools.career_categorization_tool import (
    categorize_subjects,
    score_student_by_expertise,
    create_balanced_groups_by_expertise
)


def register_all_tools():
    """Register all tool functions with the dynamic registry"""
    
    # ===========================
    # DATA RETRIEVAL ACTIONS
    # ===========================
    
    @registry.register(
        action_name="get_mahasiswa",
        executor_action="get_mahasiswa",
        depends_on=[],
        keywords=["ambil mahasiswa", "list mahasiswa", "daftar mahasiswa", "get students"],
        category="data_retrieval",
        description="Retrieve list of students/mahasiswa from database"
    )
    def _reg_get_mahasiswa(context):
        return get_mahasiswa_by_context(context)
    
    @registry.register(
        action_name="count_mahasiswa",
        executor_action="count_mahasiswa",
        depends_on=[],
        keywords=["hitung mahasiswa", "total mahasiswa", "jumlah mahasiswa"],
        category="data_retrieval",
        description="Count total number of students"
    )
    def _reg_count_mahasiswa(context):
        return count_mahasiswa_by_context(context)
    
    @registry.register(
        action_name="get_scores",
        executor_action="get_student_scores",
        depends_on=[],
        keywords=["ambil nilai", "get scores", "nilai mahasiswa"],
        category="data_retrieval",
        description="Get student scores by category"
    )
    def _reg_get_scores(students, kategori_pa):
        return get_student_scores_by_category(students, kategori_pa)
    
    @registry.register(
        action_name="get_class_average",
        executor_action="get_class_average",
        depends_on=["get_scores"],
        keywords=["rata-rata kelas", "class average", "average score"],
        category="data_retrieval",
        description="Calculate class average score"
    )
    def _reg_get_class_average(student_scores):
        return get_class_average(student_scores)
    
    @registry.register(
        action_name="get_dosen_list",
        executor_action="get_dosen_list",
        depends_on=[],
        keywords=["daftar dosen", "list dosen", "get lecturers"],
        category="data_retrieval",
        description="Get list of lecturers/dosen by prodi"
    )
    def _reg_get_dosen_list(prodi_id):
        return get_dosen_list_by_prodi(prodi_id)
    
    
    # ===========================
    # GROUPING ACTIONS (Score-Based)
    # ===========================
    
    @registry.register(
        action_name="group_by_score_balanced",
        executor_action="group_by_score_balanced",
        depends_on=["get_mahasiswa", "get_scores"],
        keywords=["kelompok nilai", "group by score", "group balanced"],
        category="grouping",
        description="Create balanced groups based on student scores"
    )
    def _reg_group_by_score_balanced(students, group_size=6, must_pairs=None, avoid_pairs=None):
        return group_by_score_with_size_constraint(
            students,
            group_size=group_size,
            must_pairs=must_pairs,
            avoid_pairs=avoid_pairs
        )
    
    @registry.register(
        action_name="group_by_exact_count",
        executor_action="group_by_exact_count",
        depends_on=["get_mahasiswa", "get_scores"],
        keywords=["kelompok exact", "exact groups", "jumlah kelompok"],
        category="grouping",
        description="Create exact number of groups with score balancing"
    )
    def _reg_group_by_exact_count(students, num_groups, must_pairs=None, avoid_pairs=None):
        return group_by_scores_exact_size(
            students,
            num_groups=num_groups,
            must_pairs=must_pairs,
            avoid_pairs=avoid_pairs
        )
    
    @registry.register(
        action_name="group_with_constraints",
        executor_action="group_with_constraints",
        depends_on=["get_mahasiswa"],
        keywords=["constraint", "harus satu kelompok", "jangan satu kelompok"],
        category="grouping",
        description="Group students with must/avoid pair constraints"
    )
    def _reg_group_with_constraints(students, group_size=6, must_pairs=None, avoid_pairs=None):
        return group_students_with_constraints(
            students,
            group_size=group_size,
            must_pairs=must_pairs,
            avoid_pairs=avoid_pairs
        )
    
    @registry.register(
        action_name="group_simple",
        executor_action="group_simple",
        depends_on=["get_mahasiswa"],
        keywords=["buat grup", "buat kelompok", "grouping"],
        category="grouping",
        description="Simple grouping without constraints"
    )
    def _reg_group_simple(students, group_size=6):
        return grouping(students, group_size)
    
    
    # ===========================
    # VALIDATION & MODIFICATION ACTIONS
    # ===========================
    
    @registry.register(
        action_name="validate_constraints",
        executor_action="validate_constraints",
        depends_on=[],
        keywords=["validasi", "check constraint", "validate groups"],
        category="validation",
        description="Validate existing groups against constraints"
    )
    def _reg_validate_constraints(groups, must_pairs=None, avoid_pairs=None):
        return validate_and_apply_constraints(
            groups,
            must_pairs=must_pairs,
            avoid_pairs=avoid_pairs
        )
    
    
    # ===========================
    # PERSISTENCE ACTIONS
    # ===========================
    
    @registry.register(
        action_name="save_groups",
        executor_action="save_groups",
        depends_on=["group_by_score_balanced"],
        keywords=["simpan kelompok", "save groups", "save result"],
        category="persistence",
        description="Save groups to database"
    )
    def _reg_save_groups(groups, context):
        return save_groups_from_result(groups, context)
    
    
    # ===========================
    # ASSIGNMENT ACTIONS
    # ===========================
    
    @registry.register(
        action_name="assign_pembimbing",
        executor_action="assign_pembimbing",
        depends_on=[],
        keywords=["assign pembimbing", "penugasan", "assign lecturer"],
        category="assignment",
        description="Automatically assign lecturers to groups"
    )
    def _reg_assign_pembimbing(prodi_id, kpa_id, tm_id, jabatan_filter=None):
        return assign_pembimbing_automatically(
            prodi_id,
            kpa_id,
            tm_id,
            jabatan_filter=jabatan_filter
        )
    
    
    # ===========================
    # CAREER-BASED GROUPING ACTIONS
    # ===========================
    
    @registry.register(
        action_name="categorize_subjects",
        executor_action="categorize_subjects",
        depends_on=["get_scores"],
        keywords=["kategori mata kuliah", "subject category", "expertise"],
        category="career_analysis",
        description="Categorize subjects by expertise area"
    )
    def _reg_categorize_subjects(student_scores):
        return categorize_subjects(student_scores)
    
    @registry.register(
        action_name="score_by_expertise",
        executor_action="score_by_expertise",
        depends_on=["categorize_subjects", "get_scores"],
        keywords=["skor keahlian", "expertise score", "skill score"],
        category="career_analysis",
        description="Score students by expertise/skill area"
    )
    def _reg_score_by_expertise(student_scores, categories):
        return score_student_by_expertise(student_scores, categories)
    
    @registry.register(
        action_name="group_by_expertise",
        executor_action="group_by_expertise",
        depends_on=["score_by_expertise"],
        keywords=["kelompok keahlian", "expertise group", "skill group"],
        category="career_analysis",
        description="Create balanced groups by expertise/career area"
    )
    def _reg_group_by_expertise(scored_students, group_size=6):
        return create_balanced_groups_by_expertise(scored_students, group_size)
    
    print("\n✅ All tools registered successfully!")
    from core.registry import print_registry_summary
    print_registry_summary()


# Call registration when module is imported
register_all_tools()

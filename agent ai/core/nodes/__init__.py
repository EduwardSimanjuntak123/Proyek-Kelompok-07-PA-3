"""
Package untuk semua nodes dalam LangGraph workflow.

Nodes adalah unit kerja individual yang menangani part spesifik dari workflow.
"""

from core.nodes.router import node_router
from core.nodes.simple_nodes import node_greeting, node_capability, node_question
from core.nodes.pembimbing_dosen_nodes import node_pembimbing, node_dosen
from core.nodes.data_nodes import node_view_groups, node_mahasiswa, node_scores, node_score_based_grouping
from core.nodes.save_delete_node import node_save_delete
from core.nodes.grouping_node import node_grouping
from core.nodes.advanced_query_nodes import (
    node_student_query,
    node_student_group_query,
    node_unscheduled_student_query,
    node_student_score_query,
    node_dosen_detail_query,
    node_group_pembimbing_status_query,
    node_student_matkul_query,
    node_student_manipulation,
    node_pembimbing_manipulation,
    node_multiple_instructions
)

# Level 4 enhancement nodes (Quality evaluation + Automatic recovery)
try:
    from core.nodes.evaluator_node import node_evaluator
    from core.nodes.replan_node import node_replan
    LEVEL_4_AVAILABLE = True
except ImportError:
    LEVEL_4_AVAILABLE = False
    node_evaluator = None
    node_replan = None

__all__ = [
    "node_router",
    "node_greeting",
    "node_capability",
    "node_question",
    "node_pembimbing",
    "node_dosen",
    "node_view_groups",
    "node_mahasiswa",
    "node_scores",
    "node_save_delete",
    "node_grouping",
    # Advanced query nodes (10 new nodes)
    "node_student_query",
    "node_student_group_query",
    "node_unscheduled_student_query",
    "node_student_score_query",
    "node_dosen_detail_query",
    "node_group_pembimbing_status_query",
    "node_student_matkul_query",
    "node_student_manipulation",
    "node_pembimbing_manipulation",
    "node_multiple_instructions",
    # Level 4 nodes (if available)
    "node_evaluator",
    "node_replan",
]


"""
LangGraph Orchestration - Main workflow graph untuk PA Agent.

File ini mengatur:
1. StateGraph definition
2. Node registration
3. Edge definition (conditional routing)
4. Graph compilation
"""

from langgraph.graph import StateGraph, END
from core.state import AgentState
from core.nodes import (
    node_router,
    node_greeting,
    node_capability,
    node_question,
    node_pembimbing,
    node_dosen,
    node_view_groups,
    node_mahasiswa,
    node_scores,
    node_score_based_grouping,
    node_save_delete,
    node_grouping,
    # Advanced query nodes
    node_student_query,
    node_student_group_query,
    node_unscheduled_student_query,
    node_student_score_query,
    node_dosen_detail_query,
    node_group_pembimbing_status_query,
    node_student_matkul_query,
    node_student_manipulation,
    node_pembimbing_manipulation,
    node_multiple_instructions,
)
# Import new nodes for Level 4 enhancement
try:
    from core.nodes.evaluator_node import node_evaluator
    from core.nodes.replan_node import node_replan
    ADVANCED_NODES_AVAILABLE = True
except ImportError:
    print("[GRAPH] Warning: Advanced nodes (evaluator, replan) not available")
    ADVANCED_NODES_AVAILABLE = False


def create_agent_graph():
    """
    Membuat dan mengkompilasi LangGraph workflow.
    
    Struktur graph:
    - Router Node (awal, menentukan route)
    - 10+ handler nodes (processing specific requests)
    - Edges (routing logic antara nodes)
    - END (terminal node)
    
    Returns:
        CompiledGraph: Compiled LangGraph yang siap dijalankan
    """
    
    print("[GRAPH] Initializing LangGraph workflow...")
    
    # ========== 1. CREATE STATE GRAPH ==========
    # StateGraph mengelola state flow antara nodes
    graph = StateGraph(AgentState)
    
    # ========== 2. ADD ROUTER NODE ==========
    # Router adalah entry point yang menentukan path
    graph.add_node("router", node_router)
    
    # ========== 3. ADD HANDLER NODES ==========
    # Simple request handlers (no data fetching)
    graph.add_node("node_greeting", node_greeting)
    graph.add_node("node_capability", node_capability)
    graph.add_node("node_question", node_question)
    
    # Pembimbing & Dosen handlers
    graph.add_node("node_pembimbing", node_pembimbing)
    graph.add_node("node_dosen", node_dosen)
    
    # Data retrieval handlers
    graph.add_node("node_view_groups", node_view_groups)
    graph.add_node("node_mahasiswa", node_mahasiswa)
    graph.add_node("node_scores", node_scores)
    graph.add_node("node_score_based_grouping", node_score_based_grouping)
    
    # Save/Delete handlers
    graph.add_node("node_save_delete", node_save_delete)
    
    # Grouping handler (kompleks)
    graph.add_node("node_grouping", node_grouping)
    
    # Advanced query handlers (10 new nodes)
    graph.add_node("node_student_query", node_student_query)
    graph.add_node("node_student_group_query", node_student_group_query)
    graph.add_node("node_unscheduled_student_query", node_unscheduled_student_query)
    graph.add_node("node_student_score_query", node_student_score_query)
    graph.add_node("node_dosen_detail_query", node_dosen_detail_query)
    graph.add_node("node_group_pembimbing_status_query", node_group_pembimbing_status_query)
    graph.add_node("node_student_matkul_query", node_student_matkul_query)
    graph.add_node("node_student_manipulation", node_student_manipulation)
    graph.add_node("node_pembimbing_manipulation", node_pembimbing_manipulation)
    graph.add_node("node_multiple_instructions", node_multiple_instructions)
    
    # ========== LEVEL 4 ENHANCEMENT NODES ==========
    # Add evaluator & replan nodes for autonomous recovery + quality feedback
    if ADVANCED_NODES_AVAILABLE:
        graph.add_node("node_evaluator", node_evaluator)
        graph.add_node("node_replan", node_replan)
        print(f"[GRAPH] ✓ Added Level 4 nodes: evaluator (quality check), replan (auto recovery)")
    
    print(f"[GRAPH] ✓ Added 21+ nodes (11 original + 10 advanced + Level 4 nodes)")
    
    # ========== 4. SET ENTRY POINT ==========
    # Router adalah node pertama yang dijalankan
    graph.set_entry_point("router")
    
    print(f"[GRAPH] ✓ Set entry point: router")
    
    # ========== 5. ADD CONDITIONAL EDGES ==========
    # Conditional edges menentukan routing berdasarkan router output
    def route_from_router(state: AgentState) -> str:
        """
        Function untuk menentukan next node berdasarkan detected_type.
        
        Ini dipanggil setelah router node selesai.
        """
        
        next_action = state.get("next_action")
        
        print(f"[ROUTING] Next action: {next_action}")
        
        # Default ke END jika tidak ada next_action
        return next_action or END
    
    # Add conditional edges dari router
    path_map_dict = {
        "node_greeting": "node_greeting",
        "node_capability": "node_capability",
        "node_question": "node_question",
        "node_pembimbing": "node_pembimbing",
        "node_dosen": "node_dosen",
        "node_view_groups": "node_view_groups",
        "node_mahasiswa": "node_mahasiswa",
        "node_scores": "node_scores",
        "node_score_based_grouping": "node_score_based_grouping",
        "node_save_delete": "node_save_delete",
        "node_grouping": "node_grouping",
        "node_student_query": "node_student_query",
        "node_student_group_query": "node_student_group_query",
        "node_unscheduled_student_query": "node_unscheduled_student_query",
        "node_student_score_query": "node_student_score_query",
        "node_dosen_detail_query": "node_dosen_detail_query",
        "node_group_pembimbing_status_query": "node_group_pembimbing_status_query",
        "node_student_matkul_query": "node_student_matkul_query",
        "node_student_manipulation": "node_student_manipulation",
        "node_pembimbing_manipulation": "node_pembimbing_manipulation",
        "node_multiple_instructions": "node_multiple_instructions",
        END: END,
    }
    
    # Add Level 4 nodes to path map if available
    if ADVANCED_NODES_AVAILABLE:
        path_map_dict["node_evaluator"] = "node_evaluator"
        path_map_dict["node_replan"] = "node_replan"
    
    graph.add_conditional_edges(
        source="router",
        path=route_from_router,
        path_map=path_map_dict
    )
    
    print(f"[GRAPH] ✓ Added conditional edges from router")
    
    # ========== 6. ADD EDGES: HANDLERS → QUALITY EVALUATION → END ==========
    # For Level 4: evaluation before output (quality check + potential retry/replan)
    
    if ADVANCED_NODES_AVAILABLE:
        # Simple nodes (greeting, capability, question) → END (no need for evaluation)
        graph.add_edge("node_greeting", END)
        graph.add_edge("node_capability", END)
        graph.add_edge("node_question", END)
        
        # Data processing nodes → EVALUATOR → (END or REPLAN)
        # These nodes produce output that needs quality check
        graph.add_edge("node_pembimbing", "node_evaluator")
        graph.add_edge("node_dosen", "node_evaluator")
        graph.add_edge("node_view_groups", "node_evaluator")
        graph.add_edge("node_mahasiswa", "node_evaluator")
        graph.add_edge("node_scores", "node_evaluator")
        graph.add_edge("node_score_based_grouping", "node_evaluator")
        graph.add_edge("node_save_delete", "node_evaluator")
        graph.add_edge("node_grouping", "node_evaluator")
        
        # Advanced query nodes → EVALUATOR
        graph.add_edge("node_student_query", "node_evaluator")
        graph.add_edge("node_student_group_query", "node_evaluator")
        graph.add_edge("node_unscheduled_student_query", "node_evaluator")
        graph.add_edge("node_student_score_query", "node_evaluator")
        graph.add_edge("node_dosen_detail_query", "node_evaluator")
        graph.add_edge("node_group_pembimbing_status_query", "node_evaluator")
        graph.add_edge("node_student_matkul_query", "node_evaluator")
        graph.add_edge("node_student_manipulation", "node_evaluator")
        graph.add_edge("node_pembimbing_manipulation", "node_evaluator")
        graph.add_edge("node_multiple_instructions", "node_evaluator")
        
        print(f"[GRAPH] ✓ Added multi-hop routing: handlers → evaluator (Level 4)")
        
        # === Evaluator conditional routing ===
        def route_from_evaluator(state: AgentState) -> str:
            """
            Route dari evaluator berdasarkan quality check result.
            - Jika quality OK → END (output ready)
            - Jika quality rendah → REPLAN (attempt recovery)
            """
            evaluation = state.get("evaluation", {})
            needs_retry = evaluation.get("needs_retry", False)
            
            if needs_retry:
                print(f"[EVALUATOR→ROUTING] Quality low, routing to replan")
                return "node_replan"
            else:
                print(f"[EVALUATOR→ROUTING] Quality OK, routing to END")
                return END
        
        graph.add_conditional_edges(
            source="node_evaluator",
            path=route_from_evaluator,
            path_map={
                "node_replan": "node_replan",
                END: END
            }
        )
        
        print(f"[GRAPH] ✓ Added conditional edges from evaluator → END or REPLAN")
        
        # === Replan routing ===
        def route_from_replan(state: AgentState) -> str:
            """
            Route dari replan node.
            - Jika new steps generated → back to executor
            - Jika fallback used → END (send best effort response)
            """
            replan_info = state.get("replan_info", {})
            fallback_used = replan_info.get("fallback_used", False)
            
            if fallback_used or not replan_info.get("new_steps"):
                print(f"[REPLAN→ROUTING] Fallback used, routing to END")
                return END
            else:
                next_action = state.get("next_action")
                print(f"[REPLAN→ROUTING] New plan generated, routing to {next_action}")
                return next_action or END
        
        graph.add_conditional_edges(
            source="node_replan",
            path=route_from_replan,
            path_map={
                "node_executor": "node_grouping",  # Re-execute with new plan (via grouping handler)
                END: END
            }
        )
        
        print(f"[GRAPH] ✓ Added conditional edges from replan → executor or END")
    
    else:
        # Fallback: all nodes → END (Level 3.4 mode without evaluation)
        print(f"[GRAPH] ⚠ Level 4 nodes not available, using fallback (direct → END)")
        
        graph.add_edge("node_greeting", END)
        graph.add_edge("node_capability", END)
        graph.add_edge("node_question", END)
        graph.add_edge("node_pembimbing", END)
        graph.add_edge("node_dosen", END)
        graph.add_edge("node_view_groups", END)
        graph.add_edge("node_mahasiswa", END)
        graph.add_edge("node_scores", END)
        graph.add_edge("node_score_based_grouping", END)
        graph.add_edge("node_save_delete", END)
        graph.add_edge("node_grouping", END)
        
        # Edges dari 10 advanced query nodes ke END
        graph.add_edge("node_student_query", END)
        graph.add_edge("node_student_group_query", END)
        graph.add_edge("node_unscheduled_student_query", END)
        graph.add_edge("node_student_score_query", END)
        graph.add_edge("node_dosen_detail_query", END)
        graph.add_edge("node_group_pembimbing_status_query", END)
        graph.add_edge("node_student_matkul_query", END)
        graph.add_edge("node_student_manipulation", END)
        graph.add_edge("node_pembimbing_manipulation", END)
        graph.add_edge("node_multiple_instructions", END)
        
        print(f"[GRAPH] ✓ Added direct edges to END from all 21 handler nodes")
    
    # ========== 7. COMPILE GRAPH ==========
    # Compile mengoptimalkan graph untuk execution
    compiled_graph = graph.compile()
    
    print(f"[GRAPH] ✓ Graph compiled successfully!")
    
    return compiled_graph


# ========== Lazy initialization ==========
# Initialize graph sekali saja (singleton pattern)
_agent_graph = None


def get_agent_graph():
    """
    Mendapatkan compiled agent graph (singleton).
    
    Inisialisasi hanya dilakukan sekali untuk performance.
    """
    
    global _agent_graph
    
    if _agent_graph is None:
        print("[GRAPH] Creating new graph instance (first call)...")
        _agent_graph = create_agent_graph()
    
    return _agent_graph

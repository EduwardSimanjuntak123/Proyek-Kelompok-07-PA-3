from langgraph.graph import StateGraph, END
import logging
import traceback
from core.state import AgentState

from nodes.question_node import question_node
from nodes.planner_node import planner_node
from nodes.executor_node import executor_node
from nodes.answer_node import answer_node

# Configure logging for main module
logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)


def _trim_messages(messages, max_messages: int = 8):
    if not messages:
        return []

    trimmed = []
    for message in messages[-max_messages:]:
        if isinstance(message, dict):
            trimmed.append(message)
    return trimmed


def should_continue(state):
    last_user = state["messages"][-2]["content"]

    if last_user.lower() == "exit":
        return "end"
    return "continue"


builder = StateGraph(AgentState)

builder.add_node("question", question_node)
builder.add_node("planner", planner_node)
builder.add_node("executor", executor_node)
builder.add_node("answer", answer_node)

builder.set_entry_point("question")

builder.add_edge("question", "planner")
builder.add_edge("planner", "executor")
builder.add_edge("executor", "answer")

# For CLI mode: no looping (just run through once)
# For API mode: looping is fine since prompt comes from API request
builder.add_edge("answer", END)

# Keeping the old conditional-looping version commented out:
# builder.add_conditional_edges(
#     "answer",
#     should_continue,
#     {
#         "continue": "question",
#         "end": END
#     }
# )

graph = builder.compile()


def run_agent_chat(prompt: str, user_id: int, dosen_context: list = None, conversation_history: list = None):
    """
    Jalankan agent untuk chat dengan LLM yang model-aware
    User bebas input apapun di UI - agent bisa menjawab tentang models/schema
    
    Args:
        prompt: Prompt/pertanyaan dari user
        user_id: User ID dari session untuk tracking conversation
        dosen_context: Context dosen (user_id, prodi, role, kategori_pa, etc)
        conversation_history: History percakapan sebelumnya (optional)
    
    Returns:
        Dictionary dengan hasil:
        - result: Respons LLM (model-aware jika user tanya tentang schema)
        - success: Status berhasil/error
    """
    import uuid
    
    try:
        user_id_str = str(user_id)
        logger.info(f"[{user_id_str}] 🚀 Agent: '{prompt[:60]}...'")
        
        # Initialize session
        session_id = str(uuid.uuid4())
        
        # Build initial state
        initial_messages = _trim_messages(conversation_history or [])
        initial_messages.append({
            "role": "user",
            "content": prompt,
            "timestamp": str(uuid.uuid4())
        })
        
        # Create state dengan dosen context (untuk model awareness)
        state = {
            "messages": initial_messages,
            "plan": None,
            "result": None,
            "grouping_payload": None,
            "grouping_meta": None,
            "pembimbing_payload": None,
            "pembimbing_meta": None,
            "penguji_payload": None,
            "penguji_meta": None,
            "excel_file_path": None,
            "excel_filename": None,
            "user_id": user_id_str,
            "session_id": session_id,
            "context": {
                "dosen_context": dosen_context or [],
                "type": "chat"
            }
        }
        
        # Invoke graph - akan go through planner -> executor -> answer
        result = graph.invoke(state)
        
        # Extract result
        response_text = result.get("messages", [])[-1].get("content", "Tidak ada respons") if result.get("messages") else "Tidak ada respons"
        
        logger.info(f"[{user_id_str}] ✓ Sukses ({len(response_text)} chars)")
        return {
            "result": response_text,
            "success": True,
            "action": (result.get("plan") or {}).get("action"),
            "grouping_payload": result.get("grouping_payload"),
            "grouping_meta": result.get("grouping_meta"),
            "pembimbing_payload": result.get("pembimbing_payload"),
            "pembimbing_meta": result.get("pembimbing_meta"),
            "penguji_payload": result.get("penguji_payload"),
            "penguji_meta": result.get("penguji_meta"),
            "excel_file_path": result.get("excel_file_path"),
            "excel_filename": result.get("excel_filename"),
        }
    
    except Exception as e:
        user_id_str = str(user_id)
        logger.error(f"[{user_id_str}] ❌ FATAL ERROR IN run_agent_chat")
        logger.error(f"[{user_id_str}] Error: {str(e)}")
        logger.error(f"[{user_id_str}] Traceback:\n{traceback.format_exc()}")
        return {
            "result": f"Error: {str(e)}",
            "success": False,
            "error": str(e)
        }


if __name__ == "__main__":
    import uuid
    from core.memory import ConversationMemory
    
    # CLI mode - ask user once and run agent once
    print("\n=== Agent AI Kelompok (CLI Mode) ===")
    print("(Use API via Laravel for full functionality)\n")
    logger.info("=" * 80)
    logger.info("CLI MODE STARTED")
    logger.info("=" * 80)
    
    user_id = input("User ID (or press Enter for 'default'): ").strip() or "default"
    logger.info(f"User ID: {user_id}")
    
    prompt = input(f"\nYou: ").strip()
    logger.info(f"Prompt: {prompt[:150]}...")
    
    if not prompt:
        print("No prompt provided. Exiting.")
        logger.warning("No prompt provided - exiting")
        exit()
    
    # Initialize memory
    logger.debug(f"Initializing conversation memory for user {user_id}")
    memory = ConversationMemory(user_id)
    
    # Create state with single input
    logger.debug("Creating state object...")
    state = {
        "messages": [{
            "role": "user",
            "content": prompt
        }],
        "plan": None,
        "result": None,
        "user_id": user_id,
        "session_id": str(uuid.uuid4()),
        "context": {}
    }
    
    # Run ONE complete agent cycle (question → plan → execute → answer)
    # Do NOT loop - just run through the graph once
    print("\n🤖 AI is thinking...\n")
    logger.info("🚀 Invoking graph...")
    
    try:
        # Run only the forward path without conditional looping
        result = graph.invoke(state)
        logger.info("✓ Graph execution completed")
        
        # Display result
        if result.get("result"):
            print("AI:", result["result"])
            logger.info(f"AI Response: {result['result'][:150]}...")
        else:
            print("No response from AI")
            logger.warning("No AI response generated")
        
        # Save to memory
        logger.debug("Saving conversation to memory...")
        memory.save_conversation(result.get("messages", []))
        print("\n✅ Conversation saved to history.")
        logger.info("✓ Conversation saved to memory")
        logger.info("=" * 80)
        logger.info("CLI MODE COMPLETED SUCCESSFULLY")
        logger.info("=" * 80)
        
    except Exception as e:
        logger.error("❌ ERROR DURING GRAPH EXECUTION")
        logger.error(f"Error: {str(e)}")
        logger.error(f"Traceback:\n{traceback.format_exc()}")
        print(f"\n❌ Error: {str(e)}")
        logger.info("=" * 80)
        logger.info("CLI MODE FAILED")
        logger.info("=" * 80)
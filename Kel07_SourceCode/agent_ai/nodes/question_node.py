import logging
from core.memory import ConversationMemory

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

def question_node(state):
    """
    Node untuk menerima input dari user
    Load conversation history dari memory
    
    Konfigurasi:
    - Jika running via API: prompt sudah di state["messages"], skip input()
    - Jika running via CLI: minta input dari terminal
    """
    try:
        user_id = state.get("user_id", "default")
        logger.info(f"[{user_id}] ❓ QUESTION_NODE: Starting...")
        
        # Load conversation history
        logger.debug(f"[{user_id}] Loading conversation history from memory...")
        memory = ConversationMemory(user_id)
        previous_messages = memory.load_conversation()
        logger.debug(f"[{user_id}] Loaded {len(previous_messages)} previous messages")
        
        # Jika ada history dan messages belum multi, load ke state
        if previous_messages and len(state.get("messages", [])) <= 1:
            state["messages"] = _trim_messages(previous_messages)
            logger.debug(f"[{user_id}] Restored conversation history to state")
        
        # Check apakah last message sudah user input (dari API)
        messages = state.get("messages", [])
        last_message_is_user = (messages and 
                                messages[-1].get("role") == "user" and 
                                messages[-1].get("content"))
        
        logger.debug(f"[{user_id}] User input already in state - API mode")
        
        # Simpan ke memory
        logger.debug(f"[{user_id}] Saving conversation to memory...")
        memory.save_conversation(state["messages"])
        logger.info(f"[{user_id}] ✓ QUESTION_NODE: Complete ({len(state['messages'])} messages)")
        
        return state
        
    except Exception as e:
        logger.error(f"[{state.get('user_id', 'unknown')}] ❌ ERROR IN QUESTION_NODE")
        logger.error(f"Traceback: {str(e)}")
        raise
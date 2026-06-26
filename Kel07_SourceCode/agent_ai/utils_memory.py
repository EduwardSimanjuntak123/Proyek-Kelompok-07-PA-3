"""
Utility script untuk manage user context dan conversation memory
"""

from core.memory import ConversationMemory, SemanticMemory
import json

def set_user_context(user_id: str, context_data: dict) -> bool:
    """
    Set user context (nama, prodi, tahun akademik, dll)
    
    Contoh:
    set_user_context("user123", {
        "nama": "Budi",
        "prodi": "Informatika", 
        "tahun_akademik": "2023/2024",
        "tahun_masuk": 2021
    })
    """
    semantic_memory = SemanticMemory(user_id)
    return semantic_memory.save_context(context_data)


def update_user_context(user_id: str, key: str, value) -> bool:
    """
    Update satu field dalam user context
    """
    semantic_memory = SemanticMemory(user_id)
    return semantic_memory.update_context(key, value)


def get_user_context(user_id: str) -> dict:
    """
    Get semua user context
    """
    semantic_memory = SemanticMemory(user_id)
    return semantic_memory.load_context()


def view_conversation_history(user_id: str, limit: int = None) -> None:
    """
    Print conversation history untuk user
    """
    memory = ConversationMemory(user_id)
    messages = memory.load_conversation()
    
    if not messages:
        print(f"No conversation history found for user: {user_id}")
        return
    
    if limit:
        messages = messages[-limit:]
    
    print(f"\n=== Conversation History for {user_id} ===")
    for i, msg in enumerate(messages, 1):
        role = msg.get("role", "unknown").upper()
        content = msg.get("content", "")
        print(f"\n[{i}] {role}:")
        print(f"{content}")
    print("\n" + "="*50)


def clear_conversation(user_id: str) -> bool:
    """
    Hapus conversation history untuk user
    """
    memory = ConversationMemory(user_id)
    return memory.clear_conversation()


def get_conversation_stats(user_id: str) -> dict:
    """
    Get statistics tentang conversation user
    """
    memory = ConversationMemory(user_id)
    stats = memory.get_conversation_summary()
    return stats


if __name__ == "__main__":
    # Example usage
    user_id = "student_001"
    
    # Set user context
    print("Setting user context...")
    set_user_context(user_id, {
        "nama": "Budi Santoso",
        "prodi": "Informatika",
        "tahun_akademik": "2023/2024",
        "tahun_masuk": 2021,
        "semester": 6
    })
    
    # View conversation history
    print("\nViewing conversation history...")
    view_conversation_history(user_id)
    
    # Get stats
    print("\nConversation stats:")
    stats = get_conversation_stats(user_id)
    print(json.dumps(stats, indent=2, ensure_ascii=False))

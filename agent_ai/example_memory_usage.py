"""
Example usage of the new 3-tier memory system
"""

from core.memory import (
    ConversationMemory, 
    SemanticMemory, 
    LongTermMemory,
    SHORT_TERM_CHECKPOINTER,
    LONG_TERM_STORE
)

def example_setup_user():
    """Setup user dengan all 3 memory tiers"""
    user_id = "student_001"
    
    print("\n" + "="*60)
    print("EXAMPLE 1: Setup User dengan All Memory Tiers")
    print("="*60)
    
    # 1. Setup Medium-Term Memory (Semantic)
    print("\n[MEDIUM-TERM] Setting user context...")
    semantic_mem = SemanticMemory(user_id)
    semantic_mem.save_context({
        "nama": "Budi Santoso",
        "prodi": "Informatika",
        "tahun_akademik": "2023/2024",
        "semester": 6,
        "advisor": "Dr. Ahmad"
    })
    print("✓ User context saved")
    print(f"Context: {semantic_mem.load_context()}")
    
    # 2. Setup Long-Term Memory (Store)
    print("\n[LONG-TERM] Saving entities to knowledge store...")
    long_term = LongTermMemory(user_id, LONG_TERM_STORE)
    
    # Save mahasiswa entity
    long_term.save_entity(
        "mahasiswa",
        "2110511001",
        {
            "nama": "Budi Santoso",
            "npm": "2110511001",
            "prodi": "Informatika",
            "semester": 6
        }
    )
    print("✓ Mahasiswa entity saved")
    
    # Save advisor entity
    long_term.save_entity(
        "advisor",
        "ahmad001",
        {
            "nama": "Dr. Ahmad",
            "specialization": "Web Development"
        }
    )
    print("✓ Advisor entity saved")
    
    # 3. Save facts
    print("\n[LONG-TERM] Saving knowledge facts...")
    long_term.save_fact(
        "qa_history",
        "Q: Apa itu kepemimpinan?\nA: Kemampuan untuk mengarahkan orang lain",
        {"topic": "leadership", "confidence": 0.95}
    )
    long_term.save_fact(
        "query_log",
        "User asked: Bagaimana cara register PA3?",
        {"action": "query_data", "timestamp": "2024-04-12"}
    )
    print("✓ Knowledge facts saved")


def example_conversation_flow():
    """Simulasi conversation flow dengan memory"""
    user_id = "student_001"
    
    print("\n" + "="*60)
    print("EXAMPLE 2: Conversation Flow dengan Memory")
    print("="*60)
    
    # Short-term checkpoint (biasa di-handle oleh LangGraph secara otomatis)
    print("\n[SHORT-TERM] Session checkpoint via thread_id...")
    thread_id = str(hash(user_id))
    print(f"✓ Thread ID: {thread_id}")
    print("✓ InMemorySaver akan checkpoint state setiap step")
    
    # Medium-term: Load conversation history
    print("\n[MEDIUM-TERM] Loading conversation history...")
    conv_mem = ConversationMemory(user_id)
    history = conv_mem.load_conversation()
    print(f"✓ Loaded {len(history)} previous messages")
    
    # Add new message
    conv_mem.add_message(
        "user",
        "Saya ingin tahu status pembimbingan PA3 saya"
    )
    print("✓ User message saved to conversation history")
    
    # Planner uses long-term facts
    print("\n[LONG-TERM] Using knowledge store in planner...")
    long_term = LongTermMemory(user_id, LONG_TERM_STORE)
    
    # Retrieve previous questions
    qa_facts = long_term.get_facts("qa_history", limit=5)
    print(f"✓ Retrieved {len(qa_facts)} previous QA pairs")
    
    # Search entities
    mahasiswa = long_term.get_entity("mahasiswa", "2110511001")
    if mahasiswa:
        print(f"✓ Found mahasiswa: {mahasiswa['data']['nama']}")
    
    # Answer saves facts
    print("\n[LONG-TERM] Saving answer back to knowledge store...")
    long_term.save_fact(
        "qa_history",
        "Q: Status pembimbingan PA3?\nA: Sudah berkomunikasi dengan advisor",
        {"topic": "PA3 progress", "resolved": True}
    )
    print("✓ Answer fact saved")


def example_retrieve_knowledge():
    """Retrieve knowledge dari long-term store"""
    user_id = "student_001"
    
    print("\n" + "="*60)
    print("EXAMPLE 3: Retrieve Knowledge dari Long-Term Store")
    print("="*60)
    
    long_term = LongTermMemory(user_id, LONG_TERM_STORE)
    
    # Get specific entity
    print("\n[RETRIEVE] Get mahasiswa entity...")
    mahasiswa = long_term.get_entity("mahasiswa", "2110511001")
    if mahasiswa:
        print(f"✓ Mahasiswa: {mahasiswa['data']}")
    
    # Get all facts of a type
    print("\n[RETRIEVE] Get all QA facts...")
    qa_facts = long_term.get_facts("qa_history", limit=3)
    for idx, fact in enumerate(qa_facts, 1):
        print(f"  [{idx}] {fact['content'][:50]}...")
    
    # Search entities
    print("\n[RETRIEVE] Search advisor entities...")
    advisors = long_term.search_entities("advisor", query="Ahmad")
    print(f"✓ Found {len(advisors)} advisor(s)")


if __name__ == "__main__":
    example_setup_user()
    example_conversation_flow()
    example_retrieve_knowledge()
    
    print("\n" + "="*60)
    print("ALL EXAMPLES COMPLETED ✓")
    print("="*60)
    print("\nMemory System: 3-Tier Architecture")
    print("  1. SHORT-TERM: InMemorySaver (session checkpoints)")
    print("  2. MEDIUM-TERM: JSON files (conversation history)")
    print("  3. LONG-TERM: InMemoryStore (knowledge entities)")

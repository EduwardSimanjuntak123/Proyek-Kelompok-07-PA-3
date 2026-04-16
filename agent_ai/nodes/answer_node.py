import logging
import traceback
from core.llm import call_llm
from core.memory import ConversationMemory, SemanticMemory, LongTermMemory, LONG_TERM_STORE
from models_documentation import get_full_model_documentation, get_model_awareness_context

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

def answer_node(state):
    """
    Node untuk generate jawaban dari assistant
    Model-aware: bisa menjawab tentang schema dan models
    Simpan response ke memory (short-term & long-term)
    """
    try:
        user_id = state.get("user_id", "default")
        
        # Load context
        semantic_memory = SemanticMemory(user_id)
        context_summary = semantic_memory.get_context_summary()
        
        # Get dosen context
        dosen_context = state.get("context", {}).get("dosen_context", [])
        role = dosen_context[0].get("role", "User") if dosen_context else "User"
        prodi = dosen_context[0].get("prodi", "Unknown") if dosen_context else "Unknown"
        kategori_pa = dosen_context[0].get("kategori_pa", "Unknown") if dosen_context else "Unknown"
        
        # Generate model-aware context
        model_context = get_model_awareness_context(role, prodi, kategori_pa)
        
        # kalau ada result dari tools → langsung jawab
        if state.get("result"):
            logger.info(f"[{user_id}] 📊 ANSWER: Gunakan hasil dari tools")
            answer = state["result"]
            
            # Jika action adalah create_group dan berhasil, tambahkan pesan congratulations
            plan = state.get("plan", {})
            if plan.get("action") == "create_group" and state.get("grouping_payload"):
                logger.info(f"[{user_id}] 🎉 ANSWER: Tambah pesan congratulations untuk create_group")
                
                grouping_summary = state.get("grouping_payload", {}).get("summary", {})
                total_groups = grouping_summary.get("total_groups", 0)
                total_members = grouping_summary.get("total_candidates", 0) - grouping_summary.get("excluded_existing_members", 0)
                
                congrats_prompt = f"""
Buatkan pesan ucapan selamat yang singkat, ramah, dan profesional dalam bahasa Indonesia untuk:
- Berhasil membuat {total_groups} kelompok
- Total {total_members} mahasiswa dikelompokkan
- Format: HTML tags yang ringkas (bukan paragraph panjang)

Contoh format:
<div style="background:#d1fae5; border:1px solid #10b981; border-radius:8px; padding:12px; margin-bottom:16px;">
  <p style="margin:0; color:#065f46;"><strong>✓ Selamat!</strong> Pembagian kelompok berhasil dibuat.</p>
</div>

PENTING: Hanya 1-2 kalimat singkat, jangan panjang!
"""
                
                congrats_msg = call_llm(
                    [{
                        "role": "user",
                        "content": congrats_prompt
                    }],
                    context=context_summary
                )
                
                answer = congrats_msg + "\n" + answer
                logger.info(f"[{user_id}] ✓ Pesan congratulations ditambahkan")
        else:
            # Generate system prompt dengan model awareness
            system_prompt = f"""
Kamu adalah AI Assistant yang model-aware dan berpengalaman tentang sistem PA3.

## Model Schema (Database):
{model_context}

## FORMAT RESPONS - GUNAKAN HTML TAGS:

Semua respons HARUS menggunakan HTML tags untuk format yang proper:

1. **Judul/Heading**: Gunakan <h1>, <h2>, <h3>
2. **Line breaks**: Gunakan <br> untuk baris baru
3. **Paragraf**: Wrap teks dengan <p>...</p>
4. **Bold/Tebal**: Gunakan <strong>...</strong>
5. **Daftar/List**: Gunakan <ul><li>...</li></ul> atau <ol><li>...</li></ol>
6. **Highlight/Important**: Gunakan <mark>...</mark> atau <span style='color:red;'>...</span>

CONTOH FORMAT YANG BENAR:
<h2>Informasi Dosen</h2>
<p>Berikut adalah daftar dosen yang tersedia:</p>
<ul>
  <li>Dr. Ahmad Wijaya - DIII Teknologi Komputer</li>
  <li>Prof. Budi Santoso - DIII Teknologi Informasi</li>
</ul>
<br>
<p>Untuk informasi lebih lanjut, hubungi bagian akademik.</p>

Jawab pertanyaan user dengan detail dan contextual. Jika user bertanya tentang model/schema, 
referencias ke dokumentasi di atas. Jika user bertanya hal lain, jawab dengan helpful.

Pertimbangkan role pengguna: {role}
Program Studi: {prodi}
Kategori PA: {kategori_pa}

**PENTING: Semua output HARUS berupa valid HTML yang siap ditampilkan di web browser!**
"""
            
            logger.info(f"[{user_id}] 💬 ANSWER: Panggil LLM (HTML format)")
            answer = call_llm(
                state["messages"],
                system_prompt=system_prompt,
                context=context_summary
            )

        state["messages"].append({
            "role": "assistant",
            "content": answer
        })

        # Simpan ke short-term memory (conversation history)
        memory = ConversationMemory(user_id)
        memory.save_conversation(state["messages"])

        # Simpan Q&A pair ke long-term memory
        if len(state["messages"]) >= 2:
            user_msg = state["messages"][-2]["content"]
            long_term_memory = LongTermMemory(user_id, LONG_TERM_STORE)
            long_term_memory.save_fact(
                "qa_pairs",
                f"Q: {user_msg}\nA: {answer}",
                {"user_question": user_msg, "ai_response": answer}
            )

        logger.info(f"[{user_id}] ✓ Respons dikirim ({len(answer)} chars)")
        return state
        
    except Exception as e:
        logger.error(f"[{state.get('user_id', 'unknown')}] ❌ ERROR IN ANSWER_NODE")
        logger.error(f"Traceback:\n{traceback.format_exc()}")
        raise
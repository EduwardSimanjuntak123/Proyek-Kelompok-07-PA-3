import json
import logging
import re
import traceback
from typing import Dict, List, Tuple
from core.llm import call_llm
from core.memory import SemanticMemory, LongTermMemory, LONG_TERM_STORE
from models_documentation import get_full_model_documentation, get_model_awareness_context

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

CREATE_VERBS = ["buat", "buatkan", "bagi", "generate", "kelompokkan", "susun", "tambahkan", "masukkan", "alokasikan"]
GROUP_TERMS = ["kelompok", "group", "grup"]
QUERY_ONLY_TERMS = ["daftar", "list", "data", "cek", "lihat", "tampilkan", "berapa"]


def _extract_plan_params(prompt_lower: str) -> Dict:
    params: Dict = {}

    anggota_match = re.search(r"(?:siapa\s+|lihat\s+|tampilkan\s+|cek\s+)?anggot?a\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)
    if anggota_match:
        params["nomor_kelompok"] = anggota_match.group(1)

    pembimbing_match = re.search(r"(?:siapa\s+)?pembimbing\s+kelompok\s*(?:nomor\s*)?(\d+)", prompt_lower)
    if pembimbing_match and "nomor_kelompok" not in params:
        params["nomor_kelompok"] = pembimbing_match.group(1)

    params["include_relations"] = (
        "kelompok" in prompt_lower
        and "pembimbing" in prompt_lower
        and "penguji" in prompt_lower
        and any(term in prompt_lower for term in ["tampilkan", "lihat", "daftar", "list", "beserta"])
    )

    params["ask_without_group"] = any(
        phrase in prompt_lower
        for phrase in [
            "belum punya kelompok",
            "belum memiliki kelompok",
            "tanpa kelompok",
            "belum berkelompok",
            "belum ada kelompok",
        ]
    )

    params["is_recreate_intent"] = any(
        phrase in prompt_lower
        for phrase in [
            "acak ulang",
            "acak kembali",
            "buat ulang",
            "ganti kelompok",
            "recreate",
        ]
    )

    return params


def _safe_parse_plan_json(response_text: str) -> Dict:
    try:
        parsed = json.loads(response_text)
        if isinstance(parsed, dict):
            return parsed
    except Exception:
        pass

    json_candidate = re.search(r"\{[\s\S]*\}", response_text)
    if json_candidate:
        try:
            parsed = json.loads(json_candidate.group(0))
            if isinstance(parsed, dict):
                return parsed
        except Exception:
            pass

    return {"action": "chat"}


def _score_action_candidates(prompt_lower: str) -> List[Tuple[str, int]]:
    scoring_rules = {
        "create_group_hybrid": {
            "keywords": ["harus satu", "satu kelompok", "satu grup"],
            "requires": ["kelompok"],
        },
        "create_group_by_grades": {
            "keywords": ["berdasarkan nilai", "grouping nilai", "average grade", "grade-based", "rata-rata nilai"],
            "requires": ["kelompok"],
        },
        "create_group": {
            "keywords": ["buat kelompok", "bagi kelompok", "kelompokkan", "pembagian kelompok", "kelompok n+1", "tambahkan ke kelompok"],
            "requires": ["kelompok"],
        },
        "generate_pembimbing": {
            "keywords": ["generate pembimbing", "assign pembimbing", "buat pembimbing", "pembagian pembimbing"],
            "requires": ["pembimbing"],
        },
        "generate_penguji": {
            "keywords": ["generate penguji", "assign penguji", "buat penguji", "pembagian penguji"],
            "requires": ["penguji"],
        },
        "generate_jadwal_seminar": {
            "keywords": ["jadwal seminar", "buat jadwal", "generate jadwal", "atur jadwal", "schedule seminar"],
            "requires": ["jadwal", "seminar"],
        },
        "generate_jadwal": {
            "keywords": ["buat jadwal", "generate jadwal", "jadwal presentasi", "atur jadwal"],
            "requires": ["jadwal"],
        },
        "save_jadwal": {
            "keywords": ["simpan jadwal", "save jadwal", "ok simpan", "ya simpan", "konfirmasi"],
            "requires": ["jadwal", "selesai"],
        },
        "query_anggota_kelompok": {
            "keywords": ["anggota kelompok", "siapa anggota kelompok"],
            "requires": ["anggota", "kelompok"],
        },
        "query_pembimbing": {
            "keywords": ["dosen pembimbing", "daftar pembimbing", "siapa pembimbing", "advisor"],
            "requires": ["pembimbing"],
        },
        "query_penguji": {
            "keywords": ["dosen penguji", "daftar penguji", "siapa penguji"],
            "requires": ["penguji"],
        },
        "query_mahasiswa": {
            "keywords": ["mahasiswa", "siswa", "nim", "daftar mahasiswa"],
            "requires": ["mahasiswa", "siswa", "nim"],
        },
        "query_kelompok": {
            "keywords": ["daftar kelompok", "list kelompok", "data kelompok"],
            "requires": ["kelompok"],
        },
        "query_dosen": {
            "keywords": ["daftar dosen", "list dosen", "siapa dosen"],
            "requires": ["dosen"],
        },
        "query_nilai": {
            "keywords": ["nilai", "grade", "ipk", "skor"],
            "requires": ["nilai", "grade", "ipk"],
        },
        "generate_excel": {
            "keywords": ["buat excel", "export excel", "spreadsheet", "unduh data excel"],
            "requires": ["excel", "spreadsheet"],
        },
        "check_kelompok": {
            "keywords": ["cek kelompok", "status kelompok", "sudah ada kelompok", "apakah ada kelompok"],
            "requires": ["kelompok"],
        },
        "delete_kelompok": {
            "keywords": ["hapus kelompok", "delete kelompok", "kosongkan kelompok"],
            "requires": ["hapus", "delete", "kosongkan"],
        },
    }

    has_create_intent = any(v in prompt_lower for v in CREATE_VERBS)
    has_query_intent = any(v in prompt_lower for v in QUERY_ONLY_TERMS)
    scores: List[Tuple[str, int]] = []

    for action, cfg in scoring_rules.items():
        score = 0
        for keyword in cfg["keywords"]:
            if keyword in prompt_lower:
                score += 3

        if any(token in prompt_lower for token in cfg["requires"]):
            score += 1

        if has_create_intent and action.startswith("query_"):
            score -= 1
        if has_query_intent and (action.startswith("create_") or action.startswith("generate_")):
            score -= 1

        if score > 0:
            scores.append((action, score))

    scores.sort(key=lambda item: item[1], reverse=True)
    return scores


def _normalize_action(raw_action: str) -> str:
    if not raw_action:
        return "chat"

    aliases = {
        "query_group": "query_kelompok",
        "query_groups": "query_kelompok",
        "query_students": "query_mahasiswa",
        "query_lecturer": "query_dosen",
        "grouping": "create_group",
        "generate_group": "create_group",
        "save_schedule": "save_jadwal",
        "generate_schedule": "generate_jadwal",
    }

    normalized = raw_action.strip().lower()
    return aliases.get(normalized, normalized)

def planner_node(state):
    try:
        user_id = state.get("user_id", "default")
        
        prompt = state["messages"][-1]["content"]
        prompt_lower = prompt.lower()
        create_pattern = r"\b(buat|buatkan|bagi|generate|kelompokkan|susun|tambahkan|masukkan|alokasikan)\b.*\b(kelompok|group|grup|n\+1)\b"
        extracted_params = _extract_plan_params(prompt_lower)

        anggota_pattern = r"(?:siapa\s+|lihat\s+|tampilkan\s+|cek\s+)?anggot?a\s+kelompok\s*(?:nomor\s*)?(\d+)"
        anggota_match = re.search(anggota_pattern, prompt_lower)
        if anggota_match:
            nomor_kelompok = anggota_match.group(1)
            plan = {
                "action": "query_anggota_kelompok",
                "confidence": 0.99,
                "source": "rule",
                "reason": "Exact anggota kelompok pattern matched",
                "params": {**extracted_params, "nomor_kelompok": nomor_kelompok},
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_anggota_kelompok ✓ (nomor={nomor_kelompok})")
            state["plan"] = plan
            return state

        pembimbing_kelompok_pattern = r"(?:siapa\s+)?pembimbing\s+kelompok\s*(?:nomor\s*)?(\d+)"
        pembimbing_kelompok_match = re.search(pembimbing_kelompok_pattern, prompt_lower)
        if pembimbing_kelompok_match:
            nomor_kelompok = pembimbing_kelompok_match.group(1)
            plan = {
                "action": "query_pembimbing",
                "confidence": 0.98,
                "source": "rule",
                "reason": "Exact pembimbing kelompok pattern matched",
                "params": {**extracted_params, "nomor_kelompok": nomor_kelompok},
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_pembimbing ✓ (kelompok={nomor_kelompok})")
            state["plan"] = plan
            return state

        # Intent khusus: tampilkan kelompok beserta pembimbing dan penguji
        if (
            "kelompok" in prompt_lower
            and "pembimbing" in prompt_lower
            and "penguji" in prompt_lower
            and any(term in prompt_lower for term in ["tampilkan", "lihat", "daftar", "list", "beserta"])
        ):
            plan = {
                "action": "query_kelompok",
                "confidence": 0.95,
                "source": "rule",
                "reason": "Detected relation-rich kelompok query",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_kelompok ✓ (with pembimbing+penguji)")
            state["plan"] = plan
            return state

        # Intent khusus: pembimbing assignment HARUS dicheck lebih dulu
        # SEBELUM "daftar dosen pembimbing" karena lebih spesifik
        if ("generate" in prompt_lower or "buat" in prompt_lower or "assign" in prompt_lower) and "pembimbing" in prompt_lower and not any(term in prompt_lower for term in ["daftar", "list", "tampilkan", "lihat"]):
            plan = {
                "action": "generate_pembimbing",
                "confidence": 0.93,
                "source": "rule",
                "reason": "Generation intent for pembimbing",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → generate_pembimbing ✓")
            state["plan"] = plan
            return state

        # Intent khusus: penguji assignment HARUS dicheck lebih dulu
        # SEBELUM "daftar penguji" karena lebih spesifik
        if ("generate" in prompt_lower or "buat" in prompt_lower or "assign" in prompt_lower) and "penguji" in prompt_lower and not any(term in prompt_lower for term in ["daftar", "list", "tampilkan", "lihat"]):
            plan = {
                "action": "generate_penguji",
                "confidence": 0.93,
                "source": "rule",
                "reason": "Generation intent for penguji",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → generate_penguji ✓")
            state["plan"] = plan
            return state

        # Intent khusus: jadwal seminar HARUS dicheck lebih dulu
        # SEBELUM "query jadwal" karena generate_jadwal_seminar lebih spesifik
        if ("buat" in prompt_lower or "generate" in prompt_lower) and ("jadwal" in prompt_lower or "seminar" in prompt_lower) and not any(term in prompt_lower for term in ["daftar", "list", "tampilkan", "lihat", "query"]):
            plan = {
                "action": "generate_jadwal_seminar",
                "confidence": 0.93,
                "source": "rule",
                "reason": "Generation intent for jadwal seminar",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → generate_jadwal_seminar ✓")
            state["plan"] = plan
            return state

        # Intent khusus: save jadwal HARUS dicheck sebelum generic "jadwal" keyword
        # SEBELUM "query jadwal" karena save_jadwal lebih spesifik untuk confirmation
        if ("simpan" in prompt_lower or "save" in prompt_lower) and ("jadwal" in prompt_lower or "selesai" in prompt_lower):
            plan = {
                "action": "save_jadwal",
                "confidence": 0.95,
                "source": "rule",
                "reason": "Explicit save jadwal command",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → save_jadwal ✓")
            state["plan"] = plan
            return state

        # Intent khusus: daftar dosen pembimbing (beda dengan daftar dosen umum)
        if "dosen pembimbing" in prompt_lower or "daftar pembimbing" in prompt_lower:
            plan = {
                "action": "query_pembimbing",
                "confidence": 0.9,
                "source": "rule",
                "reason": "Pembimbing listing query",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_pembimbing ✓ (dosen pembimbing list)")
            state["plan"] = plan
            return state

        # Intent khusus: daftar dosen penguji
        if "dosen penguji" in prompt_lower or "daftar penguji" in prompt_lower:
            plan = {
                "action": "query_penguji",
                "confidence": 0.9,
                "source": "rule",
                "reason": "Penguji listing query",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_penguji ✓ (dosen penguji list)")
            state["plan"] = plan
            return state

        if "sudah ada pembimbing" in prompt_lower or "apakah sudah ada pembimbing" in prompt_lower:
            plan = {
                "action": "check_pembimbing",
                "confidence": 0.92,
                "source": "rule",
                "reason": "Pembimbing existence check",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → check_pembimbing ✓")
            state["plan"] = plan
            return state

        # ✨ CHECK FOR HYBRID GROUPING FIRST (constraint + optional grades)
        # Pattern: "X, Y, Z satu kelompok" or "nim1, nim2 harus satu" 
        # OR: Presence of "harus satu" OR "satu kelompok" + create verbs
        # THIS MUST COME BEFORE grade-based check because it's more specific!
        hybrid_constraint_pattern = r"(?:harus\s+satu|satu\s+kelompok|satu\s+grup|harus\s+satu\s+kelompok)"
        if any(verb in prompt_lower for verb in CREATE_VERBS) and re.search(hybrid_constraint_pattern, prompt_lower) and any(term in prompt_lower for term in GROUP_TERMS):
            plan = {
                "action": "create_group_hybrid",
                "confidence": 0.97,
                "source": "rule",
                "reason": "Constraint-based grouping intent",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group_hybrid ✓ (constraint PRIORITY)")
            state["plan"] = plan
            return state

        # CHECK FOR GRADE-BASED GROUPING (before generic create_group)
        # This comes AFTER hybrid check to allow hybrid to take priority
        grade_keywords = ["berdasarkan nilai", "by grades", "nilai", "rata-rata nilai", "average grade", "grade-based", "berdasarkan nilai"]
        if ("buat" in prompt_lower or "generate" in prompt_lower or "bagi" in prompt_lower) and any(kw in prompt_lower for kw in grade_keywords) and any(term in prompt_lower for term in ["kelompok", "grup", "group"]):
            plan = {
                "action": "create_group_by_grades",
                "confidence": 0.95,
                "source": "rule",
                "reason": "Grade-based grouping intent",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group_by_grades ✓ (grade-based)")
            state["plan"] = plan
            return state

        if re.search(create_pattern, prompt_lower) and not any(term in prompt_lower for term in QUERY_ONLY_TERMS):
            plan = {
                "action": "create_group",
                "confidence": 0.9,
                "source": "rule",
                "reason": "Create-group verb pattern",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group ✓ (verb heuristic)")
            state["plan"] = plan
            return state

        if any(verb in prompt_lower for verb in CREATE_VERBS) and any(term in prompt_lower for term in GROUP_TERMS):
            plan = {
                "action": "create_group",
                "confidence": 0.85,
                "source": "rule",
                "reason": "Fallback create-group intent",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group ✓ (fallback heuristic)")
            state["plan"] = plan
            return state

        if "n+1" in prompt_lower and "kelompok" in prompt_lower:
            plan = {
                "action": "create_group",
                "confidence": 0.86,
                "source": "rule",
                "reason": "N+1 grouping pattern",
                "params": extracted_params,
                "alternatives": [],
            }
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group ✓ (n+1 pattern)")
            state["plan"] = plan
            return state

        scored_candidates = _score_action_candidates(prompt_lower)
        top_candidates = [action for action, _ in scored_candidates[:3]]
        if scored_candidates:
            top_action, top_score = scored_candidates[0]
            second_score = scored_candidates[1][1] if len(scored_candidates) > 1 else 0

            if top_score >= 4 and (top_score - second_score) >= 1:
                confidence = min(0.95, 0.5 + (top_score * 0.08))
                plan = {
                    "action": top_action,
                    "confidence": round(confidence, 2),
                    "source": "scoring",
                    "reason": f"Top intent score={top_score}, margin={top_score - second_score}",
                    "params": extracted_params,
                    "alternatives": top_candidates[1:],
                }
                logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → {top_action} ✓ (dynamic scoring)")
                state["plan"] = plan
                return state
        
        # Jika tidak match keyword, panggil LLM
        logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → LLM (ambiguous/no strong match)")
        
        # Load semantic memory untuk context
        semantic_memory = SemanticMemory(user_id)
        context_summary = semantic_memory.get_context_summary()
        
        # Get dosen context
        dosen_context = state.get("context", {}).get("dosen_context", [])
        role = dosen_context[0].get("role", "User") if dosen_context else "User"
        prodi = dosen_context[0].get("prodi", "Unknown") if dosen_context else "Unknown"
        kategori_pa = dosen_context[0].get("kategori_pa", "Unknown") if dosen_context else "Unknown"
        
        # Initialize long-term memory
        long_term_memory = LongTermMemory(user_id, LONG_TERM_STORE)
        
        # Generate model-aware context
        model_context = get_model_awareness_context(role, prodi, kategori_pa)

        candidate_hint = ", ".join(top_candidates) if top_candidates else "none"
        system_prompt = f"""
    Kamu adalah AI Router - tentukan action paling tepat dari user query.

    PRINSIP:
    - Prioritaskan intent user, bukan keyword literal semata.
    - Jika ada kandidat dari scorer, gunakan itu sebagai prior dan pilih yang paling masuk akal.
    - Jika query meminta eksekusi (buat/generate/assign), jangan pilih query_*.
    - Jika query meminta lihat/cek/daftar, jangan pilih create_*/generate_* kecuali eksplisit.

    Kandidat dari scorer: {candidate_hint}

    ACTION VALID:
    check_kelompok, delete_kelompok, query_dosen, query_dosen_role, query_mahasiswa, query_kelompok,
    query_anggota_kelompok, query_matakuliah, query_prodi, query_roles, query_kategori_pa,
    query_tahun_ajaran, query_ruangan, query_jadwal, query_nilai, query_pembimbing, query_penguji,
    generate_pembimbing, check_pembimbing, generate_penguji, check_penguji, generate_jadwal,
    save_jadwal, create_group, create_group_hybrid, create_group_by_grades, generate_excel, chat

    OUTPUT JSON ONLY:
    {{"action":"...","confidence":0.0-1.0,"reason":"short reason"}}
    """
        
        planner_messages = [{
            "role": "user",
            "content": prompt
        }]

        response = call_llm(
            planner_messages,
            system_prompt=system_prompt,
            context=context_summary
        )

        parsed = _safe_parse_plan_json(response)
        action = _normalize_action(parsed.get("action", "chat"))
        plan = {
            "action": action,
            "confidence": float(parsed.get("confidence", 0.6) or 0.6),
            "source": "llm",
            "reason": parsed.get("reason", "LLM arbitration"),
            "params": extracted_params,
            "alternatives": top_candidates,
        }
        
        # Save query fact
        long_term_memory.save_fact(
            "query_history",
            prompt,
            {"action": plan.get("action")}
        )

        state["plan"] = plan
        return state
        
    except Exception as e:
        logger.error(f"[{state.get('user_id', 'unknown')}] ❌ ERROR IN PLANNER_NODE")
        logger.error(f"Traceback:\n{traceback.format_exc()}")
        raise
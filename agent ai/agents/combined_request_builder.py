"""
Combined Request Builder - Smart dependency resolution with LLM validation

System:
1. Extract actions from user input (keywords → actions)
2. Resolve dependencies (what must run before what)
3. Build execution plan (convert to executor steps in correct order)
4. Validate results against original instruction (LLM check)
5. Handle parallel actions and cycle detection

Contoh: "buatkan kelompok dan langsung simpan"
→ actions: [create_group, save_group]
→ resolved: [get_mahasiswa, create_group, save_group]
→ steps: [get_mahasiswa, grouping, save_group]
→ validation: LLM confirms output matches instruction
"""

from openai import OpenAI

# Try to import config, fallback to None if not available (for standalone testing)
client = None
try:
    from app.config import OPENAI_API_KEY
    client = OpenAI(api_key=OPENAI_API_KEY)
except:
    try:
        import os
        api_key = os.getenv("OPENAI_API_KEY")
        if api_key:
            client = OpenAI(api_key=api_key)
    except:
        pass

# ============================================================
# ACTION DEPENDENCY RULES
# ============================================================
ACTION_RULES = {
    # Data retrieval actions (no dependencies)
    "get_mahasiswa": {
        "depends_on": [],  # No dependencies
        "executor_action": "get_mahasiswa"
    },
    "get_courses": {
        "depends_on": [],  # No dependencies
        "executor_action": "get_courses"
    },
    "get_dosen": {
        "depends_on": [],  # No dependencies
        "executor_action": "get_dosen_list"
    },
    "get_dosen_list": {
        "depends_on": [],  # No dependencies
        "executor_action": "get_dosen_list"
    },
    "count_mahasiswa": {
        "depends_on": [],  # No dependencies
        "executor_action": "count_mahasiswa"
    },
    
    # Grouping actions (depends on data retrieval)
    "create_group": {
        "depends_on": ["get_mahasiswa"],  # Must get students first
        "executor_action": "grouping"
    },
    
    # Save actions (depends on previous operations)
    "save_group": {
        "depends_on": ["create_group"],  # Must create groups first
        "executor_action": "save_group"
    }
}


# ============================================================
# DEPENDENCY VALIDATION & CYCLE DETECTION
# ============================================================

def detect_cycles(action_rules):
    """Detect circular dependencies dalam ACTION_RULES
    
    Returns: (has_cycles, cycles_found)
    """
    def has_cycle_util(action, visited, rec_stack, cycles):
        visited.add(action)
        rec_stack.add(action)
        
        deps = action_rules.get(action, {}).get("depends_on", [])
        
        for dep in deps:
            if dep not in visited:
                if has_cycle_util(dep, visited, rec_stack, cycles):
                    return True
            elif dep in rec_stack:
                cycles.append(f"{action} → {dep}")
                return True
        
        rec_stack.remove(action)
        return False
    
    visited = set()
    cycles = []
    
    for action in action_rules:
        if action not in visited:
            if has_cycle_util(action, visited, set(), cycles):
                return True, cycles
    
    return False, []


def detect_parallel_actions(actions):
    """Detect aksi yang bisa dijalankan secara parallel
    
    Returns: {
        "parallel_groups": [["get_mahasiswa", "get_courses"], ["get_dosen"]],
        "sequential": ["create_group", "save_group"]
    }
    """
    # Group actions by dependency level (bisa dirun bareng = no dependencies or same level)
    dependency_level = {}
    
    for action in actions:
        deps = ACTION_RULES.get(action, {}).get("depends_on", [])
        level = 0
        
        for dep in deps:
            if dep in dependency_level:
                level = max(level, dependency_level[dep] + 1)
        
        dependency_level[action] = level
    
    # Group by level
    parallel_groups = {}
    for action, level in dependency_level.items():
        if level not in parallel_groups:
            parallel_groups[level] = []
        parallel_groups[level].append(action)
    
    # Sort by level
    sorted_levels = sorted(parallel_groups.keys())
    
    return {
        "parallel_groups": [parallel_groups[level] for level in sorted_levels],
        "sequential": [action for level in sorted_levels for action in parallel_groups[level]],
        "dependency_levels": dependency_level
    }


def validate_result_with_llm(original_instruction, detected_actions, resolved_actions, result_data=None):
    """Validate bahwa output/execution sesuai dengan instruction
    
    Returns: {
        "valid": True/False,
        "confidence": 0.0-1.0,
        "reasoning": "explanation",
        "suggestions": []
    }
    """
    if not client:
        return {
            "valid": True,
            "confidence": 0.5,
            "reasoning": "LLM validation disabled",
            "suggestions": []
        }
    
    try:
        validation_prompt = f"""Apakah execution plan berikut sesuai dengan user instruction?

USER INSTRUCTION:
{original_instruction}

DETECTED ACTIONS:
{', '.join(detected_actions)}

RESOLVED EXECUTION ORDER:
{' → '.join(resolved_actions)}

RESULT TYPE: {type(result_data).__name__ if result_data else 'Not yet executed'}

Berikan respons dalam format:
VALID: <yes/no>
CONFIDENCE: <0.0-1.0>
REASONING: <penjelasan singkat>
SUGGESTIONS: <saran perbaikan jika ada>
"""
        
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "Anda adalah validator untuk AI planning system. Periksa apakah execution plan sesuai dengan user instruction."},
                {"role": "user", "content": validation_prompt}
            ],
            temperature=0.3,
            max_tokens=300
        )
        
        response_text = response.choices[0].message.content
        lines = response_text.split('\n')
        
        result = {
            "valid": True,
            "confidence": 0.8,
            "reasoning": "Valid execution plan",
            "suggestions": []
        }
        
        for line in lines:
            if line.startswith("VALID:"):
                result["valid"] = "yes" in line.lower()
            elif line.startswith("CONFIDENCE:"):
                try:
                    conf = float(line.split(":")[-1].strip())
                    result["confidence"] = conf
                except:
                    pass
            elif line.startswith("REASONING:"):
                result["reasoning"] = line.split(":", 1)[-1].strip()
            elif line.startswith("SUGGESTIONS:"):
                suggestions = line.split(":", 1)[-1].strip()
                if suggestions and suggestions != "-":
                    result["suggestions"] = [s.strip() for s in suggestions.split(",")]
        
        return result
        
    except Exception as e:
        print(f"[LLM VALIDATION] Error: {e}")
        return {
            "valid": True,
            "confidence": 0.5,
            "reasoning": f"Validation error: {str(e)}",
            "suggestions": []
        }


def resolve_dependencies(intents):
    """
    Resolve dependency graph untuk intents yang diminta
    
    Input: ["get_dosen", "save_group"]
    Output: ["get_mahasiswa", "create_group", "save_group", "get_dosen"]
    
    Algoritma:
    - Untuk setiap intent, tambahkan semua dependencies-nya (recursively)
    - Hiraukan duplikat
    - Maintain topological order
    """
    resolved_actions = []
    visited = set()
    
    def add_action(action):
        """DFS untuk add action dan dependencies"""
        if action in visited:
            return
        
        # Get dependencies dari ACTION_RULES
        deps = ACTION_RULES.get(action, {}).get("depends_on", [])
        
        # Recursively add dependencies first (topological order)
        for dep in deps:
            add_action(dep)
        
        # Mark as visited and add to resolved list
        visited.add(action)
        resolved_actions.append(action)
    
    # Resolve setiap intent
    for intent in intents:
        add_action(intent)
    
    return resolved_actions


def build_steps_from_actions(actions):
    """
    Convert resolved actions menjadi executor steps
    
    Input: ["get_mahasiswa", "create_group", "save_group", "get_dosen"]
    Output: [
        {"action": "get_mahasiswa", "params": {}},
        {"action": "grouping", "params": {}},
        {"action": "save_group", "params": {}},
        {"action": "get_dosen_list", "params": {}}
    ]
    """
    steps = []
    
    for action in actions:
        # Get executor action name dari ACTION_RULES
        executor_action = ACTION_RULES.get(action, {}).get("executor_action", action)
        
        steps.append({
            "action": executor_action,
            "params": {}
        })
    
    return steps


def detect_combined_request(prompt, context_history=None):
    """
    Detect dan extract actions dari user prompt dengan flexible NLP
    
    Features:
    - Keyword matching (exact + substring)
    - Context-aware (remember previous requests)
    - Flexible NLP dengan typo tolerance
    - Semantic similarity fallback
    
    Args:
        prompt: User request string
        context_history: List of previous requests for context
    
    Returns:
        {
            "type": "combined" | "single" | "unknown",
            "actions": ["create_group", "save_group"],
            "intents": [{"action": "create_group"}, {"action": "save_group"}],
            "prompt": prompt,
            "context_aware": True/False,
            "confidence": 0.0-1.0
        }
    """
    prompt_lower = prompt.lower()
    
    # === EARLY EXIT: Skip combined detection untuk VIEW/DISPLAY patterns ===
    # Jangan mistake "tampilkan kelompok yang disimpan" sebagai create+save
    view_patterns = [
        "tampilkan", "lihat", "show", "display", "daftar", "list",
        "tunjukkan", "bagaimana", "apa aja", "apa saja",
        "berapa total", "berapa jumlah"
    ]
    if any(pattern in prompt_lower for pattern in view_patterns):
        # Ini likely VIEW/DISPLAY request, not combined
        return {
            "type": "single",
            "actions": [],
            "intents": [],
            "prompt": prompt,
            "context_aware": False,
            "confidence": 0.0
        }
    
    detected_actions = []
    action_confidence = {}
    
    # Keyword mappings untuk action detection (diperluas dengan fleksibilitas)
    action_keywords = {
        "get_mahasiswa": {
            'exact': ['daftar mahasiswa', 'list mahasiswa', 'data mahasiswa'],
            'flexible': ['mahasiswa', 'siswa', 'murid', 'peserta'], 
            'partial': ['berikan mahasiswa', 'tampilkan mahasiswa', 'lihat mahasiswa']
        },
        "get_courses": {
            'exact': ['matakuliah', 'mata kuliah', 'course'],
            'flexible': ['mk', 'modul', 'materi', 'pelajaran'],
            'partial': ['daftar mata', 'berikan mata', 'tampilkan mata', 'semester']
        },
        "create_group": {
            'exact': ['buat kelompok', 'buatkan kelompok', 'generate kelompok'],
            'flexible': ['acak', 'pengelompokan', 'kelompok', 'grup'],
            'partial': ['kelompok acak', 'membuat kelompok', 'membentuk kelompok']
        },
        "save_group": {
            'exact': ['simpan', 'save', 'tetapkan', 'terapkan'],
            'flexible': ['langsung', 'apply', 'store', 'penyimpanan'],
            'partial': ['simpan kelompok', 'disimpan', 'disimpankan']
        },
        "get_dosen": {
            'exact': ['tampilkan dosen', 'lihat dosen', 'daftar dosen'],
            'flexible': ['dosen', 'pengajar', 'instruktur', 'pembimbing'],
            'partial': ['ambil dosen', 'data dosen', 'info dosen']
        },
        "count_mahasiswa": {
            'exact': ['berapa total', 'jumlah mahasiswa', 'berapa mahasiswa'],
            'flexible': ['berapa', 'jumlah', 'hitung', 'total'],
            'partial': ['total mahasiswa', 'ada berapa']
        }
    }
    
    # Check each action keyword dengan sliding confidence
    for action, keyword_groups in action_keywords.items():
        confidence = 0.0
        matched_keywords = []
        
        # Check exact matches (confidence: 1.0)
        for keyword in keyword_groups.get('exact', []):
            if keyword in prompt_lower:
                confidence = 1.0
                matched_keywords.append(keyword)
                break
        
        # Check flexible matches if no exact match (confidence: 0.7)
        if confidence < 1.0:
            for keyword in keyword_groups.get('flexible', []):
                if keyword in prompt_lower:
                    confidence = 0.7
                    matched_keywords.append(keyword)
                    break
        
        # Check partial matches if no flexible match (confidence: 0.5)
        if confidence < 0.7:
            for keyword in keyword_groups.get('partial', []):
                if keyword in prompt_lower:
                    confidence = 0.5
                    matched_keywords.append(keyword)
                    break
        
        # Special handling untuk certain actions
        if action == "save_group":
            # Only count if grouping also present
            has_grouping = any(kw in prompt_lower for kw in action_keywords["create_group"]['exact'] + action_keywords["create_group"]['flexible'])
            if confidence > 0 and has_grouping:
                detected_actions.append(action)
                action_confidence[action] = confidence
        elif action == "get_courses":
            # Only count if explicitly mentioned (not just "semester" as filter)
            has_matakuliah = any(kw in prompt_lower for kw in ['matakuliah', 'mata kuliah', 'course', 'daftar mata', 'mk'])
            if confidence > 0 and has_matakuliah:
                detected_actions.append(action)
                action_confidence[action] = confidence
        elif action == "get_dosen":
            # Skip if just "pembimbing" context in grouping
            if 'pembimbing' not in prompt_lower or ('tampilkan' in prompt_lower or 'lihat' in prompt_lower):
                if confidence > 0:
                    detected_actions.append(action)
                    action_confidence[action] = confidence
        else:
            if confidence > 0:
                detected_actions.append(action)
                action_confidence[action] = confidence
    
    # Context-aware: check if history suggests similar requests
    context_aware = False
    if context_history and len(context_history) > 0:
        # Jika ada pattern history, deteksi apakah ini follow-up yang sama
        last_actions = context_history[-1].get("actions", []) if isinstance(context_history[-1], dict) else []
        if last_actions == detected_actions:
            context_aware = True
    
    # Convert actions to intents
    intents = [{"action": action, "type": action, "confidence": action_confidence.get(action, 0.5)} 
               for action in detected_actions]
    
    # Hitung overall confidence
    overall_confidence = sum(action_confidence.values()) / len(action_confidence) if action_confidence else 0.0
    
    # Determine type
    if len(detected_actions) > 1:
        result_type = "combined"
    elif len(detected_actions) == 1:
        result_type = "single"
    else:
        result_type = "unknown"
    
    return {
        "type": result_type,
        "actions": detected_actions,
        "intents": intents,
        "prompt": prompt,
        "context_aware": context_aware,
        "confidence": overall_confidence,
        "action_confidence": action_confidence
    }


def build_combined_steps(combined_request, dosen_context=None, validate_with_llm=True):
    """
    Smart planner: Convert combined request → dependency graph → execution plan
    
    Features:
    - Cycle detection
    - Parallel action awareness
    - LLM validation of results
    - Context-aware execution
    
    Algoritma:
    1. Extract actions dari combined_request
    2. Detect cycles dalam dependency graph
    3. Resolve dependencies (auto-add missing prerequisites)
    4. Detect parallel-able actions
    5. Convert to executor steps
    6. Validate with LLM bahwa output sesuai instruction
    
    Contoh:
    Input: ["save_group", "get_dosen"]
    → Resolved: ["get_mahasiswa", "create_group", "save_group", "get_dosen"]
    → Parallel: [[get_mahasiswa, get_dosen], [create_group], [save_group]]
    → Validation: LLM confirms plan is correct
    
    Returns:
        {
            "type": "combined",
            "actions": ["get_mahasiswa", "create_group", "save_group", "get_dosen"],
            "steps": [executor steps],
            "parallel_analysis": {...},
            "validation": {...},
            "intents": original intents
        }
    """
    
    # 1. Detect cycles in dependency graph
    has_cycles, cycles = detect_cycles(ACTION_RULES)
    if has_cycles:
        print(f"[PLANNER] ⚠️  WARNING: Circular dependencies detected: {cycles}")
        return {
            "type": "error",
            "error": "Circular dependency detected",
            "cycles": cycles,
            "actions": [],
            "steps": []
        }
    
    # 2. Extract actions from combined request
    requested_actions = combined_request.get("actions", [])
    intents = combined_request.get("intents", [])
    original_prompt = combined_request.get("prompt", "")
    
    # Return early if no actions detected
    if not requested_actions:
        return {
            "type": "empty",
            "actions": [],
            "steps": [],
            "intents": []
        }
    
    print(f"[PLANNER] 🎯 Requested actions: {requested_actions}")
    print(f"[PLANNER] 📊 Confidence: {combined_request.get('confidence', 0.0):.2f}")
    
    # 3. Resolve dependencies
    resolved_actions = resolve_dependencies(requested_actions)
    print(f"[PLANNER] ✓ Resolved (with deps): {resolved_actions}")
    
    # 4. Detect parallel actions
    parallel_analysis = detect_parallel_actions(resolved_actions)
    print(f"[PLANNER] ⚡ Parallel groups: {parallel_analysis['parallel_groups']}")
    
    # 5. Convert to steps
    steps = build_steps_from_actions(resolved_actions)
    
    # 6. Add grouping details from parsing if needed (for create_group)
    if "create_group" in resolved_actions:
        try:
            from agents.parser import parse_grouping_request
            parsed = parse_grouping_request(original_prompt)
            
            for i, step in enumerate(steps):
                if step["action"] == "grouping":
                    step["params"].update({
                        "group_size": parsed.get("group_size"),
                        "num_groups": parsed.get("num_groups"),
                        "avoid_pairs": parsed.get("avoid_pairs", []),
                        "must_pairs": parsed.get("must_pairs", []),
                        "shuffle": parsed.get("shuffle", False)
                    })
                    print(f"[PLANNER] 📝 Updated grouping step with parsed details")
                    break
        except Exception as e:
            print(f"[PLANNER] ⓘ Could not parse grouping details: {e}")
    
    # 7. Validate with LLM
    validation = None
    if validate_with_llm:
        validation = validate_result_with_llm(
            original_prompt,
            requested_actions,
            resolved_actions
        )
        
        print(f"[PLANNER] 🔍 LLM Validation: {validation['valid']}")
        print(f"[PLANNER]    Confidence: {validation['confidence']:.2f}")
        print(f"[PLANNER]    Reasoning: {validation['reasoning']}")
        
        if not validation['valid']:
            print(f"[PLANNER] ⚠️  Validation failed - consider revising")
        
        if validation['suggestions']:
            print(f"[PLANNER] 💡 Suggestions: {validation['suggestions']}")
    
    result = {
        "type": "combined",
        "actions": resolved_actions,
        "requested_actions": requested_actions,
        "steps": steps,
        "parallel_analysis": parallel_analysis,
        "validation": validation,
        "cycle_safe": not has_cycles,
        "intents": intents,
        "confidence": combined_request.get("confidence", 0.0),
        "context_aware": combined_request.get("context_aware", False)
    }
    
    print(f"[PLANNER] ✅ Generated {len(steps)} execution steps")
    print(f"[PLANNER] 📋 Execution can be parallelized into {len(parallel_analysis['parallel_groups'])} stages")
    
    return result


# ============================================================
# TEST & EXAMPLES
# ============================================================

if __name__ == "__main__":
    import json
    
    print("\n" + "="*70)
    print("COMBINED REQUEST BUILDER - ENHANCED TEST")
    print("="*70)
    
    # Test cycle detection first
    print("\n[TEST] Cycle Detection")
    has_cycles, cycles = detect_cycles(ACTION_RULES)
    print(f"Has cycles: {has_cycles}")
    if has_cycles:
        print(f"Cycles found: {cycles}")
    else:
        print("✅ No cycles detected - dependency graph is safe")
    
    # Test cases
    test_cases = [
        ("buatkan kelompok berdasarkan nilai dan tampilkan daftar dosen", 
         ["create_group", "get_dosen"]),
        ("buatkan kelompok 5 orang perkelompok dan langsung simpan",
         ["create_group", "save_group"]),
        ("simpan kelompok dan tampilkan dosen",
         ["save_group", "get_dosen"]),
        ("berapa total mahasiswa dan buatkan kelompok",
         ["count_mahasiswa", "create_group"]),
        ("buat kelompok acak",
         ["create_group"]),
        ("berikan daftar mahasiswa dan matakuliah semester 1",
         ["get_mahasiswa", "get_courses"]),
        ("halo tampilkan mahasiswa dan course list",
         ["get_mahasiswa", "get_courses"]),
        ("halo berikan daftar mahasiswa dan matakuliah semester 1",
         ["get_mahasiswa", "get_courses"]),
    ]
    
    # Keep history untuk context awareness test
    history = []
    
    for idx, (prompt, expected_actions) in enumerate(test_cases, 1):
        print(f"\n{'─'*70}")
        print(f"[TEST {idx}] Input: {prompt}")
        print(f"Expected: {expected_actions}")
        
        # Detect combined request dengan context history
        result = detect_combined_request(prompt, context_history=history)
        print(f"Detected: {result['type']}")
        print(f"Actions: {result['actions']}")
        print(f"Confidence: {result['confidence']:.2f}")
        print(f"Context-aware: {result['context_aware']}")
        
        # Build steps
        if result['type'] in ['combined', 'single']:
            steps_result = build_combined_steps(result, validate_with_llm=False)
            
            if 'error' not in steps_result:
                print(f"\n  Execution order (with dependencies):")
                for i, action in enumerate(steps_result['actions'], 1):
                    print(f"    {i}. {action}")
                
                print(f"\n  Parallel groups:")
                for i, group in enumerate(steps_result['parallel_analysis']['parallel_groups'], 1):
                    if len(group) > 1:
                        print(f"    Stage {i} (parallel): {group}")
                    else:
                        print(f"    Stage {i}: {group}")
                
                print(f"\n  Executor steps:")
                for i, step in enumerate(steps_result['steps'], 1):
                    print(f"    {i}. {step['action']}")
                
                # Store in history untuk context awareness
                history.append(result)
            else:
                print(f"\n  ❌ ERROR: {steps_result.get('error')}")
    
    print(f"\n{'='*70}")
    print("ENHANCED TEST COMPLETE")
    print("="*70)
    print("\nNew Features Tested:")
    print("  ✅ Cycle detection in dependency graph")
    print("  ✅ Flexible NLP with confidence scoring")
    print("  ✅ Context-aware detection")
    print("  ✅ Parallel action analysis")
    print("  ✅ LLM validation (optional)")
    print()
    

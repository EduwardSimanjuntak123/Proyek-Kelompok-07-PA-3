"""
Helper untuk convert LLM parsed output ke Executor steps format
"""

def build_grouping_steps(parsed_output, dosen_context=None):
    """
    Convert parsed output dari LLM parser ke steps yang bisa dieksekusi
    
    Returns: dict dengan action steps dan metadata
    """
    
    action = parsed_output.get("action", "unknown")
    strategy = parsed_output.get("grouping_strategy", "balanced")
    
    # Prepare base parameters
    params = {
        "shuffle": parsed_output.get("shuffle", False),
        "must_pairs": parsed_output.get("must_pairs", []),
        "avoid_pairs": parsed_output.get("avoid_pairs", []),
        "show_scores": parsed_output.get("show_scores", False),
    }
    
    steps = []
    
    if action == "create_group":
        # Step 1: Get mahasiswa
        get_mhs_params = {
            "fields": ["nama", "nim"]
        }
        
        # Tambah prodi_filter jika ada di context
        if dosen_context and hasattr(dosen_context, 'prodi_id'):
            get_mhs_params["prodi_filter"] = dosen_context.prodi_id
        
        steps.append({
            "action": "get_mahasiswa",
            "params": get_mhs_params
        })
        
        # Step 2: Score mahasiswa jika strategy score-based
        if strategy == "score-based" or params.get("show_scores"):
            steps.append({
                "action": "get_student_scores_by_category",
                "params": {}
            })
        
        # Step 3: Score mahasiswa berdasarkan karir jika strategy career-based
        if strategy == "career-balanced":
            steps.append({
                "action": "categorize_subjects",
                "params": {}
            })
            steps.append({
                "action": "create_balanced_groups_by_career",
                "params": {
                    "num_groups": parsed_output.get("num_groups"),
                    "shuffle": params["shuffle"],
                    "must_pairs": params["must_pairs"],
                    "avoid_pairs": params["avoid_pairs"]
                }
            })
        else:
            # Regular grouping dengan constraints
            num_groups = parsed_output.get("num_groups")
            group_size = parsed_output.get("group_size")
            
            grouping_params = {
                "shuffle": params["shuffle"],
                "must_pairs": params["must_pairs"],
                "avoid_pairs": params["avoid_pairs"],
            }
            
            if num_groups:
                grouping_params["num_groups"] = num_groups
            
            if group_size:
                grouping_params["group_size"] = group_size
            
            # If score-based, tambah score handling
            if strategy == "score-based":
                steps.append({
                    "action": "group_by_score_balance_with_constraints",
                    "params": grouping_params
                })
            else:
                steps.append({
                    "action": "grouping_with_constraints",
                    "params": grouping_params
                })
    
    elif action == "view_group":
        steps.append({
            "action": "view_group_by_number",
            "params": {
                "group_number": parsed_output.get("group_number"),
                "show_scores": params.get("show_scores", False)
            }
        })
    
    elif action == "modify_group":
        steps.append({
            "action": "modify_existing_groups",
            "params": {
                "shuffle": params["shuffle"],
                "must_pairs": params["must_pairs"],
                "avoid_pairs": params["avoid_pairs"]
            }
        })
    
    elif action == "save_group":
        # Save group action - doesn't need to fetch mahasiswa
        # Just saves the last result from memory to database
        steps.append({
            "action": "save_group",
            "params": {}
        })
    
    elif action == "confirm_save_group":
        # User confirmed to replace existing groups
        # params should contain the pending save info
        steps.append({
            "action": "confirm_save_group",
            "params": {}
        })
    
    else:
        return {"error": f"Unknown action: {action}"}
    
    return {
        "action": action,
        "strategy": strategy,
        "steps": steps,
        "metadata": {
            "show_scores": params.get("show_scores"),
            "shuffle": params["shuffle"],
            "must_pairs": len(params.get("must_pairs", [])),
            "avoid_pairs": len(params.get("avoid_pairs", []))
        }
    }


# Test
if __name__ == "__main__":
    import json
    
    test_parsed = {
        "action": "create_group",
        "num_groups": None,
        "must_pairs": [["Revi", "Malino"], ["Mei", "Sahat"]],
        "avoid_pairs": [],
        "shuffle": True,
        "show_scores": True,
        "grouping_strategy": "constraint-based"
    }
    
    result = build_grouping_steps(test_parsed)
    print(json.dumps(result, indent=2))

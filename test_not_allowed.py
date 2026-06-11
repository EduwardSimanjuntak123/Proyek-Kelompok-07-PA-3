import json, sys, os
sys.path.append(os.path.abspath('.'))
from agent_ai.nodes.grouping_form_handler import GroupingFormHandler
sample = "Mahasiswa dengan NIM 11420063 tidak boleh sekelompok dengan mahasiswa NIM 11420042"
constraints = GroupingFormHandler._parse_constraints(sample)
print(json.dumps(constraints, ensure_ascii=False, indent=2))

import re

with open('nodes/executor_node.py', 'r') as f:
    lines = f.readlines()

# Search for 're +=' or other augmented assignments
for i, line in enumerate(lines[1656:1960], start=1657):  
    if re.search(r'\sre\s*[+\-*/&|^]?=', line):
        if 'recreate' not in line.lower():
            print(f'Line {i}: {line.rstrip()}')

# Search for list/dict/set comprehensions with 're'
for i, line in enumerate(lines[1656:1960], start=1657):
    if '[' in line and ' for ' in line and 're' in line:
        print(f'Line {i} (comprehension): {line.rstrip()}')
    if '{' in line and ' for ' in line and 're' in line and '{' not in line.split('for')[0]:  # Not in HTML
        print(f'Line {i} (set/dict comp): {line.rstrip()}')

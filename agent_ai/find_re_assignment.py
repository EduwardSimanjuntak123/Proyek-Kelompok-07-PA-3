import re

with open('nodes/executor_node.py', 'r') as f:
    lines = f.readlines()

# Search for 're =' pattern in the create_group_by_grades section
for i, line in enumerate(lines[1656:1960], start=1657):  
    # Look for patterns like: re = something (but skip 'recreate' and comments)
    if re.search(r'^\s*re\s*=', line) or re.search(r'\sre\s*=\s', line):
        if 'recreate' not in line.lower():
            print(f'Line {i}: {line.rstrip()}')

# Also check for walrus operators with 're'
for i, line in enumerate(lines[1656:1960], start=1657):
    if ':= ' in line and 're' in line:
        print(f'Line {i} (walrus): {line.rstrip()}')

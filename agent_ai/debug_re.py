import ast

with open('nodes/executor_node.py', 'r') as f:
    code = f.read()

tree = ast.parse(code)

# Find the executor_node function
for node in ast.walk(tree):
    if isinstance(node, ast.FunctionDef) and node.name == 'executor_node':
        # Get all names in this function
        store_lines = set()
        for n in ast.walk(node):
            if isinstance(n, ast.Name) and isinstance(n.ctx, ast.Store):
                if n.id == 're':
                    store_lines.add(n.lineno)
        
        if store_lines:
            print(f"Found assignments to 're' at lines: {sorted(store_lines)}")
        else:
            print("No assignments to 're' found in executor_node")
            
        # Look for 're' in nested functions
        for n in ast.walk(node):
            if isinstance(n, ast.FunctionDef):
                print(f"Found nested function: {n.name} at line {n.lineno}")

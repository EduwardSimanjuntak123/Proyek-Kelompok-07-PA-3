import ast
import sys

with open('nodes/executor_node.py', 'r') as f:
    content = f.read()

tree = ast.parse(content)

# Find executor_node function
for node in tree.body:
    if isinstance(node, ast.FunctionDef) and node.name == 'executor_node':
        print(f"Found executor_node function at line {node.lineno}")
        
        # Find all Store contexts (assignments) for 're'
        assignments = []
        for n in ast.walk(node):
            if isinstance(n, ast.Name) and isinstance(n.ctx, ast.Store) and n.id == 're':
                assignments.append(n.lineno)
        
        if assignments:
            print(f"Found {len(assignments)} assignments to 're' at lines: {sorted(set(assignments))}")
            sys.exit(1)
        else:
            print("No direct assignments to 're' found")
            
        # Check for unpacking assignments
        for n in ast.walk(node):
            if isinstance(n, ast.Tuple) or isinstance(n, ast.List):
                for elt in n.elts:
                    if isinstance(elt, ast.Name) and elt.id == 're':
                        print(f"Found 're' in unpacking at line {elt.lineno}")
                        sys.exit(1)
            
            # Check for comprehensions
            if isinstance(n, (ast.ListComp, ast.SetComp, ast.DictComp, ast.GeneratorExp)):
                for gen in n.generators:
                    if isinstance(gen.target, ast.Name) and gen.target.id == 're':
                        print(f"Found 're' as comprehension variable at line {gen.target.lineno}")
                        sys.exit(1)

print("No problematic assignments found")

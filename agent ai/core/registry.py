"""
Dynamic Function Registry System
Enables auto-discovery and registration of tool functions
"""

from typing import Dict, List, Callable, Any, Optional
from functools import wraps
import inspect


class ActionRegistry:
    """Central registry for all executable actions"""
    
    def __init__(self):
        self._registry: Dict[str, Dict[str, Any]] = {}
        self._functions: Dict[str, Callable] = {}
    
    def register(
        self, 
        action_name: str,
        executor_action: Optional[str] = None,
        depends_on: Optional[List[str]] = None,
        keywords: Optional[List[str]] = None,
        category: str = "general",
        description: str = ""
    ):
        """
        Decorator to register a function as an executable action
        
        Args:
            action_name: Unique action identifier
            executor_action: Name of executor handler (defaults to action_name)
            depends_on: List of actions that must run first
            keywords: Keywords that trigger this action
            category: Action category (data, grouping, assignment, etc.)
            description: Human-readable description
        """
        def decorator(func: Callable):
            executor_act = executor_action or action_name
            deps = depends_on or []
            kws = keywords or []
            
            # Store function
            self._functions[action_name] = func
            
            # Store metadata
            self._registry[action_name] = {
                "executor_action": executor_act,
                "depends_on": deps,
                "keywords": kws,
                "category": category,
                "description": description,
                "function": func,
                "signature": str(inspect.signature(func))
            }
            
            print(f"[REGISTRY] Registered action: {action_name}")
            return func
        
        return decorator
    
    def get_action_rules(self) -> Dict[str, Dict]:
        """Get ACTION_RULES format for backwards compatibility"""
        rules = {}
        for action_name, metadata in self._registry.items():
            rules[action_name] = {
                "depends_on": metadata["depends_on"],
                "executor_action": metadata["executor_action"]
            }
        return rules
    
    def get_keywords_mapping(self) -> Dict[str, List[str]]:
        """Get keyword to action mapping"""
        keywords_map = {}
        for action_name, metadata in self._registry.items():
            for keyword in metadata["keywords"]:
                if keyword not in keywords_map:
                    keywords_map[keyword] = []
                keywords_map[keyword].append(action_name)
        return keywords_map
    
    def get_function(self, action_name: str) -> Optional[Callable]:
        """Get function by action name"""
        return self._functions.get(action_name)
    
    def get_metadata(self, action_name: str) -> Optional[Dict]:
        """Get metadata for action"""
        return self._registry.get(action_name)
    
    def list_actions(self, category: Optional[str] = None) -> List[str]:
        """List all registered actions, optionally filtered by category"""
        if category:
            return [
                name for name, meta in self._registry.items()
                if meta["category"] == category
            ]
        return list(self._registry.keys())
    
    def list_by_category(self) -> Dict[str, List[str]]:
        """List actions grouped by category"""
        by_cat = {}
        for action_name, metadata in self._registry.items():
            cat = metadata["category"]
            if cat not in by_cat:
                by_cat[cat] = []
            by_cat[cat].append(action_name)
        return by_cat
    
    def is_registered(self, action_name: str) -> bool:
        """Check if action is registered"""
        return action_name in self._registry
    
    def get_dependencies(self, action_name: str) -> List[str]:
        """Get all dependencies (recursive) for an action"""
        deps = set()
        
        def add_deps(action):
            if action not in self._registry:
                return
            
            for dep in self._registry[action].get("depends_on", []):
                if dep not in deps:
                    deps.add(dep)
                    add_deps(dep)
        
        add_deps(action_name)
        return list(deps)
    
    def __repr__(self):
        return f"ActionRegistry({len(self._registry)} actions)"


# Global registry instance
registry = ActionRegistry()


def print_registry_summary():
    """Print summary of registered actions"""
    by_cat = registry.list_by_category()
    
    print("\n" + "="*60)
    print("📋 ACTION REGISTRY SUMMARY")
    print("="*60)
    
    for cat in sorted(by_cat.keys()):
        actions = by_cat[cat]
        print(f"\n{cat.upper()} ({len(actions)} actions):")
        for action in sorted(actions):
            meta = registry.get_metadata(action)
            deps = f" → {meta['depends_on']}" if meta['depends_on'] else ""
            print(f"  • {action}{deps}")
    
    print(f"\n{'='*60}")
    print(f"Total: {len(registry.list_actions())} actions registered")
    print("="*60 + "\n")

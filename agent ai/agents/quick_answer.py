"""
Quick Answer - Generate quick responses to queries
"""

def generate_quick_answer(query, context_data=None):
    """
    Generate a quick answer to a user query
    
    Args:
        query: The user's query
        context_data: Optional context information
    
    Returns:
        dict: Response with type and content
    """
    # Stub implementation
    return {
        "type": "quick_answer",
        "content": f"Pertanyaan diterima: {query}"
    }

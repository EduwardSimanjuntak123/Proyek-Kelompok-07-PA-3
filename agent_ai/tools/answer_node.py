from core.llm import call_llm

def answer_node(state):
    
    # kalau ada result dari tools → langsung jawab
    if state.get("result"):
        answer = state["result"]
    else:
        answer = call_llm(state["messages"])

    state["messages"].append({
        "role": "assistant",
        "content": answer
    })

    print("AI:", answer)

    return state
from typing import TypedDict, List, Optional

class AgentState(TypedDict):
    messages: List[dict]
    plan: Optional[dict]
    result: Optional[str]
    grouping_payload: Optional[dict]
    grouping_meta: Optional[dict]
    pembimbing_payload: Optional[dict]
    pembimbing_meta: Optional[dict]
    penguji_payload: Optional[dict]
    penguji_meta: Optional[dict]
    excel_file_path: Optional[str]
    excel_filename: Optional[str]
    user_id: str
    session_id: str
    context: Optional[dict]
from typing import TypedDict, List, Optional, Any, Dict

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
    # ── Jadwal Seminar state ──────────────────────────────────────────
    # jadwal_stage  : "input_form" | "preview" | "completed" | None
    # jadwal_meta   : tanggal, ruangan_list, durasi_menit, kelompok_order
    # jadwal_entries: list of jadwal entry dicts dari preview terakhir
    # jadwal_actions: { can_save, can_acak }
    # request_data  : data tambahan dari frontend (jadwal_meta, jadwal_entries)
    #                 dikirim tiap request agar state bisa di-restore
    jadwal_stage: Optional[str]
    jadwal_meta: Optional[dict]
    jadwal_entries: Optional[list]
    jadwal_actions: Optional[dict]
    request_data: Optional[dict]
"""
Tools untuk generate jadwal seminar.

Fitur:
- Generate jadwal untuk semua kelompok
- Kelompok diacak
- 2 jam per kelompok
- Slot waktu: 08:00-11:50 (slot 1,2), 13:00-16:50 (slot 3,4)
- Pastikan pembimbing & penguji ada di ruangan
- Tanya detail jika user belum lengkap memberikan info
"""

from datetime import datetime, timedelta
from typing import Dict, List, Optional, Tuple
import random
import logging

from sqlalchemy import and_

from core.database import SessionLocal
from models.jadwal import Jadwal
from models.kelompok import Kelompok
from models.ruangan import Ruangan
from models.pembimbing import Pembimbing
from models.penguji import Penguji
from models.dosen import Dosen

logger = logging.getLogger(__name__)

# Akademik time slots (2 jam per slot)
MORNING_SLOTS = [
    (8, 0, 9, 50),    # 08:00 - 09:50
    (10, 0, 11, 50),  # 10:00 - 11:50
]

AFTERNOON_SLOTS = [
    (13, 0, 14, 50),  # 13:00 - 14:50
    (15, 0, 16, 50),  # 15:00 - 16:50
]

ALL_SLOTS = MORNING_SLOTS + AFTERNOON_SLOTS


def ask_for_schedule_details(prompt: str, dosen_context: Dict = None) -> Dict:
    """
    Tanya detail jadwal secara incremental.
    
    Stage 1 (dosen_context=None): Ask for ruangan selection only
    Stage 2 (dosen_context provided): Ask for tanggal selection only
    
    Returns dict dengan 'asking' = True jika perlu tanya detail + HTML dengan UI
    """
    session = SessionLocal()
    try:
        prompt_lower = prompt.lower()
        
        # STAGE 1: Initial request - ask for ruangan only
        if dosen_context is None:
            if "jadwal seminar" in prompt_lower or "seminar" in prompt_lower or "buat jadwal" in prompt_lower:
                # Build ruangan selection UI with JavaScript at the top
                ruangan_list = session.query(Ruangan).order_by(Ruangan.ruangan.asc()).all()
                
                # Add script tag FIRST, before any HTML that uses the functions
                html_message = """
<script>
window.handleRuanganSubmit = function(event) {
  event.preventDefault();
  const checkboxes = document.querySelectorAll('input[name="ruangan_ids"]:checked');
  const selectedIds = Array.from(checkboxes).map(cb => cb.value).join(', ');
  
  if (selectedIds.length === 0) {
    alert('Harap pilih minimal 1 ruangan');
    return;
  }
  
  // Convert to message format: "saya pilih ruangan 1, 2, 3"
  const message = `Saya pilih ruangan: ${selectedIds}`;
  
  // Send message back to the system
  if (window.__sendMessage) {
    window.__sendMessage(message);
  } else {
    console.error('sendMessage function not found');
  }
};
</script>
"""
                
                html_message += f"<p style='color:#ea580c;'><strong>ℹ️ Pilih Ruangan Untuk Seminar</strong></p>"
                html_message += "<p>Harap pilih ruangan yang akan digunakan (bisa lebih dari 1):</p>"
                html_message += "<div style='border:1px solid #ddd; padding:12px; border-radius:4px; background:#f9f9f9;'>"
                
                if ruangan_list:
                    for ruang in ruangan_list:
                        html_message += f"<div style='margin:8px 0;'>"
                        html_message += f"<label style='cursor:pointer; display:flex; align-items:center;'>"
                        html_message += f"<input type='checkbox' name='ruangan_ids' value='{ruang.id}' class='ruangan-checkbox' style='margin-right:8px;'> "
                        html_message += f"<span>{ruang.ruangan}</span>"
                        html_message += f"</label>"
                        html_message += f"</div>"
                else:
                    html_message += "<p style='color:#999;'>Tidak ada ruangan tersedia</p>"
                
                html_message += "</div>"
                
                # Add Submit button with JavaScript handler
                html_message += """
<div style='margin-top:16px;'>
  <button 
    id='submit-ruangan-btn' 
    class='btn btn-primary' 
    style='padding:10px 20px; background:#2563eb; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;'
    onclick='handleRuanganSubmit(event)'
  >
    📤 Kirim Pilihan
  </button>
</div>
"""
                
                session.close()
                return {
                    "asking": True,
                    "message": html_message,
                    "stage": "ruangan_selection",
                    "ruangan_list": [{"id": r.id, "name": r.ruangan} for r in ruangan_list],
                }
        
        # STAGE 2: After ruangan selection - ask for tanggal only
        else:
            # At this stage, we already have ruangan, just ask for tanggal
            # Don't check for "seminar" keyword since user might just send date
            
            # Check if tanggal is already provided
            has_tanggal = any(word in prompt_lower for word in ["tanggal", "tgl", "maret", "april", "mei", "juni", "juli", "agustus", "september"])
            
            if not has_tanggal:
                # Add script tag FIRST, before any HTML that uses the functions
                html_message = """
<script>
window.handleTanggalSubmit = function(event) {
  event.preventDefault();
  const tanggalInput = document.getElementById('tanggal-input');
  const tanggal = tanggalInput.value.trim();
  
  if (!tanggal) {
    alert('Harap masukkan tanggal terlebih dahulu');
    return;
  }
  
  // Send tanggal back to the system
  if (window.__sendMessage) {
    window.__sendMessage(tanggal);
  } else {
    console.error('sendMessage function not found');
  }
};

window.handleTanggalKeypress = function(event) {
  if (event.key === 'Enter') {
    window.handleTanggalSubmit(event);
  }
};
</script>
"""
                
                # Build tanggal input UI
                html_message += f"<p style='color:#ea580c;'><strong>ℹ️ Sebutkan Tanggal Mulai Seminar</strong></p>"
                html_message += "<p>Harap sebutkan tanggal mulai seminar dalam format: <strong>tanggal bulan tahun</strong></p>"
                html_message += "<p>Contoh: <em>5 mei 2026</em>, <em>10 juni 2026</em>, dsb.</p>"
                html_message += """
<div style='border:1px solid #ddd; padding:12px; border-radius:4px; background:#f9f9f9; margin-top:12px;'>
  <input 
    type='text' 
    id='tanggal-input'
    class='form-control' 
    placeholder='Contoh: 5 mei 2026'
    style='padding:8px; border:1px solid #ccc; border-radius:4px; width:100%; font-size:14px;'
    onkeypress='handleTanggalKeypress(event)'
  />
</div>

<div style='margin-top:12px;'>
  <button 
    id='submit-tanggal-btn' 
    class='btn btn-primary' 
    style='padding:10px 20px; background:#2563eb; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;'
    onclick='handleTanggalSubmit(event)'
  >
    📤 Kirim Tanggal
  </button>
</div>
"""
                
                session.close()
                return {
                    "asking": True,
                    "message": html_message,
                    "stage": "tanggal_selection",
                }
        
        session.close()
        return {"asking": False}
    
    except Exception as e:
        session.close()
        logger.error(f"Error in ask_for_schedule_details: {e}")
        return {
            "asking": False,
            "error": str(e),
        }


def parse_schedule_input(user_id: int, prompt: str, dosen_context: Dict, ruangan_ids: List[int] = None) -> Dict:
    """
    Parse input user untuk extract tanggal mulai dan ruangan.
    
    Returns:
        Dict dengan tanggal_mulai, ruangan_ids (list), prodi_id, kategori_pa_id, angkatan_id
    """
    session = SessionLocal()
    try:
        prompt_lower = prompt.lower()
        
        # Extract tanggal - simple parsing (format: "5 mei", "5/5", etc)
        tanggal_mulai = None
        
        # Ekstrak hari dari prompt (misal "5 mei 2026")
        import re
        date_pattern = r'(\d{1,2})\s*(mei|april|maret|juni|juli|agustus|september)\s*(?:(\d{4}))?'
        date_match = re.search(date_pattern, prompt_lower)
        
        if date_match:
            day = int(date_match.group(1))
            month_str = date_match.group(2).lower()
            year = int(date_match.group(3)) if date_match.group(3) else datetime.now().year
            
            month_map = {
                'januari': 1, 'februari': 2, 'maret': 3, 'april': 4,
                'mei': 5, 'juni': 6, 'juli': 7, 'agustus': 8,
                'september': 9, 'oktober': 10, 'november': 11, 'desember': 12
            }
            
            if month_str in month_map:
                try:
                    tanggal_mulai = datetime(year, month_map[month_str], day)
                except ValueError:
                    pass
        
        # Use provided ruangan_ids atau extract dari prompt
        final_ruangan_ids = []
        ruangan_names = []
        
        if ruangan_ids and len(ruangan_ids) > 0:
            # Use provided IDs
            final_ruangan_ids = ruangan_ids
            # Get ruangan names
            for rid in ruangan_ids:
                ruang = session.query(Ruangan).filter(Ruangan.id == rid).first()
                if ruang:
                    ruangan_names.append(ruang.ruangan)
        else:
            # Try extract from prompt
            ruangan_query = session.query(Ruangan).all()
            for ruang in ruangan_query:
                if ruang.ruangan and ruang.ruangan.lower() in prompt_lower:
                    final_ruangan_ids.append(ruang.id)
                    ruangan_names.append(ruang.ruangan)
        
        session.close()
        
        return {
            "tanggal_mulai": tanggal_mulai,
            "ruangan_ids": final_ruangan_ids,
            "ruangan_names": ruangan_names,
            "prodi_id": dosen_context.get("prodi_id"),
            "kategori_pa_id": dosen_context.get("kategori_pa"),
            "angkatan_id": dosen_context.get("angkatan"),
        }
    except Exception as e:
        session.close()
        logger.error(f"Error parsing schedule input: {e}")
        return {
            "error": str(e),
            "tanggal_mulai": None,
            "ruangan_ids": [],
        }


def generate_seminar_schedule(
    tanggal_mulai: datetime,
    ruangan_ids: List[int],
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
) -> Dict:
    """
    Generate jadwal seminar untuk kelompok ke multiple ruangan.
    
    Algoritma:
    1. Ambil semua kelompok dari konteks
    2. Randomize urutan
    3. Assign ke slot waktu (2 jam per kelompok) di ruangan-ruangan
    4. Round-robin distribute ke ruangan
    5. Cek pembimbing & penguji tersedia
    6. Simpan ke database
    
    Args:
        tanggal_mulai: Tanggal mulai seminar (datetime)
        ruangan_ids: List of ruangan IDs untuk seminar
        prodi_id: Filter kelompok by prodi
        kategori_pa_id: Filter kelompok by kategori PA
        angkatan_id: Filter kelompok by angkatan
        
    Returns:
        Dict dengan status, jadwal_list, dan summary
    """
    session = SessionLocal()
    try:
        if not ruangan_ids or len(ruangan_ids) == 0:
            session.close()
            return {
                "status": "error",
                "message": "Minimal harus ada 1 ruangan untuk seminar",
            }
        
        # 1. Ambil kelompok dari konteks
        kelompok_query = session.query(Kelompok)
        if prodi_id:
            kelompok_query = kelompok_query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            kelompok_query = kelompok_query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            kelompok_query = kelompok_query.filter(Kelompok.TM_id == angkatan_id)
        
        kelompoks = kelompok_query.order_by(Kelompok.id.asc()).all()
        
        if not kelompoks:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada kelompok pada konteks ini",
            }
        
        # 2. Randomize urutan kelompok
        random.shuffle(kelompoks)
        
        # Get ruangan names
        ruangan_map = {}
        for rid in ruangan_ids:
            ruang = session.query(Ruangan).filter(Ruangan.id == rid).first()
            if ruang:
                ruangan_map[rid] = ruang.ruangan
        
        # 3. Prepare slot schedule dengan round-robin ke ruangan
        jadwal_list = []
        slot_index = 0
        current_date = tanggal_mulai
        ruangan_round_robin_index = 0
        
        for kelompok in kelompoks:
            # Tentukan ruangan dengan round-robin
            current_ruangan_id = ruangan_ids[ruangan_round_robin_index % len(ruangan_ids)]
            
            # Tentukan slot waktu
            if slot_index >= len(ALL_SLOTS):
                # Pindah ke hari berikutnya
                current_date = current_date + timedelta(days=1)
                slot_index = 0
            
            slot = ALL_SLOTS[slot_index]
            start_hour, start_min, end_hour, end_min = slot
            
            waktu_mulai = current_date.replace(hour=start_hour, minute=start_min, second=0)
            waktu_selesai = current_date.replace(hour=end_hour, minute=end_min, second=0)
            
            # 4. Cek pembimbing & penguji
            pembimbing_list = session.query(Pembimbing).filter(
                Pembimbing.kelompok_id == kelompok.id
            ).all()
            
            penguji_list = session.query(Penguji).filter(
                Penguji.kelompok_id == kelompok.id
            ).all()
            
            if not pembimbing_list:
                logger.warning(f"Kelompok {kelompok.nomor_kelompok} tidak ada pembimbing")
                continue
            
            if not penguji_list or len(penguji_list) < 2:
                logger.warning(f"Kelompok {kelompok.nomor_kelompok} tidak ada 2 penguji")
                continue
            
            # Get dosen names
            pembimbing_names = []
            for pb in pembimbing_list:
                dosen = session.query(Dosen).filter(Dosen.user_id == pb.user_id).first()
                if dosen:
                    pembimbing_names.append(dosen.nama)
            
            penguji_names = []
            for pg in penguji_list:
                dosen = session.query(Dosen).filter(Dosen.user_id == pg.user_id).first()
                if dosen:
                    penguji_names.append(dosen.nama)
            
            jadwal_entry = {
                "kelompok_id": kelompok.id,
                "kelompok_nomor": kelompok.nomor_kelompok,
                "waktu_mulai": waktu_mulai.isoformat(),
                "waktu_selesai": waktu_selesai.isoformat(),
                "ruangan_id": current_ruangan_id,
                "ruangan_name": ruangan_map.get(current_ruangan_id, f"Ruangan {current_ruangan_id}"),
                "pembimbing": pembimbing_names,
                "penguji": penguji_names,
                "num_pembimbing": len(pembimbing_list),
                "num_penguji": len(penguji_list),
            }
            
            jadwal_list.append(jadwal_entry)
            
            # Move ke slot berikutnya
            slot_index += 1
            ruangan_round_robin_index += 1
        
        if not jadwal_list:
            session.close()
            return {
                "status": "error",
                "message": "Tidak ada kelompok yang memiliki pembimbing dan penguji lengkap",
            }
        
        session.close()
        
        return {
            "status": "success",
            "message": f"Jadwal seminar berhasil dibuat untuk {len(jadwal_list)} kelompok",
            "summary": {
                "total_kelompok": len(kelompoks),
                "scheduled_kelompok": len(jadwal_list),
                "tanggal_mulai": tanggal_mulai.isoformat(),
                "ruangan_ids": ruangan_ids,
                "ruangan_count": len(ruangan_ids),
            },
            "jadwal_list": jadwal_list,
        }
    
    except Exception as e:
        session.close()
        logger.error(f"Error generating schedule: {e}")
        return {
            "status": "error",
            "message": f"Error: {str(e)}",
        }


def save_jadwal_to_database(
    jadwal_list: List[Dict],
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    user_id: int = None,
) -> Dict:
    """
    Simpan jadwal ke database.
    
    Args:
        jadwal_list: List of jadwal entries dari generate_seminar_schedule
        prodi_id, kategori_pa_id, angkatan_id: Context
        user_id: Dosen yang membuat jadwal
        
    Returns:
        Dict dengan status simpan
    """
    session = SessionLocal()
    try:
        now = datetime.now()
        
        for jadwal_entry in jadwal_list:
            jadwal_record = Jadwal(
                kelompok_id=jadwal_entry["kelompok_id"],
                waktu_mulai=datetime.fromisoformat(jadwal_entry["waktu_mulai"]),
                waktu_selesai=datetime.fromisoformat(jadwal_entry["waktu_selesai"]),
                user_id=user_id,
                ruangan_id=jadwal_entry["ruangan_id"],
                KPA_id=kategori_pa_id,
                prodi_id=prodi_id,
                TM_id=angkatan_id,
                created_at=now,
                updated_at=now,
            )
            session.add(jadwal_record)
        
        session.commit()
        session.close()
        
        return {
            "status": "success",
            "message": f"✓ {len(jadwal_list)} jadwal berhasil disimpan ke database",
            "count": len(jadwal_list),
        }
    
    except Exception as e:
        session.rollback()
        session.close()
        logger.error(f"Error saving jadwal: {e}")
        return {
            "status": "error",
            "message": f"Error saving jadwal: {str(e)}",
        }

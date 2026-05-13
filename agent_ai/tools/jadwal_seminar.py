"""
Tools untuk manage jadwal seminar feature
"""
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Tuple
import re
from sqlalchemy import func
from core.database import SessionLocal
from models.jadwal import Jadwal
from models.kelompok import Kelompok
from models.ruangan import Ruangan
from models.pembimbing import Pembimbing
from models.penguji import Penguji
import logging

logger = logging.getLogger(__name__)


class JadwalSeminarTools:
    """Tools untuk manage jadwal seminar"""
    
    # Durasi standard: 1 jam 50 menit (110 menit)
    DURATION_MINUTES = 110
    
    # Time slots untuk seminar (4 slots per hari)
    TIME_SLOTS = [
        ("08:00", "09:50"),      # Slot 1: 08:00-09:50
        ("10:00", "11:50"),      # Slot 2: 10:00-11:50
        ("13:00", "14:50"),      # Slot 3: 13:00-14:50 (afternoon)
        ("15:00", "16:50"),      # Slot 4: 15:00-16:50 (afternoon)
    ]

    @staticmethod
    def get_form_jadwal(ruangan_list: Optional[List] = None) -> Dict:
        """
        Generate form HTML untuk input jadwal seminar
        
        Returns:
            Dict dengan form HTML
        """
        session = SessionLocal()
        try:
            # Ambil daftar ruangan dari database
            if ruangan_list is None:
                ruangan_list = session.query(Ruangan).order_by(Ruangan.ruangan.asc()).all()
            
            # Build ruangan options HTML
            ruangan_options = '<option value="">-- Pilih Ruangan --</option>\n'
            for ruang in ruangan_list:
                ruangan_options += f"                    <option value='{ruang.id}'>{ruang.ruangan}</option>\n"
            
            html = f"""
<div style='border:1px solid #ddd; padding:16px; border-radius:4px; background:#f9f9f9;'>
    <p style='color:#ea580c; margin:0 0 16px 0;'><strong>📅 Input Jadwal Seminar</strong></p>
    
    <div style='margin-bottom:12px;'>
        <label style='display:block; margin-bottom:4px; font-weight:bold;'>Tanggal Mulai Seminar *</label>
        <input 
            type='text' 
            id='jadwal-tanggal' 
            placeholder='Contoh: 15 mei 2026'
            style='width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; font-size:14px; box-sizing:border-box;'
        />
        <small style='color:#666; display:block; margin-top:4px;'>Format: tanggal bulan tahun (contoh: 15 mei 2026, 10 juni 2026)</small>
    </div>
    
    <div style='margin-bottom:12px;'>
        <label style='display:block; margin-bottom:4px; font-weight:bold;'>Ruangan * (Tambahkan lebih dari 1 ruangan jika perlu)</label>
        <div id='jadwal-ruangan-container' style='border:1px solid #e0e0e0; padding:8px; border-radius:4px; background:white;'>
            <div style='display:flex; gap:8px; margin-bottom:8px; align-items:center;' class='ruangan-row'>
                <select 
                    class='jadwal-ruangan-select'
                    style='flex:1; padding:8px; border:1px solid #ccc; border-radius:4px; font-size:14px;'
                >
{ruangan_options}                </select>
                <button 
                    type='button' 
                    class='remove-ruangan-btn'
                    style='padding:8px 12px; background:#ef4444; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold; display:none; min-width:40px;'
                >
                    ✕
                </button>
            </div>
        </div>
        <button 
            type='button' 
            id='add-ruangan-btn'
            style='margin-top:8px; padding:8px 12px; background:#059669; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold; font-size:13px; width:100%;'
        >
            + Tambah Ruangan
        </button>
    </div>
    
    <div style='margin-bottom:16px;'>
        <label style='display:block; margin-bottom:4px; font-weight:bold;'>Durasi</label>
        <div style='display:flex; gap:8px; align-items:flex-end;'>
            <div style='flex:1;'>
                <label style='font-size:13px; color:#666;'>Jam</label>
                <input 
                    type='number' 
                    id='jadwal-durasi-jam' 
                    value='1' 
                    min='0' 
                    max='8'
                    style='width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; font-size:14px; box-sizing:border-box;'
                />
            </div>
            <div style='flex:1;'>
                <label style='font-size:13px; color:#666;'>Menit</label>
                <input 
                    type='number' 
                    id='jadwal-durasi-menit' 
                    value='50' 
                    min='0' 
                    max='59'
                    style='width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; font-size:14px; box-sizing:border-box;'
                />
            </div>
        </div>
        <small style='color:#666; display:block; margin-top:4px;'>Default: 1 jam 50 menit (110 menit total)</small>
    </div>
    
    <button 
        type='button' 
        id='submit-jadwal-btn'
        style='width:100%; padding:10px; background:#2563eb; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold; font-size:14px;'
    >
        📤 Buat Jadwal Seminar
    </button>
</div>

<script>
(function() {{
    // Setup add ruangan button
    const addBtn = document.getElementById('add-ruangan-btn');
    if (addBtn) {{
        addBtn.addEventListener('click', function(e) {{
            e.preventDefault();
            const container = document.getElementById('jadwal-ruangan-container');
            if (!container) return;
            
            const rowCount = container.querySelectorAll('.ruangan-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'ruangan-row';
            newRow.style.display = 'flex';
            newRow.style.gap = '8px';
            newRow.style.marginBottom = '8px';
            newRow.style.alignItems = 'center';
            
            const select = document.createElement('select');
            select.className = 'jadwal-ruangan-select';
            select.style.flex = '1';
            select.style.padding = '8px';
            select.style.border = '1px solid #ccc';
            select.style.borderRadius = '4px';
            select.style.fontSize = '14px';
            select.innerHTML = '<option value="">-- Pilih Ruangan ' + (rowCount + 1) + ' --</option>{ruangan_options}';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-ruangan-btn';
            removeBtn.style.padding = '8px 12px';
            removeBtn.style.background = '#ef4444';
            removeBtn.style.color = 'white';
            removeBtn.style.border = 'none';
            removeBtn.style.borderRadius = '4px';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.fontWeight = 'bold';
            removeBtn.style.minWidth = '40px';
            removeBtn.textContent = '✕';
            
            removeBtn.addEventListener('click', function(e) {{
                e.preventDefault();
                newRow.remove();
                updateRemoveButtons();
            }});
            
            newRow.appendChild(select);
            newRow.appendChild(removeBtn);
            container.appendChild(newRow);
            
            updateRemoveButtons();
        }});
    }}
    
    function updateRemoveButtons() {{
        const container = document.getElementById('jadwal-ruangan-container');
        if (!container) return;
        const rows = container.querySelectorAll('.ruangan-row');
        document.querySelectorAll('.remove-ruangan-btn').forEach((btn) => {{
            btn.style.display = rows.length > 1 ? 'block' : 'none';
        }});
    }}
    
    // Setup submit button
    const submitBtn = document.getElementById('submit-jadwal-btn');
    if (submitBtn) {{
        submitBtn.addEventListener('click', function(e) {{
            e.preventDefault();
            if (window.__submitJadwal) {{
                window.__submitJadwal(e);
            }} else {{
                alert('Error: __submitJadwal function not found');
            }}
        }});
    }}
    
    // Initialize
    updateRemoveButtons();
}})();
</script>
"""
            
            session.close()
            return {
                "asking": True,
                "message": html,
                "stage": "jadwal_input",
                "ruangan_list": [{"id": r.id, "name": r.ruangan} for r in ruangan_list]
            }
        except Exception as e:
            logger.error(f"❌ Error generating jadwal form: {e}")
            session.close()
            return {
                "asking": False,
                "message": f"❌ Error: {str(e)}"
            }

    @staticmethod
    def parse_tanggal_input(tanggal_str: str, tahun: Optional[int] = None) -> Optional[datetime]:
        """
        Parse tanggal string ke datetime
        Support format: "15 mei 2026", "10 juni", dll
        
        Args:
            tanggal_str: String tanggal
            tahun: Tahun (jika tidak ada di string, gunakan tahun sekarang)
            
        Returns:
            datetime object atau None jika parsing gagal
        """
        try:
            tanggal_lower = tanggal_str.lower().strip()
            
            # Default tahun ke tahun sekarang jika tidak diberikan
            if tahun is None:
                tahun = datetime.now().year
            
            # Dictionary bulan
            bulan_map = {
                'januari': 1, 'jan': 1, '01': 1,
                'februari': 2, 'feb': 2, '02': 2,
                'maret': 3, 'mar': 3, '03': 3,
                'april': 4, 'apr': 4, '04': 4,
                'mei': 5, '05': 5,
                'juni': 6, 'jun': 6, '06': 6,
                'juli': 7, 'jul': 7, '07': 7,
                'agustus': 8, 'agt': 8, '08': 8,
                'september': 9, 'sep': 9, '09': 9,
                'oktober': 10, 'okt': 10, '10': 10,
                'november': 11, 'nov': 11, '11': 11,
                'desember': 12, 'des': 12, '12': 12,
            }
            
            # Pattern: "15 mei 2026" atau "15 mei"
            pattern = r'(\d{1,2})\s+(\w+)(?:\s+(\d{4}))?'
            match = re.search(pattern, tanggal_lower)
            
            if match:
                hari = int(match.group(1))
                bulan_str = match.group(2)
                tahun_match = match.group(3)
                
                if tahun_match:
                    tahun = int(tahun_match)
                
                # Cari bulan
                bulan = None
                for key, value in bulan_map.items():
                    if bulan_str.startswith(key[:3]):  # Match first 3 chars
                        bulan = value
                        break
                
                if bulan:
                    return datetime(tahun, bulan, hari)
            
            logger.warning(f"⚠️ Could not parse tanggal: {tanggal_str}")
            return None
        except Exception as e:
            logger.error(f"❌ Error parsing tanggal: {e}")
            return None

    @staticmethod
    def get_kelompok_for_jadwal(user_id: int, dosen_context: List[Dict]) -> List[Kelompok]:
        """
        Get kelompok yang sesuai dengan dosen context untuk dijadwalkan
        
        Kriteria:
        - Kelompok dengan prodi_id sesuai dosen context
        - Kelompok dengan TahunMasuk (angkatan) sesuai dosen context
        - Kelompok dengan KPA (kategori_pa) sesuai dosen context
        
        Args:
            user_id: User ID dosen
            dosen_context: List of dosen context dengan format:
                [
                    {"prodi_id": 4, "angkatan": 2, "kategori_pa": 3, ...},
                    ...
                ]
        
        Returns:
            List of Kelompok objects
        """
        session = SessionLocal()
        try:
            if not dosen_context:
                logger.warning(f"⚠️ dosen_context is empty")
                return []
            
            # Extract unique prodi, angkatan (TM_id), dan kategori_pa (KPA_id) dari dosen context
            prodi_ids = set()
            tm_ids = set()
            kpa_ids = set()
            
            for context in dosen_context:
                if context.get("prodi_id"):
                    prodi_ids.add(context["prodi_id"])
                # Field name: "angkatan" in dosen_context maps to TM_id in Kelompok model
                if context.get("angkatan"):
                    tm_ids.add(context["angkatan"])
                # Field name: "kategori_pa" in dosen_context maps to KPA_id in Kelompok model
                if context.get("kategori_pa"):
                    kpa_ids.add(context["kategori_pa"])
            
            logger.info(f"[{user_id}] 🔍 Searching kelompok with: prodi_ids={prodi_ids}, tm_ids={tm_ids}, kpa_ids={kpa_ids}")
            
            # Query kelompok
            query = session.query(Kelompok)
            
            if prodi_ids:
                query = query.filter(Kelompok.prodi_id.in_(prodi_ids))
            if tm_ids:
                query = query.filter(Kelompok.TM_id.in_(tm_ids))
            if kpa_ids:
                query = query.filter(Kelompok.KPA_id.in_(kpa_ids))
            
            kelompok_list = query.all()
            logger.info(f"[{user_id}] ✓ Found {len(kelompok_list)} kelompok for jadwal")
            
            if not kelompok_list:
                logger.warning(f"[{user_id}] ⚠️  No kelompok found with given filters")
            
            session.close()
            return kelompok_list
        except Exception as e:
            logger.error(f"[{user_id}] ❌ Error getting kelompok: {e}")
            session.close()
            return []

    @staticmethod
    def generate_jadwal_seminar(
        user_id: int,
        tanggal_mulai: datetime,
        durasi_menit: int,
        ruangan_list: List[int],
        kelompok_list: List[Kelompok],
        dosen_context: List[Dict]
    ) -> Dict:
        """
        Generate dan simpan jadwal seminar ke database untuk multiple ruangan
        
        Distribusi: Round-robin across kelompok per ruangan
        - Setiap ruangan mendapat jadwal untuk semua kelompok
        - Kelompok didistribusi across time slots untuk setiap ruangan
        
        Args:
            user_id: Koordinator user_id
            tanggal_mulai: Tanggal mulai jadwal
            durasi_menit: Durasi per seminar (menit)
            ruangan_list: List of ruangan IDs
            kelompok_list: List of Kelompok to schedule
            dosen_context: Dosen context untuk extract KPA, prodi, TM
            
        Returns:
            Dict dengan hasil jadwal
        """
        session = SessionLocal()
        try:
            if not kelompok_list:
                return {"success": False, "message": "Tidak ada kelompok untuk dijadwalkan"}
            
            if not ruangan_list:
                return {"success": False, "message": "Minimal 1 ruangan harus dipilih"}
            
            # Get info from first kelompok (assume sama untuk semua)
            sample_kelompok = kelompok_list[0]
            kpa_id = sample_kelompok.KPA_id
            prodi_id = sample_kelompok.prodi_id
            tm_id = sample_kelompok.TM_id
            
            jadwal_entries = []
            
            # Generate jadwal untuk setiap ruangan
            for ruangan_id in ruangan_list:
                current_date = tanggal_mulai
                slot_index = 0
                
                # Iterate through kelompok dan assign jadwal
                for kelompok in kelompok_list:
                    # Get time slot (round-robin)
                    time_slot = JadwalSeminarTools.TIME_SLOTS[slot_index % len(JadwalSeminarTools.TIME_SLOTS)]
                    
                    # Parse waktu mulai dan selesai
                    time_parts = time_slot[0].split(':')
                    waktu_mulai = current_date.replace(hour=int(time_parts[0]), minute=int(time_parts[1]), second=0)
                    
                    time_parts_end = time_slot[1].split(':')
                    waktu_selesai = current_date.replace(hour=int(time_parts_end[0]), minute=int(time_parts_end[1]), second=0)
                    
                    # Create jadwal entry
                    jadwal = Jadwal(
                        kelompok_id=kelompok.id,
                        waktu_mulai=waktu_mulai,
                        waktu_selesai=waktu_selesai,
                        user_id=user_id,
                        ruangan_id=ruangan_id,
                        KPA_id=kpa_id,
                        prodi_id=prodi_id,
                        TM_id=tm_id,
                        created_at=datetime.now(),
                        updated_at=datetime.now()
                    )
                    
                    session.add(jadwal)
                    jadwal_entries.append({
                        "kelompok_id": kelompok.id,
                        "tanggal": waktu_mulai.strftime("%d %b %Y"),
                        "waktu": f"{time_slot[0]} - {time_slot[1]}",
                        "ruangan_id": ruangan_id
                    })
                    
                    # Move to next slot
                    slot_index += 1
                    
                    # If slot wraps, move to next day
                    if slot_index % len(JadwalSeminarTools.TIME_SLOTS) == 0:
                        current_date += timedelta(days=1)
            
            # Commit to database
            session.commit()
            logger.info(f"✓ Created {len(jadwal_entries)} jadwal entries untuk {len(ruangan_list)} ruangan")
            
            # Generate HTML response dengan jadwal yang dibuat
            html = f"<p style='color:#059669; font-weight:bold;'>✅ Jadwal Seminar Berhasil Dibuat untuk {len(ruangan_list)} Ruangan!</p>"
            html += "<table style='width:100%; border-collapse:collapse; margin-top:12px; font-size:13px;'>"
            html += "<tr style='background:#f0f0f0; border:1px solid #ddd;'>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>No</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Kelompok</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Ruangan</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Tanggal</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Waktu</th>"
            html += "</tr>"
            
            for idx, entry in enumerate(jadwal_entries, 1):
                html += f"<tr style='border:1px solid #ddd;'>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{idx}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>Kelompok {entry['kelompok_id']}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>Ruangan {entry['ruangan_id']}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry['tanggal']}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry['waktu']}</td>"
                html += f"</tr>"
            
            html += "</table>"
            html += f"<p style='margin-top:12px; color:#666;'><small>Total: {len(jadwal_entries)} jadwal dibuat ({len(ruangan_list)} ruangan × {len(kelompok_list)} kelompok)</small></p>"
            
            session.close()
            return {
                "success": True,
                "message": html,
                "total": len(jadwal_entries),
                "jadwal_entries": jadwal_entries
            }
        except Exception as e:
            session.rollback()
            logger.error(f"❌ Error generating jadwal: {e}")
            session.close()
            return {
                "success": False,
                "message": f"❌ Error: {str(e)}"
            }

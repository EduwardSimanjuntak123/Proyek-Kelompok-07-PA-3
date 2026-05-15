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
<div style='margin:8px 0; padding:12px; border:1px solid #c7d2fe; border-radius:14px; background:linear-gradient(180deg, #eff6ff 0%, #ffffff 100%); box-shadow:0 12px 24px rgba(59,130,246,0.1);'>
    <div style='display:flex; gap:10px; align-items:center; margin-bottom:10px;'>
        <div style='width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, #3b82f6, #1d4ed8); display:flex; align-items:center; justify-content:center; color:white; font-size:18px; box-shadow:0 6px 16px rgba(59,130,246,0.2);'>📅</div>
        <div>
            <h3 style='margin:0; color:#1e3a8a; font-size:18px; font-weight:700;'>Input Jadwal Seminar</h3>
            <p style='margin:2px 0 0 0; color:#4b5563; font-size:12px;'>Isi tanggal, pilih ruangan, dan tentukan durasi.</p>
        </div>
    </div>

    <div style='background:rgba(255,255,255,0.86); border:1px solid #dbeafe; border-radius:12px; padding:12px;'>
        <div style='margin-bottom:12px;'>
            <label style='display:flex; align-items:center; gap:6px; margin-bottom:4px; font-weight:700; color:#1f2937; font-size:13px;'>
                <span>📆</span>
                <span>Tanggal Mulai Seminar *</span>
            </label>
            <input 
                type='date' 
                id='jadwal-tanggal' 
                style='width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px; font-size:13px; box-sizing:border-box; background:#f8fbff; color:#0f172a; transition:all 0.2s ease; outline:none;'
                onFocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'; this.style.background='#ffffff';"
                onBlur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.background='#f8fbff';"
            />
            <small style='color:#64748b; display:block; margin-top:4px; font-size:11px;'>Pilih tanggal dari kalender.</small>
        </div>

        <div style='margin-bottom:12px;'>
            <label style='display:flex; align-items:center; gap:6px; margin-bottom:4px; font-weight:700; color:#1f2937; font-size:13px;'>
                <span>🏫</span>
                <span>Ruangan * (boleh lebih dari satu)</span>
            </label>
            <div id='jadwal-ruangan-container' style='border:1px solid #dbeafe; padding:10px; border-radius:10px; background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%); max-height:180px; overflow-y:auto;'>
                <div style='display:flex; gap:6px; margin-bottom:6px; align-items:center;' class='ruangan-row'>
                    <select 
                        class='jadwal-ruangan-select'
                        style='flex:1; padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px; font-size:13px; background:white; cursor:pointer; transition:all 0.2s ease; outline:none;'
                    >
{ruangan_options}                    </select>
                    <button 
                        type='button' 
                        class='remove-ruangan-btn'
                        style='padding:10px 12px; background:#ef4444; color:white; border:none; border-radius:10px; cursor:pointer; font-weight:700; display:none; min-width:44px; box-shadow:0 6px 12px rgba(239,68,68,0.18);'
                        onMouseOver="this.style.background='#dc2626';"
                        onMouseOut="this.style.background='#ef4444';"
                    >
                        ✕
                    </button>
                </div>
            </div>
            <button 
                type='button' 
                id='add-ruangan-btn'
                style='margin-top:8px; padding:10px 12px; background:linear-gradient(135deg, #10b981, #059669); color:white; border:none; border-radius:10px; cursor:pointer; font-weight:700; font-size:12px; width:100%; box-shadow:0 8px 16px rgba(16,185,129,0.15); transition:transform 0.2s ease, box-shadow 0.2s ease;'
                onMouseOver="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 12px 20px rgba(16,185,129,0.2)';"
                onMouseOut="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 16px rgba(16,185,129,0.15)';"
            >
                + Tambah Ruangan
            </button>
        </div>

        <div style='margin-bottom:10px;'>
            <label style='display:flex; align-items:center; gap:6px; margin-bottom:4px; font-weight:700; color:#1f2937; font-size:13px;'>
                <span>⏱️</span>
                <span>Durasi Seminar</span>
            </label>
            <div style='display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px;'>
                <div style='background:#fff; border:1px solid #dbeafe; border-radius:10px; padding:8px;'>
                    <label style='font-size:11px; color:#64748b; margin-bottom:3px; display:block;'>Jam</label>
                    <input 
                        type='number' 
                        id='jadwal-durasi-jam' 
                        value='1' 
                        min='0' 
                        max='8'
                        style='width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; box-sizing:border-box; background:#f8fbff; outline:none;'
                    />
                </div>
                <div style='background:#fff; border:1px solid #dbeafe; border-radius:10px; padding:8px;'>
                    <label style='font-size:11px; color:#64748b; margin-bottom:3px; display:block;'>Menit</label>
                    <input 
                        type='number' 
                        id='jadwal-durasi-menit' 
                        value='50' 
                        min='0' 
                        max='59'
                        style='width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; box-sizing:border-box; background:#f8fbff; outline:none;'
                    />
                </div>
            </div>
            <small style='color:#64748b; display:block; margin-top:4px; font-size:11px;'>Default: 1 jam 50 menit = 110 menit.</small>
        </div>

        <div style='background:#eff6ff; border:1px dashed #93c5fd; padding:10px 12px; border-radius:10px; margin-top:10px;'>
            <p style='margin:0; font-size:12px; color:#1e3a8a; line-height:1.4;'>
                <strong>Catatan:</strong> Pastikan tanggal dan ruangan sudah benar.
            </p>
        </div>

        <div id='jadwal-form-actions' style='margin-top:12px; display:flex; flex-direction:column; gap:8px;'></div>
    </div>
</div>
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
        dosen_context: List[Dict],
        persist: bool = False,
        shuffle_groups: bool = False,
        kelompok_order: Optional[List[int]] = None
    ) -> Dict:
        """
        Generate jadwal seminar untuk multiple ruangan dengan urutan serial

        Distribusi:
        - Setiap slot waktu hanya boleh diisi 1 kelompok total
        - Ruangan untuk setiap kelompok bisa berbeda
        - Jika slot pada hari itu habis, lanjut ke hari berikutnya
        
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

            # Normalize ruangan list: fetch ruangan names and preserve requested order
            ruangan_objs = session.query(Ruangan).filter(Ruangan.id.in_(ruangan_list)).all()
            ruangan_map = {r.id: r.ruangan for r in ruangan_objs}
            ordered_ruangan_ids = [rid for rid in ruangan_list if rid in ruangan_map]

            if not ordered_ruangan_ids:
                return {"success": False, "message": "Ruangan yang dipilih tidak ditemukan"}

            R = len(ordered_ruangan_ids)
            S = len(JadwalSeminarTools.TIME_SLOTS)
            capacity_per_day = S

            # Reorder kelompok if an explicit order is provided or shuffle is requested
            kelompok_map = {kelompok.id: kelompok for kelompok in kelompok_list}
            if kelompok_order:
                ordered_kelompok_list = [kelompok_map[k_id] for k_id in kelompok_order if k_id in kelompok_map]
                remaining_kelompok = [kelompok for kelompok in kelompok_list if kelompok.id not in set(kelompok_order)]
                ordered_kelompok_list.extend(remaining_kelompok)
            else:
                ordered_kelompok_list = list(kelompok_list)

            if shuffle_groups:
                from random import shuffle as _shuffle
                _shuffle(ordered_kelompok_list)

            # Assign each kelompok exactly once, with one group per time slot total.
            for idx, kelompok in enumerate(ordered_kelompok_list):
                day = idx // capacity_per_day
                within_day = idx % capacity_per_day
                slot_index = within_day
                room_index = idx % R

                ruangan_id = ordered_ruangan_ids[room_index]
                time_slot = JadwalSeminarTools.TIME_SLOTS[slot_index]

                current_date = tanggal_mulai + timedelta(days=day)

                # Parse waktu mulai dan selesai
                time_parts = time_slot[0].split(':')
                waktu_mulai = current_date.replace(hour=int(time_parts[0]), minute=int(time_parts[1]), second=0)

                time_parts_end = time_slot[1].split(':')
                waktu_selesai = current_date.replace(hour=int(time_parts_end[0]), minute=int(time_parts_end[1]), second=0)

                # Create jadwal entry (one entry per kelompok)
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

                # Only persist to DB if requested (persist=True). Otherwise keep entries in memory for preview.
                if persist:
                    session.add(jadwal)
                jadwal_entries.append({
                    "kelompok_id": kelompok.id,
                    "kelompok_nomor": getattr(kelompok, "nomor_kelompok", str(kelompok.id)),
                    "tanggal": waktu_mulai.strftime("%d %b %Y"),
                    "waktu": f"{time_slot[0]} - {time_slot[1]}",
                    "ruangan_id": ruangan_id,
                    "ruangan_name": ruangan_map.get(ruangan_id, str(ruangan_id))
                })
            
            # Commit to database if persisting
            if persist:
                session.commit()
                logger.info(f"✓ Persisted {len(jadwal_entries)} jadwal entries untuk {len(ruangan_list)} ruangan")
            else:
                logger.info(f"✓ Generated {len(jadwal_entries)} jadwal entries (preview, not persisted)")

            # Generate HTML response with appropriate messaging
            if persist:
                html = f"<p style='color:#059669; font-weight:bold;'>✅ Jadwal Seminar Berhasil Disimpan untuk {len(ruangan_list)} Ruangan!</p>"
            else:
                html = f"<p style='color:#0c4a6e; font-weight:bold;'>ℹ️ Preview Jadwal Seminar untuk {len(ruangan_list)} Ruangan (belum disimpan)</p>"
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
                html += f"<td style='padding:8px; border:1px solid #ddd;'>Kelompok {entry.get('kelompok_nomor', entry.get('kelompok_id'))}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry.get('ruangan_name', 'Ruangan ' + str(entry.get('ruangan_id')))}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry['tanggal']}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry['waktu']}</td>"
                html += f"</tr>"

            html += "</table>"
            html += f"<p style='margin-top:12px; color:#666;'><small>Total: {len(jadwal_entries)} jadwal dibuat ({len(kelompok_list)} kelompok dijadwalkan, {len(ordered_ruangan_ids)} ruangan)</small></p>"

            session.close()
            return {
                "success": True,
                "message": html,
                "total": len(jadwal_entries),
                "jadwal_entries": jadwal_entries,
                "persisted": bool(persist),
                "meta": {
                    "tanggal": tanggal_mulai.strftime("%d %b %Y"),
                    "ruangan_list": ordered_ruangan_ids,
                    "durasi_menit": durasi_menit,
                    "kelompok_order": [entry["kelompok_id"] for entry in jadwal_entries]
                }
            }
        except Exception as e:
            session.rollback()
            logger.error(f"❌ Error generating jadwal: {e}")
            session.close()
            return {
                "success": False,
                "message": f"❌ Error: {str(e)}"
            }

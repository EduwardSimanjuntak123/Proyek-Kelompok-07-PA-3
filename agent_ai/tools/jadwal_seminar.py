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
                <span>Tanggal Mulai Seminar <span style='color:#ef4444;'>*</span>x</span>
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
            
            # Get prodi, KPA, dan TM names
            from models.kategori_pa import KategoriPA
            from models.prodi import Prodi
            from models.tahun_masuk import TahunMasuk
            
            prodi_obj = session.query(Prodi).filter(Prodi.id == prodi_id).first()
            kpa_obj = session.query(KategoriPA).filter(KategoriPA.id == kpa_id).first()
            tm_obj = session.query(TahunMasuk).filter(TahunMasuk.id == tm_id).first()
            
            prodi_name = prodi_obj.nama_prodi if prodi_obj else f"Prodi {prodi_id}"
            kpa_name = kpa_obj.kategori_pa if kpa_obj else f"PA-{kpa_id}"
            tm_name = tm_obj.Tahun_Masuk if tm_obj else tm_id
            
            # Format: "PA-3 Prodi TRPL 2023"
            prodi_kpa_label = f"{kpa_name} {prodi_name} {tm_name}"
            
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
                    "ruangan_name": ruangan_map.get(ruangan_id, str(ruangan_id)),
                    "prodi_kpa": prodi_kpa_label
                })
            
            # Commit to database if persisting
            if persist:
                session.commit()
                logger.info(f"✓ Persisted {len(jadwal_entries)} jadwal entries untuk {len(ruangan_list)} ruangan")
            else:
                logger.info(f"✓ Generated {len(jadwal_entries)} jadwal entries (preview, not persisted)")

            # Generate HTML response with appropriate messaging
            if persist:
                html = f"<p style='color:#059669; font-weight:bold;'>Jadwal Seminar Berhasil Disimpan untuk {len(ruangan_list)} Ruangan!</p>"
            else:
                html = f"<p style='color:#0c4a6e; font-weight:bold;'>Preview Jadwal Seminar untuk {len(ruangan_list)} Ruangan (belum disimpan)</p>"
            html += "<table style='width:100%; border-collapse:collapse; margin-top:12px; font-size:13px;'>"
            html += "<tr style='background:#f0f0f0; border:1px solid #ddd;'>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>No</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Kelompok</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Prodi & Kategori PA</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Ruangan</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Tanggal</th>"
            html += "<th style='padding:8px; text-align:left; border:1px solid #ddd;'>Waktu</th>"
            html += "</tr>"
            
            for idx, entry in enumerate(jadwal_entries, 1):
                html += f"<tr style='border:1px solid #ddd;'>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{idx}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>Kelompok {entry.get('kelompok_nomor', entry.get('kelompok_id'))}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry.get('prodi_kpa', '')}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry.get('ruangan_name', 'Ruangan ' + str(entry.get('ruangan_id')))}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry['tanggal']}</td>"
                html += f"<td style='padding:8px; border:1px solid #ddd;'>{entry['waktu']}</td>"
                html += f"</tr>"

            html += "</table>"
            html += f"<p style='margin-top:12px; color:#666;'><small>Total: {len(jadwal_entries)} jadwal dibuat ({len(kelompok_list)} kelompok dijadwalkan, {len(ordered_ruangan_ids)} ruangan)</small></p>"
            
            # Add grouped by date section
            grouped_result = JadwalSeminarTools.format_jadwal_by_date(jadwal_entries)
            grouped_html = grouped_result.get("html", "")
            html += "<hr style='margin:16px 0; border:none; border-top:2px solid #ddd;'>"
            html += grouped_html
            
            # Add action buttons if not persisted
            if not persist:
                html += """
<div style='margin-top:16px; border:1px solid #fbbf24; background:#fef3c7; border-radius:10px; padding:12px;'>
    <h5 style='margin:0 0 8px 0; color:#92400e; font-size:14px; font-weight:600;'>Aksi Preview Jadwal</h5>
    <div style='display:flex; gap:8px; flex-wrap:wrap;'>
        <button type='button' class='btn btn-sm btn-success save-jadwal-btn' style='cursor:pointer; pointer-events:auto; position:relative; z-index:2;' onclick='if(window.__saveJadwalDb){window.__saveJadwalDb(event);} return false;'>
            Simpan ke Database
        </button>
        <button type='button' class='btn btn-sm btn-warning reshuffle-jadwal-btn' style='cursor:pointer; pointer-events:auto; position:relative; z-index:2;' onclick='if(window.__reshuffleJadwal){window.__reshuffleJadwal(event);} return false;'>
            Acak Ulang
        </button>
    </div>
</div>
"""

            session.close()
            return {
                "success": True,
                "message": html,
                "total": len(jadwal_entries),
                "jadwal_entries": jadwal_entries,
                "grouped": grouped_result.get("grouped", {}),
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

    @staticmethod
    def format_jadwal_by_date(jadwal_entries: List[Dict]) -> Dict:
        """
        Format jadwal entries dikelompokkan berdasarkan tanggal/hari
        
        Args:
            jadwal_entries: List jadwal entries dari generate_jadwal_seminar
            
        Returns:
            Dict with grouped jadwal, HTML preview, and metadata
        """
        from datetime import datetime
        from collections import defaultdict
        
        # Group by tanggal
        grouped = defaultdict(list)
        for entry in jadwal_entries:
            tanggal = entry['tanggal']
            grouped[tanggal].append(entry)
        
        # Sort by tanggal
        sorted_dates = sorted(grouped.keys(), key=lambda x: datetime.strptime(x, "%d %b %Y"))
        
        # Generate HTML dengan grouping per hari
        html = '<div style="background:#f8fafc; border-radius:12px; padding:16px; border:1px solid #e2e8f0;">'
        html += '<h3 style="margin:0 0 16px 0; color:#1e293b; font-size:16px; font-weight:700;">📅 Preview Jadwal Seminar Terstruktur</h3>'
        
        for tanggal in sorted_dates:
            # Parse date to get day name
            try:
                date_obj = datetime.strptime(tanggal, "%d %b %Y")
                hari_map = {0: "Senin", 1: "Selasa", 2: "Rabu", 3: "Kamis", 4: "Jumat", 5: "Sabtu", 6: "Minggu"}
                hari_nama = hari_map.get(date_obj.weekday(), "Hari")
            except:
                hari_nama = "Hari"
            
            entries = grouped[tanggal]
            html += f'<div style="margin-bottom:16px; border-left:4px solid #3b82f6; padding-left:12px;">'
            html += f'<h4 style="margin:0 0 10px 0; color:#1e40af; font-size:14px; font-weight:700;">🗓️ {hari_nama}, {tanggal}</h4>'
            html += '<table style="width:100%; border-collapse:collapse; font-size:12px;">'
            
            for idx, entry in enumerate(entries, 1):
                bg_color = '#f0fdf4' if idx % 2 == 0 else '#ffffff'
                html += f'<tr style="background:{bg_color}; border-bottom:1px solid #e5e7eb;">'
                html += f'<td style="padding:8px; width:5%;">📍</td>'
                html += f'<td style="padding:8px; width:25%; font-weight:600;">Kelompok {entry.get("kelompok_nomor", entry.get("kelompok_id"))}</td>'
                html += f'<td style="padding:8px; width:25%; color:#666;">⏰ {entry["waktu"]}</td>'
                html += f'<td style="padding:8px; width:45%; color:#059669; font-weight:600;">🏢 {entry.get("ruangan_name", "Ruangan " + str(entry.get("ruangan_id")))}</td>'
                html += f'</tr>'
            
            html += '</table>'
            html += '</div>'
        
        html += f'<div style="margin-top:12px; padding-top:12px; border-top:1px solid #e2e8f0;">'
        html += f'<p style="margin:0; color:#64748b; font-size:12px;">📊 Total: <strong>{len(jadwal_entries)}</strong> jadwal | <strong>{len(sorted_dates)}</strong> hari | <strong>{len(set(entry.get("ruangan_id") for entry in jadwal_entries))}</strong> ruangan</p>'
        html += '</div>'
        html += '</div>'
        
        return {
            "grouped": {tanggal: grouped[tanggal] for tanggal in sorted_dates},
            "html": html,
            "sorted_dates": sorted_dates,
            "total": len(jadwal_entries),
            "total_days": len(sorted_dates)
        }

    @staticmethod
    def get_jadwal_kelompok_detail(kelompok_nomor: int, dosen_context: List[Dict]) -> Dict:
        """
        Get jadwal lengkap untuk kelompok tertentu dengan anggota, pembimbing, penguji, dan ruangan
        
        Args:
            kelompok_nomor: Nomor kelompok (1, 2, 3, dst)
            dosen_context: Dosen context untuk filter
            
        Returns:
            Dict dengan detail jadwal kelompok
        """
        from models.kelompok import Kelompok
        from models.kelompokMahasiswa import KelompokMahasiswa
        from models.mahasiswa import Mahasiswa
        from models.pembimbing import Pembimbing
        from models.penguji import Penguji
        from models.ruangan import Ruangan
        
        session = SessionLocal()
        try:
            # Extract konteks
            prodi_id = dosen_context[0].get("prodi_id")
            tm_id = dosen_context[0].get("tahun_masuk_id")
            kpa_id = dosen_context[0].get("kategori_pa_id")
            
            # Find kelompok
            kelompok = session.query(Kelompok).filter(
                Kelompok.prodi_id == prodi_id,
                Kelompok.TM_id == tm_id,
                Kelompok.KPA_id == kpa_id,
                Kelompok.nomor_kelompok == kelompok_nomor
            ).first()
            
            if not kelompok:
                return {
                    "status": "error",
                    "message": f"❌ Kelompok {kelompok_nomor} tidak ditemukan"
                }
            
            # Get jadwal for this kelompok
            from models.jadwal import Jadwal
            jadwal = session.query(Jadwal).filter(
                Jadwal.kelompok_id == kelompok.id
            ).first()
            
            # Get anggota kelompok
            anggota = session.query(KelompokMahasiswa, Mahasiswa).join(
                Mahasiswa, KelompokMahasiswa.mahasiswa_id == Mahasiswa.id
            ).filter(KelompokMahasiswa.kelompok_id == kelompok.id).all()
            
            anggota_list = []
            for km, mhs in anggota:
                anggota_list.append({
                    "nim": mhs.nim,
                    "nama": mhs.nama
                })
            
            # Get pembimbing
            pembimbing_list = session.query(Pembimbing).filter(
                Pembimbing.kelompok_id == kelompok.id
            ).all()
            
            pembimbing_names = [pb.dosen.nama if pb.dosen else "Belum ditentukan" for pb in pembimbing_list]
            
            # Get penguji
            penguji_list = session.query(Penguji).filter(
                Penguji.kelompok_id == kelompok.id
            ).all()
            
            penguji_names = [pj.dosen.nama if pj.dosen else "Belum ditentukan" for pj in penguji_list]
            
            # Get ruangan
            ruangan_name = "Belum dijadwalkan"
            tanggal_jadwal = "Belum dijadwalkan"
            waktu_jadwal = "Belum dijadwalkan"
            
            if jadwal:
                ruangan = session.query(Ruangan).filter(Ruangan.id == jadwal.ruangan_id).first()
                ruangan_name = ruangan.ruangan if ruangan else f"Ruangan {jadwal.ruangan_id}"
                tanggal_jadwal = jadwal.waktu_mulai.strftime("%d %B %Y")
                waktu_jadwal = f"{jadwal.waktu_mulai.strftime('%H:%M')} - {jadwal.waktu_selesai.strftime('%H:%M')}"
            
            # Generate HTML response
            html = '<div style="background:#f0fdf4; border-radius:12px; padding:16px; border:2px solid #10b981;">'
            html += f'<h3 style="margin:0 0 12px 0; color:#065f46; font-size:16px; font-weight:700;">🎓 Detail Jadwal Seminar Kelompok {kelompok_nomor}</h3>'
            
            # Jadwal info
            html += '<div style="background:#dcfce7; border-left:4px solid #10b981; padding:12px; margin-bottom:12px; border-radius:6px;">'
            html += f'<p style="margin:0 0 4px 0; color:#065f46; font-weight:600;">📅 Tanggal: {tanggal_jadwal}</p>'
            html += f'<p style="margin:0 0 4px 0; color:#065f46; font-weight:600;">⏰ Waktu: {waktu_jadwal}</p>'
            html += f'<p style="margin:0; color:#065f46; font-weight:600;">🏢 Ruangan: {ruangan_name}</p>'
            html += '</div>'
            
            # Anggota kelompok
            html += '<div style="margin-bottom:12px;">'
            html += '<h4 style="margin:0 0 8px 0; color:#1e40af; font-size:13px; font-weight:700;">👥 Anggota Kelompok:</h4>'
            html += '<ul style="margin:0; padding-left:20px;">'
            for anggota in anggota_list:
                html += f'<li style="color:#374151; margin-bottom:4px;">{anggota["nama"]} ({anggota["nim"]})</li>'
            html += '</ul>'
            html += '</div>'
            
            # Pembimbing
            html += '<div style="margin-bottom:12px;">'
            html += '<h4 style="margin:0 0 8px 0; color:#7c3aed; font-size:13px; font-weight:700;">👨‍🏫 Dosen Pembimbing:</h4>'
            html += '<ul style="margin:0; padding-left:20px;">'
            for pb_name in pembimbing_names:
                html += f'<li style="color:#374151; margin-bottom:4px;">{pb_name}</li>'
            html += '</ul>'
            html += '</div>'
            
            # Penguji
            html += '<div style="margin-bottom:12px;">'
            html += '<h4 style="margin:0 0 8px 0; color:#dc2626; font-size:13px; font-weight:700;">✅ Dosen Penguji:</h4>'
            html += '<ul style="margin:0; padding-left:20px;">'
            for pj_name in penguji_names:
                html += f'<li style="color:#374151; margin-bottom:4px;">{pj_name}</li>'
            html += '</ul>'
            html += '</div>'
            
            html += '</div>'
            
            session.close()
            return {
                "status": "success",
                "message": html,
                "kelompok_nomor": kelompok_nomor,
                "tanggal": tanggal_jadwal,
                "waktu": waktu_jadwal,
                "ruangan": ruangan_name,
                "anggota": anggota_list,
                "pembimbing": pembimbing_names,
                "penguji": penguji_names
            }
        
        except Exception as e:
            logger.error(f"❌ Error getting jadwal detail: {e}")
            session.close()
            return {
                "status": "error",
                "message": f"❌ Error: {str(e)}"
            }

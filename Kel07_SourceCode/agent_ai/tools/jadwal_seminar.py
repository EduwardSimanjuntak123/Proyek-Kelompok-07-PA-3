"""
Tools untuk manage jadwal seminar feature

PERBAIKAN (BUG FIX):
- BUG 1: room_index dihitung dari idx global (idx % R), bukan dari slot ruangan per hari.
  Akibatnya, saat ada 2 ruangan, kelompok ke-5 (hari baru) mendapat ruangan[1] bukan ruangan[0].
  FIX: room_index = within_day % R  ← gunakan posisi dalam hari, bukan index global.

- BUG 2: Saat persist=True, generate_jadwal_seminar tidak mengembalikan grouped/entries
  yang bisa dipakai untuk verifikasi. Sudah ada fallback, tapi grouped_result kosong.
  FIX: Tetap jalankan format_jadwal_by_date meskipun persist=True agar log dan return data lengkap.

- BUG 3 (executor_node.py): Saat action == "save_jadwal", kelompok_order diambil dari
  state["jadwal_entries"], tapi SETELAH itu generate_jadwal_seminar dipanggil ulang dengan
  shuffle_groups=False. Namun jika jadwal_entries kosong di state, order kembali ke default
  (urutan DB) yang berbeda dari preview. Solusi ada di executor (lihat executor_node_FIXED.py).
"""
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Tuple
import re
import random
import calendar 
from sqlalchemy import func
from core.database import SessionLocal
from models.jadwal import Jadwal
from models.kelompok import Kelompok
from models.ruangan import Ruangan
from models.pembimbing import Pembimbing
from models.penguji import Penguji
from models.dosen import Dosen
from models.dosen_role import DosenRole
from models.role import Role
import logging

logger = logging.getLogger(__name__)


class JadwalSeminarTools:
    """Tools untuk manage jadwal seminar"""
    
    # Durasi standard: 1 jam 50 menit (110 menit)
    DURATION_MINUTES = 110
    
    # Time slots untuk seminar (4 slots per hari)
    TIME_SLOTS = [
        ("08:00", "09:50"),
        ("10:00", "11:50"),
        ("13:00", "14:50"),
        ("15:00", "16:50"),
    ]

    @staticmethod
    def get_form_jadwal(ruangan_list: Optional[List] = None) -> Dict:
        """Generate form HTML untuk input jadwal seminar"""
        session = SessionLocal()

        try:

            # Filter ruangan yang diperbolehkan
            ALLOWED_ROOMS = [
                "Common Room",
                "Ruang Meeting Gedung Rektorat Lantai 2",
                "Ruang Meeting Kecil",
                "Ruang Rapat Vokasi Lt-1",
                "Ruang Rapat Vokasi Lt-2"
            ]

            if ruangan_list is None:
                ruangan_list = (
                    session.query(Ruangan)
                    .filter(Ruangan.ruangan.in_(ALLOWED_ROOMS))
                    .order_by(Ruangan.ruangan.asc())
                    .all()
                )

            ruangan_options = '<option value="">-- Pilih Ruangan --</option>\n'

            for ruang in ruangan_list:
                ruangan_options += (
                    f"<option value='{ruang.id}'>{ruang.ruangan}</option>\n"
                )

            html = f"""
        <div style='margin:8px 0;padding:12px;border:1px solid #c7d2fe;
        border-radius:14px;background:linear-gradient(180deg,#eff6ff 0%,#ffffff 100%);
        box-shadow:0 12px 24px rgba(59,130,246,0.1);'>

            <div style='display:flex;gap:10px;align-items:center;margin-bottom:10px;'>
                <div style='width:36px;height:36px;border-radius:10px;
                background:linear-gradient(135deg,#3b82f6,#1d4ed8);
                display:flex;align-items:center;justify-content:center;
                color:white;font-size:18px;'>📅</div>

                <div>
                    <h3 style='margin:0;color:#1e3a8a;font-size:18px;font-weight:700;'>
                        Input Jadwal Seminar
                    </h3>

                    <p style='margin:2px 0;color:#4b5563;font-size:12px;'>
                        Hari weekend, hari libur, dan tanggal lewat tidak dapat dipilih
                    </p>
                </div>
            </div>

            <div style='background:rgba(255,255,255,.86);
            border:1px solid #dbeafe;border-radius:12px;padding:12px;'>

                <!-- TANGGAL -->
                <div style='margin-bottom:12px;'>

                    <label style='display:flex;align-items:center;gap:6px;
                    margin-bottom:4px;font-weight:700;color:#1f2937;'>

                        <span>📆</span>
                        <span>Tanggal Mulai Seminar *</span>

                    </label>

                    <div style='position:relative;'>

                        <input
                            type='text'
                            id='jadwal-tanggal'
                            readonly
                            placeholder='Pilih tanggal seminar'
                            style='width:100%;
                            padding:10px 42px 10px 12px;
                            border:1px solid #cbd5e1;
                            border-radius:10px;
                            background:#f8fbff;
                            cursor:pointer;'
                        />

                        <!-- icon kalender -->
                        <i
                            class="fas fa-calendar-alt"
                            style='position:absolute;
                            right:14px;
                            top:50%;
                            transform:translateY(-50%);
                            font-size:16px;
                            color:#3b82f6;
                            pointer-events:none;'
                        ></i>
                    </div>

                </div>  


                <!-- RUANGAN -->
                <div style='margin-bottom:12px;'>

                    <label style='display:flex;align-items:center;
                    gap:6px;margin-bottom:4px;font-weight:700;'>

                        🏫 Ruangan * (boleh lebih dari satu)

                    </label>

                    <div id='jadwal-ruangan-container'
                    style='border:1px solid #dbeafe;
                    padding:10px;
                    border-radius:10px;
                    max-height:180px;
                    overflow-y:auto;'>

                        <div class='ruangan-row'
                        style='display:flex;gap:6px;'>

                            <select
                                class='jadwal-ruangan-select'
                                style='flex:1;
                                padding:10px;
                                border:1px solid #cbd5e1;
                                border-radius:10px;'
                            >
                            {ruangan_options}
                            </select>

                            <button
                                type='button'
                                class='remove-ruangan-btn'
                                style='display:none;
                                padding:10px;
                                background:#ef4444;
                                color:white;
                                border:none;
                                border-radius:10px;'
                            >
                            ✕
                            </button>

                        </div>

                    </div>

                    <button
                        type='button'
                        id='add-ruangan-btn'
                        style='margin-top:8px;
                        width:100%;
                        padding:10px;
                        background:linear-gradient(135deg,#10b981,#059669);
                        color:white;
                        border:none;
                        border-radius:10px;'
                    >
                    + Tambah Ruangan
                    </button>

                </div>


                <!-- DURASI -->
                <div style='margin-bottom:10px;'>

                    <label style='display:flex;
                    align-items:center;
                    gap:6px;
                    font-weight:700;
                    color:#1f2937;
                    margin-bottom:6px;'>

                        <i class="fas fa-clock"
                        style='color:#3b82f6;'></i>

                        Durasi Seminar

                    </label>

                    <div style='padding:12px;
                    background:linear-gradient(180deg,#f8fbff,#ffffff);
                    border:1px solid #dbeafe;
                    border-radius:10px;
                    display:flex;
                    align-items:center;
                    justify-content:space-between;'>

                        <div>

                            <div style='font-size:15px;
                            font-weight:700;
                            color:#1e3a8a;'>

                                1 Jam 50 Menit

                            </div>

                            <small style='color:#64748b;'>

                                Durasi seminar telah ditetapkan sistem

                            </small>

                        </div>

                        <i class="fas fa-lock"
                        style='color:#94a3b8;
                        font-size:18px;'></i>

                    </div>

                    <!-- hidden input untuk tetap dikirim ke backend -->
                    <input
                        type="hidden"
                        id="jadwal-durasi"
                        value="110"
                    />

                </div>

                <div id='jadwal-form-actions'></div>

            </div>

        </div>

        """

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
                "message": f"Error: {str(e)}"
            }

    @staticmethod
    def parse_tanggal_input(tanggal_str: str, tahun: Optional[int] = None) -> Optional[datetime]:
        """Parse tanggal string ke datetime"""
        try:
            tanggal_lower = tanggal_str.lower().strip()
            
            if tahun is None:
                tahun = datetime.now().year
            
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
            
            pattern = r'(\d{1,2})\s+(\w+)(?:\s+(\d{4}))?'
            match = re.search(pattern, tanggal_lower)
            
            if match:
                hari = int(match.group(1))
                bulan_str = match.group(2)
                tahun_match = match.group(3)
                
                if tahun_match:
                    tahun = int(tahun_match)
                
                bulan = None
                for key, value in bulan_map.items():
                    if bulan_str.startswith(key[:3]):
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
        """Get kelompok yang sesuai dengan dosen context untuk dijadwalkan"""
        session = SessionLocal()
        try:
            if not dosen_context:
                return []
            
            prodi_ids = set()
            tm_ids = set()
            kpa_ids = set()
            
            for context in dosen_context:
                if context.get("prodi_id"):
                    prodi_ids.add(context["prodi_id"])
                if context.get("angkatan"):
                    tm_ids.add(context["angkatan"])
                if context.get("kategori_pa"):
                    kpa_ids.add(context["kategori_pa"])
            
            query = session.query(Kelompok)
            
            if prodi_ids:
                query = query.filter(Kelompok.prodi_id.in_(prodi_ids))
            if tm_ids:
                query = query.filter(Kelompok.TM_id.in_(tm_ids))
            if kpa_ids:
                query = query.filter(Kelompok.KPA_id.in_(kpa_ids))
            
            kelompok_list = query.all()
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
        Generate jadwal seminar.

        PERBAIKAN BUG UTAMA:
        - room_index sebelumnya: idx % R  → salah karena menggunakan index global
        - room_index sekarang:   within_day % R → benar karena menggunakan posisi dalam hari

        Contoh dengan 14 kelompok, 2 ruangan, 4 slot per hari:
        
        SEBELUM (salah):
          Kelompok 0 (idx=0):  slot 0, room[0%2=0]  → Ruangan A ✓
          Kelompok 1 (idx=1):  slot 1, room[1%2=1]  → Ruangan B  ← HARUSNYA A
          Kelompok 2 (idx=2):  slot 2, room[2%2=0]  → Ruangan A
          ...dst. bergantian terus, tidak sesuai preview

        SESUDAH (benar):
          Hari 1: Kelompok 0,1,2,3 dapat slot 0,1,2,3; room = within_day % R
                  → within_day 0: room[0%2=0]=A, 1: room[1%2=1]=B, dst.
          Hari 2: Kelompok 4,5,6,7 → within_day 0,1,2,3 → room A, B, A, B
          (sesuai dengan apa yang dikonfigurasi di preview)
        """
        session = SessionLocal()
        try:
            if not kelompok_list:
                return {"success": False, "message": "Tidak ada kelompok untuk dijadwalkan"}
            
            if not ruangan_list:
                return {"success": False, "message": "Minimal 1 ruangan harus dipilih"}
            
            sample_kelompok = kelompok_list[0]
            kpa_id = sample_kelompok.KPA_id
            prodi_id = sample_kelompok.prodi_id
            tm_id = sample_kelompok.TM_id

            # Validasi tanggal mulai: skip weekend
            if tanggal_mulai.weekday() in [5, 6]:
                # Jika tanggal mulai weekend, geser ke Senin berikutnya
                days_ahead = 7 - tanggal_mulai.weekday()  # 5→2, 6→1
                tanggal_mulai = tanggal_mulai + timedelta(days=days_ahead)
                logger.info(
                    f"[{user_id}] ⚠️ Tanggal mulai adalah weekend, "
                    f"digeser ke: {tanggal_mulai.strftime('%d %b %Y')}"
                )

            # Validasi tanggal mulai: skip hari libur nasional (ditentukan setelah definisi helper)
            # Sementara cek dulu hari libur tetap secara langsung
            LIBUR_CHECK_MM_DD = [
                "01-01", "05-01", "06-01", "08-17", "12-25", "12-26",
            ]
            while tanggal_mulai.strftime("%m-%d") in LIBUR_CHECK_MM_DD:
                logger.info(
                    f"[{user_id}] ⚠️ Tanggal mulai adalah hari libur nasional "
                    f"({tanggal_mulai.strftime('%d %b %Y')}), digeser +1 hari"
                )
                tanggal_mulai = tanggal_mulai + timedelta(days=1)
                # Setelah geser, pastikan tidak jatuh di weekend lagi
                while tanggal_mulai.weekday() in [5, 6]:
                    tanggal_mulai = tanggal_mulai + timedelta(days=1)

            if tanggal_mulai.date() < datetime.now().date():
                raise Exception(
                    "Tanggal seminar tidak boleh tanggal yang sudah lewat"
                )

            # Daftar hari libur nasional Indonesia (bulan-hari, berlaku setiap tahun)
            LIBUR_NASIONAL_MM_DD = [
                "01-01",  # Tahun Baru Masehi
                "05-01",  # Hari Buruh Internasional
                "06-01",  # Hari Lahir Pancasila
                "08-17",  # Hari Kemerdekaan RI
                "12-25",  # Hari Raya Natal
                "12-26",  # Cuti Bersama Natal
            ]

            def is_libur_nasional(dt: datetime) -> bool:
                """Cek apakah tanggal adalah hari libur nasional tetap."""
                mm_dd = dt.strftime("%m-%d")
                return mm_dd in LIBUR_NASIONAL_MM_DD

            def next_working_day(dt: datetime, offset_days: int) -> datetime:
                """
                Hitung hari kerja ke-N dari dt (skip Sabtu, Minggu & hari libur nasional).
                offset_days=0 → dt itu sendiri (jika hari kerja).
                """
                result = dt
                skipped = 0
                added   = 0
                while added < offset_days:
                    result  = result + timedelta(days=1)
                    added  += 1
                    # Jika mendarat di Sabtu (5), Minggu (6), atau hari libur nasional, lompati
                    while result.weekday() in [5, 6] or is_libur_nasional(result):
                        reason = "weekend" if result.weekday() in [5, 6] else f"libur nasional ({result.strftime('%m-%d')})"
                        logger.info(
                            f"[{user_id}] ↩️  Skip {reason}: {result.strftime('%d %b %Y (%A)')}"
                        )
                        result  = result + timedelta(days=1)
                        skipped += 1
                if skipped:
                    logger.info(
                        f"[{user_id}] ↩️  Total skip {skipped} hari (weekend/libur), "
                        f"hari ke-{offset_days} → {result.strftime('%d %b %Y (%A)')}"
                    )
                return result
            
            from models.kategori_pa import KategoriPA
            from models.prodi import Prodi
            from models.tahun_masuk import TahunMasuk
            
            prodi_obj = session.query(Prodi).filter(Prodi.id == prodi_id).first()
            kpa_obj = session.query(KategoriPA).filter(KategoriPA.id == kpa_id).first()
            tm_obj = session.query(TahunMasuk).filter(TahunMasuk.id == tm_id).first()
            
            prodi_name = prodi_obj.nama_prodi if prodi_obj else f"Prodi {prodi_id}"
            kpa_name = kpa_obj.kategori_pa if kpa_obj else f"PA-{kpa_id}"
            tm_name = tm_obj.Tahun_Masuk if tm_obj else tm_id
            prodi_kpa_label = f"{kpa_name} {prodi_name} {tm_name}"
            
            jadwal_entries = []

            ruangan_objs = session.query(Ruangan).filter(Ruangan.id.in_(ruangan_list)).all()
            ruangan_map = {r.id: r.ruangan for r in ruangan_objs}
            ordered_ruangan_ids = [rid for rid in ruangan_list if rid in ruangan_map]

            if not ordered_ruangan_ids:
                return {"success": False, "message": "Ruangan yang dipilih tidak ditemukan"}

            R = len(ordered_ruangan_ids)
            S = len(JadwalSeminarTools.TIME_SLOTS)

            # Kapasitas per hari hanya sebanyak slot
            capacity_per_day = S

            kelompok_map = {
                kelompok.id: kelompok
                for kelompok in kelompok_list
            }

            # Tentukan urutan kelompok
            # shuffle_groups SELALU menang atas kelompok_order agar acak ulang benar-benar random
            if shuffle_groups:
                ordered_kelompok_list = list(kelompok_list)
                import time as _time
                random.seed(int(_time.time() * 1000) % (2**32))
                random.shuffle(ordered_kelompok_list)
                # Pastikan urutan berbeda dari preview sebelumnya
                if kelompok_order and len(kelompok_order) == len(ordered_kelompok_list):
                    current_order = [k.id for k in ordered_kelompok_list]
                    attempts = 0
                    while current_order == kelompok_order and attempts < 10:
                        random.shuffle(ordered_kelompok_list)
                        current_order = [k.id for k in ordered_kelompok_list]
                        attempts += 1
                logger.info(f"[{user_id}] Shuffle result: {[k.id for k in ordered_kelompok_list]}")

            elif kelompok_order:
                ordered_kelompok_list = [
                    kelompok_map[kid]
                    for kid in kelompok_order
                    if kid in kelompok_map
                ]

            else:
                ordered_kelompok_list = list(kelompok_list)

            logger.info(
                f"[{user_id}] kelompok_order={kelompok_order}"
            )

            logger.info(
                f"[{user_id}] ordered_kelompok_ids="
                f"{[k.id for k in ordered_kelompok_list]}"
            )

            logger.info(
                f"[{user_id}] ordered_nomor_kelompok="
                f"{[k.nomor_kelompok for k in ordered_kelompok_list]}"
            )

            for idx, kelompok in enumerate(ordered_kelompok_list):

                day = idx // capacity_per_day
                within_day = idx % capacity_per_day

                # slot unik
                slot_index = within_day

                # ruangan bergantian
                room_index = within_day % R

                ruangan_id = ordered_ruangan_ids[room_index]

                time_slot = JadwalSeminarTools.TIME_SLOTS[slot_index]

                # Hitung hari kerja ke-N (skip Sabtu & Minggu otomatis)
                current_date = next_working_day(tanggal_mulai, day)

                time_parts = time_slot[0].split(':')
                waktu_mulai = current_date.replace(hour=int(time_parts[0]), minute=int(time_parts[1]), second=0)

                time_parts_end = time_slot[1].split(':')
                waktu_selesai = current_date.replace(hour=int(time_parts_end[0]), minute=int(time_parts_end[1]), second=0)

                pembimbing_list = session.query(Pembimbing).filter(
                    Pembimbing.kelompok_id == kelompok.id
                ).all()
                
                pembimbing_data = []
                for pb in pembimbing_list:
                    dosen = session.query(Dosen).filter(Dosen.user_id == pb.user_id).first()
                    nama = dosen.nama if dosen else "Belum ditentukan"
                    pembimbing_data.append({"nama": nama, "user_id": pb.user_id})
                
                penguji_list = session.query(Penguji).filter(
                    Penguji.kelompok_id == kelompok.id
                ).all()
                
                penguji_data = []
                for pj in penguji_list:
                    dosen = session.query(Dosen).filter(Dosen.user_id == pj.user_id).first()
                    nama = dosen.nama if dosen else "Belum ditentukan"
                    penguji_data.append({"nama": nama, "user_id": pj.user_id})
                
                pembimbing_1 = pembimbing_data[0]["nama"] if len(pembimbing_data) > 0 else "-"
                pembimbing_2 = pembimbing_data[1]["nama"] if len(pembimbing_data) > 1 else "-"
                penguji_1 = penguji_data[0]["nama"] if len(penguji_data) > 0 else "-"
                penguji_2 = penguji_data[1]["nama"] if len(penguji_data) > 1 else "-"

                jadwal_obj = Jadwal(
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

                if persist:
                    session.add(jadwal_obj)

                jadwal_entries.append({
                    "kelompok_id": kelompok.id,
                    "kelompok_nomor": getattr(kelompok, "nomor_kelompok", str(kelompok.id)),
                    "tanggal": waktu_mulai.strftime("%d %b %Y"),
                    "waktu": f"{time_slot[0]} - {time_slot[1]}",
                    "ruangan_id": ruangan_id,
                    "ruangan_name": ruangan_map.get(ruangan_id, str(ruangan_id)),
                    "prodi_kpa": prodi_kpa_label,
                    "pembimbing_1": pembimbing_1,
                    "pembimbing_2": pembimbing_2,
                    "penguji_1": penguji_1,
                    "penguji_2": penguji_2
                })
            
            if persist:
                session.commit()
                logger.info(f"[{user_id}] ✓ Persisted {len(jadwal_entries)} jadwal entries")
                html = f"Jadwal Seminar Berhasil Disimpan untuk {len(ruangan_list)} Ruangan"
                # ✅ FIX BUG 2: Tetap format entries agar return data lengkap
                grouped_result = JadwalSeminarTools.format_jadwal_by_date(jadwal_entries)
            else:
                logger.info(f"[{user_id}] ✓ Preview {len(jadwal_entries)} jadwal entries (not persisted)")
                grouped_result = JadwalSeminarTools.format_jadwal_by_date(jadwal_entries)
                grouped_html = grouped_result.get("html", "")
                html = grouped_html
                
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

            # Format tanggal dalam bahasa Indonesia agar konsisten saat dikirim balik ke parser
            BULAN_ID = {
                1: "januari", 2: "februari", 3: "maret", 4: "april",
                5: "mei", 6: "juni", 7: "juli", 8: "agustus",
                9: "september", 10: "oktober", 11: "november", 12: "desember"
            }
            tanggal_id = f"{tanggal_mulai.day} {BULAN_ID[tanggal_mulai.month]} {tanggal_mulai.year}"

            session.close()
            return {
                "success": True,
                "message": html,
                "total": len(jadwal_entries),
                "jadwal_entries": jadwal_entries,
                "grouped": grouped_result.get("grouped", {}),
                "persisted": bool(persist),
                "meta": {
                    "tanggal": tanggal_id,
                    "ruangan_list": ordered_ruangan_ids,
                    "durasi_menit": durasi_menit,
                    "kelompok_order": [entry["kelompok_id"] for entry in jadwal_entries]
                }
            }
        except Exception as e:
            session.rollback()
            logger.error(f"[{user_id}] ❌ Error generating jadwal: {e}")
            session.close()
            return {
                "success": False,
                "message": f"Error: {str(e)}"
            }

    @staticmethod
    def format_jadwal_by_date(jadwal_entries: List[Dict]) -> Dict:
        """Format jadwal entries dikelompokkan berdasarkan tanggal/hari"""
        from datetime import datetime
        from collections import defaultdict
        
        grouped = defaultdict(list)
        for entry in jadwal_entries:
            tanggal = entry['tanggal']
            grouped[tanggal].append(entry)
        
        sorted_dates = sorted(grouped.keys(), key=lambda x: datetime.strptime(x, "%d %b %Y"))
        
        html = '<div style="background:#f8fafc; border-radius:12px; padding:16px; border:1px solid #e2e8f0; overflow-x:auto;">'
        html += '<h3 style="margin:0 0 16px 0; color:#1e293b; font-size:16px; font-weight:700;">Preview Jadwal Seminar Terstruktur</h3>'
        
        for tanggal in sorted_dates:
            try:
                date_obj = datetime.strptime(tanggal, "%d %b %Y")
                hari_map = {0: "Senin", 1: "Selasa", 2: "Rabu", 3: "Kamis", 4: "Jumat", 5: "Sabtu", 6: "Minggu"}
                hari_nama = hari_map.get(date_obj.weekday(), "Hari")
            except:
                hari_nama = "Hari"
            
            entries = grouped[tanggal]
            html += f'<div style="margin-bottom:20px;">'
            html += f'<h4 style="margin:0 0 12px 0; color:#1e40af; font-size:14px; font-weight:700; padding-bottom:8px; border-bottom:2px solid #3b82f6;">{hari_nama}, {tanggal}</h4>'
            
            html += '<table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:16px;">'
            html += '<thead><tr style="background:#e0e7ff; border:1px solid #cbd5e1;">'
            for col in ['Kelompok', 'Waktu', 'Ruangan', 'Pembimbing 1', 'Pembimbing 2', 'Penguji 1', 'Penguji 2']:
                html += f'<th style="padding:10px; text-align:left; border:1px solid #cbd5e1; font-weight:700;">{col}</th>'
            html += '</tr></thead><tbody>'
            
            for idx, entry in enumerate(entries, 1):
                bg_color = '#f8fafc' if idx % 2 == 0 else '#ffffff'
                html += f'<tr style="background:{bg_color}; border:1px solid #cbd5e1;">'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1; font-weight:600;">Kelompok {entry.get("kelompok_nomor", entry.get("kelompok_id"))}</td>'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1;">{entry["waktu"]}</td>'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1;">{entry.get("ruangan_name", "Ruangan " + str(entry.get("ruangan_id")))}</td>'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1; color:#7c3aed;">{entry.get("pembimbing_1", "-")}</td>'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1; color:#7c3aed;">{entry.get("pembimbing_2", "-")}</td>'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1; color:#dc2626;">{entry.get("penguji_1", "-")}</td>'
                html += f'<td style="padding:10px; border:1px solid #cbd5e1; color:#dc2626;">{entry.get("penguji_2", "-")}</td>'
                html += '</tr>'
            
            html += '</tbody></table>'
            html += '</div>'
        
        html += f'<div style="margin-top:12px; padding-top:12px; border-top:1px solid #e2e8f0;">'
        html += f'<p style="margin:0; color:#64748b; font-size:12px;">Total: <strong>{len(jadwal_entries)}</strong> jadwal | <strong>{len(sorted_dates)}</strong> hari | <strong>{len(set(entry.get("ruangan_id") for entry in jadwal_entries))}</strong> ruangan</p>'
        html += '</div>'
        html += '</div>'
        
        return {
            "grouped": {tanggal: grouped[tanggal] for tanggal in sorted_dates},
            "html": html,
            "sorted_dates": sorted_dates,
            "total": len(jadwal_entries),
            "total_days": len(sorted_dates)
        }


def check_existing_jadwal_by_context(user_id: int) -> Dict:
    """
    Cek apakah jadwal seminar sudah ada untuk konteks user ini.
    Return dict: { exists: bool, total: int, info: str }
    """
    session = SessionLocal()
    try:
        total = session.query(func.count(Jadwal.id)).filter(
            Jadwal.user_id == user_id
        ).scalar() or 0

        if total > 0:
            # Ambil rentang tanggal jadwal yang sudah ada
            earliest = session.query(func.min(Jadwal.waktu_mulai)).filter(
                Jadwal.user_id == user_id
            ).scalar()
            latest = session.query(func.max(Jadwal.waktu_selesai)).filter(
                Jadwal.user_id == user_id
            ).scalar()

            tgl_mulai = earliest.strftime("%d %b %Y") if earliest else "-"
            tgl_selesai = latest.strftime("%d %b %Y") if latest else "-"

            return {
                "exists": True,
                "total": total,
                "tgl_mulai": tgl_mulai,
                "tgl_selesai": tgl_selesai,
                "info": f"{total} jadwal seminar (mulai {tgl_mulai} s/d {tgl_selesai})"
            }

        return {"exists": False, "total": 0, "info": ""}

    except Exception as e:
        logger.error(f"❌ Error check_existing_jadwal: {e}")
        return {"exists": False, "total": 0, "info": ""}

    finally:
        session.close()


def get_jadwal_by_dosen_context(dosen_id=None):
    """Get jadwal berdasarkan dosen context"""
    session = SessionLocal()
    try:
        query = session.query(Jadwal)
        if dosen_id is not None:
            query = query.filter(Jadwal.user_id == dosen_id)

        jadwal_list = query.all()
        results = []

        for jadwal in jadwal_list:
            kelompok = session.query(Kelompok).filter(Kelompok.id == jadwal.kelompok_id).first()
            ruangan = session.query(Ruangan).filter(Ruangan.id == jadwal.ruangan_id).first()

            results.append({
                "jadwal_id": jadwal.id,
                "kelompok_id": jadwal.kelompok_id,
                "kelompok_nomor": kelompok.nomor_kelompok if kelompok else None,
                "tanggal": jadwal.waktu_mulai.strftime("%d %b %Y"),
                "waktu_mulai": jadwal.waktu_mulai.strftime("%H:%M"),
                "waktu_selesai": jadwal.waktu_selesai.strftime("%H:%M"),
                "ruangan": ruangan.ruangan if ruangan else None,
                "user_id": jadwal.user_id
            })

        return results

    except Exception as e:
        logger.error(f"❌ Error get_jadwal_by_dosen_context: {e}")
        return []

    finally:
        session.close()
<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use App\Models\Jadwal;
use App\Models\Kelompok;
use App\Models\KelompokMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Bimbingan;
use App\Models\pembimbing;
use App\Models\Nilai_Mahasiswa;
use App\Models\Penguji;
use App\Models\Pengumuman;
use App\Models\Tugas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class dashboard_Controller extends Controller
{
    public function Koordinator()
    {
        $KPA_id = session('KPA_id');
        $prodi_id = session('prodi_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');


        $jumlah_mahasiswa = KelompokMahasiswa::with('kelompok')
            ->whereHas('kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
                $q->where('KPA_id', $KPA_id);
                $q->where('prodi_id', $prodi_id);
                $q->where('TM_id', $TM_id);
            })
            ->count();

        // dd($jumlah_mahasiswa);
        $jumlah_pengumuman = Pengumuman::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();
        $jumlah_kelompok = Kelompok::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();
        $daftar_kelompok = Kelompok::with(['jadwal'])
            ->withCount([
                'KelompokMahasiswa as jumlah_anggota',

                'bimbingan as jumlah_bimbingan_selesai' => function ($q) {
                    $q->where('status', 'selesai');
                },

                'pengumpulanTugas as jumlah_artefak_submit' => function ($q) {
                    $q->whereHas('tugas', function ($query) {
                        $query->where('kategori_tugas', 'Artefak');
                    })
                        ->whereIn('status', ['Submitted', 'Late']);
                }
            ])
            ->withExists([
                'jadwal as sudah_memiliki_jadwal'
            ])
            ->withAvg('nilaiMahasiswa as rata_nilai_akhir', 'nilai_akhir')
            ->where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->get();

        $bar_labels = $daftar_kelompok->map(function ($item) {
            return 'Kelompok ' . $item->nomor_kelompok;
        });

        $bar_data = $daftar_kelompok->map(function ($item) {
            return $item->jumlah_bimbingan_selesai;
        });
        $jumlah_dosen = DosenRole::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();

        $jumlah_tugas = Tugas::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();
        $jadwal = Jadwal::with('kelompok')
            ->where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->get();

        $events = $jadwal->map(function ($item) {
            return [
                'title' => 'Kelompok ' . $item->kelompok->nomor_kelompok . 'seminar  ',
                'start' => Carbon::parse($item->waktu_mulai)->toIso8601String(),
                'end' => Carbon::parse($item->waktu_selesai)->toIso8601String(),
            ];
        });

        // Check verification status
        $verification_status = $this->checkVerificationStatus($KPA_id, $prodi_id, $TM_id);

        // untuk barchart
        $nilai_mahasiswa = Nilai_Mahasiswa::whereHas('kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
            $q->where('KPA_id', $KPA_id)
                ->where('prodi_id', $prodi_id)
                ->where('TM_id', $TM_id);
        });
        // dd($nilai_mahasiswa->get());

        $jumlah_A = (clone $nilai_mahasiswa)
            ->where('nilai_akhir', '>=', 85)
            ->count();

        $jumlah_B = (clone $nilai_mahasiswa)
            ->whereBetween('nilai_akhir', [70, 84.99])
            ->count();

        $jumlah_C = (clone $nilai_mahasiswa)
            ->whereBetween('nilai_akhir', [55, 69.99])
            ->count();

        $jumlah_D = (clone $nilai_mahasiswa)
            ->where('nilai_akhir', '<', 55)
            ->count();

        $dist_nilai = [
            $jumlah_A,
            $jumlah_B,
            $jumlah_C,
            $jumlah_D
        ];
        //untuk nilai top 
        $top_kelompok = $daftar_kelompok
            ->sortByDesc('rata_nilai_akhir')
            ->take(5)
            ->map(function ($item) {
                return [
                    'nama' => 'Kelompok ' . $item->nomor_kelompok,
                    'nilai' => round($item->rata_nilai_akhir ?? 0, 1),
                ];
            })
            ->values();
        $stat_lengkap = 0;
        $stat_menunggu = 0;
        $stat_belum = 0;

        foreach ($daftar_kelompok as $kelompok) {

            $progressCount = 0;

            // 1. Bimbingan selesai >= 8
            if (($kelompok->jumlah_bimbingan_selesai ?? 0) >= 8) {
                $progressCount++;
            }

            // 2. Artefak submit
            if (($kelompok->jumlah_artefak_submit ?? 0) > 0) {
                $progressCount++;
            }

            // 3. Sudah ada jadwal seminar
            if ($kelompok->sudah_memiliki_jadwal) {
                $progressCount++;
            }

            // Klasifikasi status
            if ($progressCount >= 3) {

                $stat_lengkap++;

            } elseif ($progressCount > 0) {

                $stat_menunggu++;

            } else {

                $stat_belum++;
            }
        }

        return view('pages.Koordinator.dashboard', compact(
            'jumlah_mahasiswa',
            'daftar_kelompok',
            'jumlah_kelompok',
            'jumlah_pengumuman',
            'jumlah_dosen',
            'jumlah_tugas',
            'events',
            'verification_status',
            'bar_labels',
            'bar_data',
            'dist_nilai',
            'top_kelompok',
            'stat_lengkap',
            'stat_menunggu',
            'stat_belum'
        ));
    }

    public function detailAdministratif()
{
    $KPA_id = session('KPA_id');
    $prodi_id = session('prodi_id');
    $TM_id = session('TM_id');
    $user_id = session('user_id');

    // =========================
    // FILTER & SORTING
    // =========================

    $statusFilter = request('status');
    $sortFilter = request('sort');

    // =========================
    // QUERY DATA KELOMPOK
    // =========================

    $daftar_kelompok = Kelompok::with(['jadwal'])

        ->withCount([

            // Jumlah anggota kelompok
            'KelompokMahasiswa as jumlah_anggota',

            // Jumlah bimbingan selesai
            'bimbingan as jumlah_bimbingan_selesai' => function ($q) {
                $q->where('status', 'selesai');
            },

            // Jumlah artefak submit
            'pengumpulanTugas as jumlah_artefak_submit' => function ($q) {
                $q->whereHas('tugas', function ($query) {
                    $query->where('kategori_tugas', 'Artefak');
                })
                ->whereIn('status', ['Submitted', 'Late']);
            }

        ])

        // Cek apakah sudah punya jadwal seminar
        ->withExists([
            'jadwal as sudah_memiliki_jadwal'
        ])

        // Rata-rata nilai akhir
        ->withAvg('nilaiMahasiswa as rata_nilai_akhir', 'nilai_akhir')

        // Filter data
        ->where('KPA_id', $KPA_id)
        ->where('prodi_id', $prodi_id)
        ->where('TM_id', $TM_id)

        ->get();
        // Total asli semua kelompok
         $total_kelompok = $daftar_kelompok->count();

    // =========================
    // HITUNG STATUS MONITORING
    // =========================

    $daftar_kelompok = $daftar_kelompok->map(function ($kelompok) {

        $progressCount = 0;

        // 1. Bimbingan minimal 8x
        if (($kelompok->jumlah_bimbingan_selesai ?? 0) >= 8) {
            $progressCount++;
        }

        // 2. Artefak sudah submit
        if (($kelompok->jumlah_artefak_submit ?? 0) > 0) {
            $progressCount++;
        }

        // 3. Seminar sudah terjadwal
        if ($kelompok->sudah_memiliki_jadwal) {
            $progressCount++;
        }

        // Simpan progress
        $kelompok->progress_count = $progressCount;

        // Status monitoring
        if ($progressCount == 3) {

        $kelompok->status_monitoring = 'Selesai';

        } else {

        $kelompok->status_monitoring = 'Berlangsung';
        }

            return $kelompok;
        });

    // =========================
    // FILTER STATUS
    // =========================

    if ($statusFilter) {

    $daftar_kelompok = $daftar_kelompok->where(
        'status_monitoring',
        $statusFilter
    );
}

    // =========================
    // SORTING
    // =========================

    if ($sortFilter == 'tinggi') {

        $daftar_kelompok = $daftar_kelompok->sortByDesc('progress_count');

    } elseif ($sortFilter == 'rendah') {

        $daftar_kelompok = $daftar_kelompok->sortBy('progress_count');

    } elseif ($sortFilter == 'terbaru') {

        $daftar_kelompok = $daftar_kelompok->sortByDesc('created_at');
    }

    // Reset index collection
    $daftar_kelompok = $daftar_kelompok->values();

    // =========================
    // STATISTIK DASHBOARD
    // =========================

    $jumlah_kelompok = $total_kelompok;

    $selesai = $daftar_kelompok
        ->where('status_monitoring', 'Selesai')
        ->count();

    $berlangsung = $daftar_kelompok
        ->where('status_monitoring', 'Berlangsung')
        ->count();

    // =========================
    // PAGINATION MANUAL
    // =========================

    $perPage = 10;
    $currentPage = request()->get('page', 1);

    $pagedData = $daftar_kelompok->slice(
        ($currentPage - 1) * $perPage,
        $perPage
    );

    $daftar_kelompok = new \Illuminate\Pagination\LengthAwarePaginator(
        $pagedData,
        $daftar_kelompok->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );

    // =========================
    // RETURN VIEW
    // =========================

    return view(
        'pages.Koordinator.Detail.detail-administratif',
        compact(
            'daftar_kelompok',
            'jumlah_kelompok',
            'selesai',
            'berlangsung',
            'KPA_id',
            'prodi_id',
            'TM_id',
            'user_id'
        )
    );
}



public function pembimbing()
{
    $KPA_id = session('KPA_id');
    $prodi_id = session('prodi_id');
    $TM_id = session('TM_id');
    $user_id = session('user_id');
    $token = session('token');

    /*
    |--------------------------------------------------------------------------
    | Kelompok IDs
    |--------------------------------------------------------------------------
    */
    $kelompokIds = pembimbing::where('user_id', $user_id)
        ->whereHas('kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
            $q->where([
                'KPA_id' => $KPA_id,
                'prodi_id' => $prodi_id,
                'TM_id' => $TM_id
            ]);
        })
        ->pluck('kelompok_id')
        ->unique()
        ->values();

    $jumlah_kelompok = $kelompokIds->count();

    /*
    |--------------------------------------------------------------------------
    | Base Collections
    |--------------------------------------------------------------------------
    */
    $kelompokMap = Kelompok::whereIn('id', $kelompokIds)
        ->get(['id', 'nomor_kelompok', 'status'])
        ->keyBy('id');

    $anggotaRaw = KelompokMahasiswa::whereIn('kelompok_id', $kelompokIds)
        ->get(['kelompok_id', 'user_id']);

    $anggotaPerKelompok = $anggotaRaw->groupBy('kelompok_id');

    $mahasiswaMap = Mahasiswa::whereIn(
        'user_id',
        $anggotaRaw->pluck('user_id')->unique()
    )
        ->get(['user_id', 'nama', 'nim'])
        ->keyBy('user_id');

    /*
    |--------------------------------------------------------------------------
    | Statistik Bimbingan
    |--------------------------------------------------------------------------
    */
    $bimbinganStats = DB::table('request_bimbingan')
        ->select(
            'kelompok_id',
            DB::raw('COUNT(*) as total_sesi'),
            DB::raw('MAX(rencana_selesai) as terakhir_bimbingan')
        )
        ->whereIn('kelompok_id', $kelompokIds)
        ->where('status', 'selesai')
        ->groupBy('kelompok_id')
        ->get()
        ->keyBy('kelompok_id');

    /*
    |--------------------------------------------------------------------------
    | Pembimbing Map
    |--------------------------------------------------------------------------
    */
    $pembimbingMap = pembimbing::whereIn('kelompok_id', $kelompokIds)
        ->get()
        ->groupBy('kelompok_id');

    /*
    |--------------------------------------------------------------------------
    | Kelompok List
    |--------------------------------------------------------------------------
    */
    $kelompokList = $kelompokIds->map(function ($kelompokId) use (
        $kelompokMap,
        $anggotaPerKelompok,
        $mahasiswaMap,
        $bimbinganStats,
        $pembimbingMap,
        $user_id
    ) {

        $kelompok = $kelompokMap[$kelompokId] ?? null;

        $anggota = collect($anggotaPerKelompok[$kelompokId] ?? [])
            ->map(function ($member) use ($mahasiswaMap) {

                $mahasiswa = $mahasiswaMap[$member->user_id] ?? null;

                return [
                    'user_id' => $member->user_id,
                    'nama' => $mahasiswa->nama ?? 'Tidak ditemukan',
                    'nim' => $mahasiswa->nim ?? '-',
                ];
            })
            ->values();

        $stat = $bimbinganStats[$kelompokId] ?? null;

        $totalSesi = (int) ($stat->total_sesi ?? 0);

        $pembimbingList = collect($pembimbingMap[$kelompokId] ?? []);

        $posisiPembimbing = $pembimbingList
            ->search(fn($item) => $item->user_id == $user_id);

        return [
            'kelompok_id' => $kelompokId,
            'nomor_kelompok' => $kelompok->nomor_kelompok ?? '-',
            'status_kelompok' => $kelompok->status ?? '-',

            'jumlah_anggota' => $anggota->count(),
            'anggota' => $anggota,

            'total_sesi_bimbingan' => $totalSesi,

            'terakhir_bimbingan' => $stat->terakhir_bimbingan ?? null,

            'status_bimbingan' => $totalSesi > 0
                ? 'Sudah Ada Catatan'
                : 'Belum Ada Catatan',

            'posisi_pembimbing' => $posisiPembimbing !== false
                ? $posisiPembimbing + 1
                : null,

            'jumlah_pembimbing' => $pembimbingList->count(),

            // dummy nilai sementara
            'nilai_rata' => rand(70, 95),
        ];
    })
        ->sortBy(fn($item) => (int) preg_replace('/\D/', '', $item['nomor_kelompok']))
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Statistik Dashboard
    |--------------------------------------------------------------------------
    */
    $jumlah_pengumuman = Pengumuman::where([
        'KPA_id' => $KPA_id,
        'prodi_id' => $prodi_id,
        'TM_id' => $TM_id
    ])->count();

    $jumlah_tugas = Tugas::where([
        'KPA_id' => $KPA_id,
        'prodi_id' => $prodi_id,
        'TM_id' => $TM_id
    ])->count();

    /*
    |--------------------------------------------------------------------------
    | Jadwal Seminar
    |--------------------------------------------------------------------------
    */
    $events = Jadwal::with('kelompok')
        ->where([
            'KPA_id' => $KPA_id,
            'prodi_id' => $prodi_id,
            'TM_id' => $TM_id
        ])
        ->get()
        ->map(function ($item) {
            return [
                'title' => 'Kelompok ' . $item->kelompok->nomor_kelompok,
                'start' => Carbon::parse($item->waktu_mulai)->toIso8601String(),
                'end' => Carbon::parse($item->waktu_selesai)->toIso8601String(),
            ];
        });

    /*
    |--------------------------------------------------------------------------
    | Role Dosen
    |--------------------------------------------------------------------------
    */
    $roles = DosenRole::where('user_id', $user_id)
        ->where('status', 'Aktif')
        ->whereIn('role_id', [3, 5])
        ->get();

    $prodi_ids = $roles->pluck('prodi_id')->unique();
    $TM_ids = $roles->pluck('TM_id')->unique();
    $KPA_ids = $roles->pluck('KPA_id')->unique();

    /*
    |--------------------------------------------------------------------------
    | Pengumuman
    |--------------------------------------------------------------------------
    */
    $pengumuman = Pengumuman::with(['prodi', 'kategoriPA'])
        ->whereIn('prodi_id', $prodi_ids)
        ->whereIn('TM_id', $TM_ids)
        ->whereIn('KPA_id', $KPA_ids)
        ->where('status', 'aktif')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | API Dosen
    |--------------------------------------------------------------------------
    */
    $responseDosen = Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->get(env('API_URL') . "library-api/dosen");

    $dosen_map = collect(
        $responseDosen->successful()
            ? ($responseDosen->json()['data']['dosen'] ?? [])
            : []
    )->keyBy('user_id');

    $pengumuman->each(function ($item) use ($dosen_map) {
        $item->nama = $dosen_map[$item->user_id]['nama'] ?? 'N/A';
    });

    /*
    |--------------------------------------------------------------------------
    | Chart Data
    |--------------------------------------------------------------------------
    */
    $chart_kelompok_labels = $kelompokList->map(
        fn($item) => 'Kelompok ' . $item['nomor_kelompok']
    );

    $chart_bimbingan_data = $kelompokList->pluck('total_sesi_bimbingan');

    $chart_nilai_kelompok = $kelompokList->pluck('nilai_rata');

    $chart_kelompok_group = $kelompokList
        ->take(3)
        ->pluck('nomor_kelompok');

    $chart_tugas = collect([85, 90, 75]);
    $chart_presentasi = collect([80, 88, 70]);
    $chart_laporan = collect([78, 92, 74]);

    /*
    |--------------------------------------------------------------------------
    | Donut Statistik
    |--------------------------------------------------------------------------
    */
    $artefak_disetujui = 12;
    $artefak_revisi = 5;
    $artefak_belum = 3;
    $artefak_lainnya = 1;

    return view('pages.Pembimbing.dashboard', compact(
        'jumlah_kelompok',
        'jumlah_pengumuman',
        'jumlah_tugas',

        'events',
        'pengumuman',

        'kelompokList',

        'chart_kelompok_labels',
        'chart_bimbingan_data',
        'chart_nilai_kelompok',
        'chart_kelompok_group',

        'chart_tugas',
        'chart_presentasi',
        'chart_laporan',

        'artefak_disetujui',
        'artefak_revisi',
        'artefak_belum',
        'artefak_lainnya'
    ));
}


    public function penguji()
    {
        $KPA_id = session('KPA_id');
        $prodi_id = session('prodi_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');

        $kelompokIds = Penguji::where('user_id', $user_id)
            ->whereHas('Kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
                $q->where('KPA_id', $KPA_id)
                    ->where('prodi_id', $prodi_id)
                    ->where('TM_id', $TM_id);
            })
            ->pluck('kelompok_id')
            ->unique()
            ->values();

        $jumlah_kelompok = $kelompokIds->count();

        $kelompokList = collect();
        if ($kelompokIds->isNotEmpty()) {
            $kelompokMap = Kelompok::whereIn('id', $kelompokIds)
                ->get(['id', 'nomor_kelompok', 'status', 'TM_id', 'KPA_id', 'prodi_id'])
                ->keyBy('id');

            $anggotaRaw = KelompokMahasiswa::whereIn('kelompok_id', $kelompokIds)
                ->get(['kelompok_id', 'user_id']);

            $mahasiswaMap = Mahasiswa::whereIn('user_id', $anggotaRaw->pluck('user_id')->unique())
                ->get(['user_id', 'nama', 'nim', 'angkatan'])
                ->keyBy('user_id');

            $jadwalMap = Jadwal::whereIn('kelompok_id', $kelompokIds)
                ->get(['kelompok_id', 'waktu_mulai', 'waktu_selesai'])
                ->keyBy('kelompok_id');

            // Ambil data penguji dengan informasi posisi
            $pengujiMap = Penguji::whereIn('kelompok_id', $kelompokIds)
                ->with('Kelompok')
                ->get()
                ->groupBy('kelompok_id')
                ->map(function ($pengujiList) {
                    return $pengujiList->sortBy('id')->values();
                });

            $anggotaPerKelompok = $anggotaRaw->groupBy('kelompok_id');

            $kelompokList = $kelompokIds->map(function ($kelompokId) use ($kelompokMap, $anggotaPerKelompok, $mahasiswaMap, $jadwalMap, $pengujiMap, $user_id) {
                $kelompok = $kelompokMap->get($kelompokId);
                $anggota = collect($anggotaPerKelompok->get($kelompokId, []))
                    ->map(function ($member) use ($mahasiswaMap) {
                        $mahasiswa = $mahasiswaMap->get($member->user_id);

                        return [
                            'user_id' => $member->user_id,
                            'nama' => $mahasiswa->nama ?? 'Mahasiswa tidak ditemukan',
                            'nim' => $mahasiswa->nim ?? '-',
                            'angkatan' => $mahasiswa->angkatan ?? '-',
                        ];
                    })
                    ->values();

                $jadwal = $jadwalMap->get($kelompokId);

                // Cari posisi penguji saat ini
                $pengujiList = $pengujiMap->get($kelompokId, []);
                $posisiPenguji = null;
                foreach ($pengujiList as $index => $penguji) {
                    if ($penguji->user_id == $user_id) {
                        $posisiPenguji = $index + 1;
                        break;
                    }
                }

                return [
                    'kelompok_id' => $kelompokId,
                    'nomor_kelompok' => $kelompok->nomor_kelompok ?? $kelompokId,
                    'status_kelompok' => $kelompok->status ?? '-',
                    'jumlah_anggota' => $anggota->count(),
                    'anggota' => $anggota,
                    'jadwal' => $jadwal ? [
                        'waktu_mulai' => $jadwal->waktu_mulai,
                        'waktu_selesai' => $jadwal->waktu_selesai,
                    ] : null,
                    'posisi_penguji' => $posisiPenguji,
                    'jumlah_penguji' => count($pengujiList),
                ];
            })->sortBy(function ($item) {
                return (int) preg_replace('/\D/', '', (string) $item['nomor_kelompok']);
            })->values();
        }

        $jumlah_pengumuman = Pengumuman::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();
        $jumlah_tugas = Tugas::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();
        $jadwal = Jadwal::with('kelompok')
            ->where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->get();

        $events = $jadwal->map(function ($item) {
            return [
                'title' => 'Kelompok ' . $item->kelompok->nomor_kelompok . 'seminar  ',
                'start' => Carbon::parse($item->waktu_mulai)->toIso8601String(),
                'end' => Carbon::parse($item->waktu_selesai)->toIso8601String(),
            ];
        });
        $token = session('token');
        $user_id = session('user_id');
        $role_ids = [2, 4];
        $prodi_ids = DosenRole::where('user_id', $user_id)
            ->where('status', 'Aktif')
            ->where('role_id', $role_ids)
            ->pluck('prodi_id');
        $TM_ids = DosenRole::where('user_id', $user_id)
            ->where('status', 'Aktif')
            ->where('role_id', $role_ids)
            ->pluck('TM_id');
        $KPA_ids = DosenRole::where('user_id', $user_id)
            ->where('status', 'Aktif')
            ->where('role_id', $role_ids)
            ->pluck('KPA_id');
        $prodi_ids = $prodi_ids->unique();
        $TM_ids = $TM_ids->unique();
        $KPA_ids = $KPA_ids->unique();
        // Mengambil pengumuman yang hanya terkait dengan prodi_id yang sesuai dan status 'aktif'
        $pengumuman = Pengumuman::with(['prodi', 'kategoriPA'])
            ->wherein('prodi_id', $prodi_ids)
            ->wherein('KPA_id', $KPA_ids)
            ->wherein('TM_id', $TM_ids)
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->get();
        // 
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen");
        if ($responseDosen->successful()) {
            $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
            // Buat map user_id => nama
            $dosen_map = collect($dosen_list)->keyBy('user_id');

            $pengumuman->each(function ($item) use ($dosen_map) {
                $item->nama = $dosen_map[$item->user_id]['nama'] ?? 'N/A';
            });
        } else {
            // Tangani jika API gagal
            $pengumuman->each(function ($item) {
                $item->nama = 'N/A'; // Tampilkan N/A jika API gagal
            });
        }

        return view('pages.Penguji.dashboard', compact('jumlah_kelompok', 'jumlah_pengumuman', 'events', 'jumlah_tugas', 'pengumuman', 'kelompokList'));

    }
    public function mahasiswa()
    {
        $user_id = session('user_id');
        $token = session('token');

        // Ambil kelompok_id berdasarkan user_id
        $kelompok_id = KelompokMahasiswa::where('user_id', $user_id)->value('kelompok_id');

        $mahasiswa_kelompok = collect();
        $pembimbing = collect();

        if ($kelompok_id) {
            // Ambil semua anggota kelompok
            $mahasiswa_kelompok = KelompokMahasiswa::where('kelompok_id', $kelompok_id)->get();

            // Ambil data mahasiswa dari API
            $mahasiswaResponse = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/mahasiswa");

            $mahasiswa_map = collect();

            if ($mahasiswaResponse->successful()) {
                $data = $mahasiswaResponse->json();
                $listMahasiswa = $data['data']['mahasiswa'] ?? [];

                // Buat map: user_id => mahasiswa
                $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
            }

            // Tambahkan data nama, nim, angkatan ke masing-masing anggota
            $mahasiswa_kelompok = $mahasiswa_kelompok->map(function ($item) use ($mahasiswa_map) {
                $mhs = $mahasiswa_map->get($item->user_id);
                $item->nama = $mhs['nama'] ?? 'N/A';
                $item->nim = $mhs['nim'] ?? 'N/A';
                $item->angkatan = $mhs['angkatan'] ?? 'N/A';
                return $item;
            });

            // Ambil user_id pembimbing kelompok
            $pembimbing_ids = pembimbing::where('kelompok_id', $kelompok_id)->pluck('user_id');

            // Ambil data dosen dari API
            $dosenResponse = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen");

            $dosen_map = collect();

            if ($dosenResponse->successful()) {
                $data = $dosenResponse->json();
                $listDosen = $data['data']['dosen'] ?? [];

                // Buat map: user_id => dosen
                $dosen_map = collect($listDosen)->keyBy('user_id');
            }

            // Ubah data pembimbing: isikan nama
            $pembimbing = $pembimbing_ids->map(function ($id) use ($dosen_map) {
                return (object) [
                    'user_id' => $id,
                    'nama' => $dosen_map->get($id)['nama'] ?? 'N/A'
                ];
            });
            $penguji_ids = penguji::where('kelompok_id', $kelompok_id)->pluck('user_id');
            $penguji = $penguji_ids->map(function ($id) use ($dosen_map) {
                return (object) [
                    'user_id' => $id,
                    'nama' => $dosen_map->get($id)['nama'] ?? 'N/A'
                ];
            });
        }

        $jadwal = Jadwal::with(['kelompok', 'ruangan'])
            ->where('kelompok_id', $kelompok_id)->get();

        return view('pages.Mahasiswa.dashboard', compact('mahasiswa_kelompok', 'pembimbing', 'penguji', 'jadwal'));


    }

    public function BAAK()
    {
        // Menghitung total mahasiswa dari semua kelompok
        $jumlah_mahasiswa = KelompokMahasiswa::count();

        // Menghitung total pengumuman
        $jumlah_pengumuman = Pengumuman::count();

        // Menghitung total dosen
        $jumlah_dosen = DosenRole::distinct('user_id')->count('user_id');

        // Mengambil jadwal dan membuat events untuk calendar
        $jadwal = Jadwal::all();
        $events = $jadwal->map(function ($item) {
            return [
                'title' => 'Kelompok ' . $item->kelompok->nomor_kelompok . ' seminar',
                'start' => Carbon::parse($item->waktu_mulai)->toIso8601String(),
                'end' => Carbon::parse($item->waktu_selesai)->toIso8601String(),
            ];
        });

        return view('pages.BAAK.dashboard', compact('jumlah_mahasiswa', 'jumlah_pengumuman', 'jumlah_dosen', 'events'));
    }

    // ============= CHECK VERIFICATION STATUS =============
    /**
     * Check verification status untuk alur PA
     * Return array dengan status untuk setiap tahapan
     * 
     * Untuk kelompok: Cek apakah SEMUA mahasiswa sudah mendapat kelompok
     * Bukan hanya cek apakah ada kelompok yang ada
     */
    private function checkVerificationStatus($KPA_id, $prodi_id, $TM_id)
    {
        // ===== CHECK KELOMPOK =====
        // Count kelompok yang ada untuk KPA, prodi, dan tahun ini
        $kelompok_count = Kelompok::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->count();

        // Jika ada kelompok, berarti sudah ada pembagian
        $kelompok_status = $kelompok_count > 0 ? 'success' : 'pending';

        // Check pembimbing
        $pembimbing_count = pembimbing::whereHas('kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
            $q->where('KPA_id', $KPA_id);
            $q->where('prodi_id', $prodi_id);
            $q->where('TM_id', $TM_id);
        })->count();

        $pembimbing_status = $pembimbing_count > 0 ? 'success' : 'pending';

        // Check penguji
        $penguji_count = Penguji::whereHas('kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
            $q->where('KPA_id', $KPA_id);
            $q->where('prodi_id', $prodi_id);
            $q->where('TM_id', $TM_id);
        })->count();

        $penguji_status = $penguji_count > 0 ? 'success' : 'pending';

        // Check jadwal
        $jadwal_count = Jadwal::whereHas('kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {
            $q->where('KPA_id', $KPA_id);
            $q->where('prodi_id', $prodi_id);
            $q->where('TM_id', $TM_id);
        })->count();

        $jadwal_status = $jadwal_count > 0 ? 'success' : 'pending';

        return [
            'kelompok' => $kelompok_status,
            'pembimbing' => $pembimbing_status,
            'penguji' => $penguji_status,
            'jadwal' => $jadwal_status
        ];
    }

}

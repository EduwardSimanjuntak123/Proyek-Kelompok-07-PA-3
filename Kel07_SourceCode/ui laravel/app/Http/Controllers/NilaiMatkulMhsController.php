<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiMatkulMhsController extends Controller
{
    public function index(Request $request)
    {

        // dd($request);

        $query = Mahasiswa::query();

        // FILTER
        if ($request->nim) {
            $query->where('nim', 'like', '%' . $request->nim . '%');
        }
        

        if ($request->nama) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        // 1 halaman = 1 mahasiswa
        $mahasiswa = $query->paginate(1)->withQueryString();

        $nilaiSemester = collect();

        if ($mahasiswa->count()) {

            $mhs = $mahasiswa->first();

            $nilai = DB::table('nilai_matkul_mahasiswa as n')
                ->join('mata_kuliah as mk', 'n.kode_mk', '=', 'mk.kode_mk')
                ->where('n.mahasiswa_id', $mhs->id)
                ->select(
                    'mk.nama_matkul',
                    'n.nilai_angka',
                    'n.nilai_huruf',
                    'n.semester'
                )
                ->get();

            // group berdasarkan semester
            $nilaiSemester = $nilai
                ->groupBy('semester')
                ->sortKeys();
        }
        $listAngkatan = Mahasiswa::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');


        return view('pages.Koordinator.nilai_matkul_mahasiswa.index', compact(
            'mahasiswa',
            'nilaiSemester',
            'listAngkatan'
        ));
    }
}

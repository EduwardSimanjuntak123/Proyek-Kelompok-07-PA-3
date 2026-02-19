<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\KelompokMahasiswa;
use App\Models\Nilai_Individu;
use App\Models\Nilai_kelompok;
use App\Models\pembimbing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NilaiIndividu_Controller extends Controller
{

    public function indexpembimbing1()
    {
        $token = session('token');
        $userId = session('user_id');
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $token = session('token');
        $roleId = session('role_id');
        $dosenrole = session('dosen_roles');
       
        
        // Ambil data kelompok
        $kelompoks = Kelompok::with(['pembimbing.dosenRoles', 'KelompokMahasiswa', 'nilais','kategoriPA','prodi'])
            ->whereHas('pembimbing.dosenRoles', function ($query) use ($userId,$roleId) {
                $query->where('user_id', $userId);
            })->get();
    
        // Ambil semua mahasiswa dari API
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);
    
        $mahasiswa_map = collect();
        if ($response->successful()) {
            $data = $response->json();
            $listMahasiswa = $data['data']['mahasiswa'] ?? [];
            $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
        }
    
        // Kumpulkan semua user_id dari KelompokMahasiswa
        $user_ids = $kelompoks->flatMap(function ($kelompok) {
            return $kelompok->KelompokMahasiswa->pluck('user_id');
        });
    
        // Ambil nilai individu
        $nilaiindividu = Nilai_Individu::whereIn('user_id', $user_ids)  
        ->where('penilai_id',$userId)
        ->where('role_id', '3')
        ->get()->keyBy('user_id');
    
        // Pasang nama, nim, dan nilai ke setiap mahasiswa
        $kelompoks->each(function ($kelompok) use ($mahasiswa_map, $nilaiindividu) {
            $kelompok->KelompokMahasiswa->each(function ($mhs) use ($mahasiswa_map, $nilaiindividu) {
                $data = $mahasiswa_map->get($mhs->user_id);
                $mhs->nama = $data['nama'] ?? 'N/A';
                $mhs->nim = $data['nim'] ?? 'N/A';
                $mhs->nilai_individu = $nilaiindividu->get($mhs->user_id)->nilai ?? null;
            });
        });
//dd($roleId);pages.Pembimbing.Nilai_Individu.index
        return view('pages.Pembimbing.Nilai_Individu.index', [
            'kelompoks' => $kelompoks,
             'nilaiindividu' => $nilaiindividu
        ]);
    }

    public function storepembimbing1(Request $request){
        $userId = session('user_id');

        $request->validate([
            'user_id' =>'required',
            'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        // $nilai = Nilai_Individu::findOrFail($id);
        // dd($request->all());
        Nilai_Individu::create([
            'user_id' => $request->user_id,
            'penilai_id' => $userId, 
            'role_id' => 3,
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('pembimbing1.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }

    public function updatepembimbing1(Request $request, $id){
       

        $request->validate([
            // 'user_id' =>'required',
            // 'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->update([
            
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('pembimbing1.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }
        
    public function destroypembimbing1($id){
        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }
    //untuk pembimbing 2

    public function indexpembimbing2()
    {
        $token = session('token');
        $userId = session('user_id');
        $roleId = session('role_id');
        
        // Ambil data kelompok
        $kelompoks = Kelompok::with(['pembimbing.dosenRoles', 'KelompokMahasiswa', 'nilais','kategoriPA','prodi'])
            ->whereHas('pembimbing.dosenRoles', function ($query) use ($userId,$roleId) {
                $query->where('user_id', $userId);
            })
            ->get();
    
        // Ambil semua mahasiswa dari API
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);
    
        $mahasiswa_map = collect();
        if ($response->successful()) {
            $data = $response->json();
            $listMahasiswa = $data['data']['mahasiswa'] ?? [];
            $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
        }
    
        // Kumpulkan semua user_id dari KelompokMahasiswa
        $user_ids = $kelompoks->flatMap(function ($kelompok) {
            return $kelompok->KelompokMahasiswa->pluck('user_id');
        });
    
        // Ambil nilai individu
        $nilaiindividu = Nilai_Individu::whereIn('user_id', $user_ids)  
        ->where('penilai_id',$userId)
       ->where('role_id', 5)
        ->get()->keyBy('user_id');
    
        // Pasang nama, nim, dan nilai ke setiap mahasiswa
        $kelompoks->each(function ($kelompok) use ($mahasiswa_map, $nilaiindividu, $roleId) {
            $kelompok->KelompokMahasiswa->each(function ($mhs) use ($mahasiswa_map, $nilaiindividu) {
                $data = $mahasiswa_map->get($mhs->user_id);
                $mhs->nama = $data['nama'] ?? 'N/A';
                $mhs->nim = $data['nim'] ?? 'N/A';
                $mhs->nilai_individu = $nilaiindividu->get($mhs->user_id)->nilai ?? null;
            });
        });

        return view('pages.Pembimbing.Nilai_Individu.indexp2', [
            'kelompoks' => $kelompoks,
             'nilaiindividu' => $nilaiindividu
        ]);
    }

    public function storepembimbing2(Request $request){
        $userId = session('user_id');
        
        $request->validate([
            'user_id' =>'required',
            'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        // $nilai = Nilai_Individu::findOrFail($id);
        // dd($request->all());    
        Nilai_Individu::create([
            'user_id' => $request->user_id,
            'penilai_id' => $userId, 
            'role_id' => 5,
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('pembimbing2.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }

    public function updatepembimbing2(Request $request, $id){
       

        $request->validate([
            // 'user_id' =>'required',
            // 'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->update([
            
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('pembimbing2.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }
        
    public function destroypembimbing2($id){
        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }



    //untuk penguji 1

    public function indexpenguji1()
    {
        $token = session('token');
        $userId = session('user_id');
        $roleId = session('role_id');
        
        // Ambil data kelompok
        $kelompoks = Kelompok::with(['penguji.dosenRoles', 'KelompokMahasiswa', 'nilais','kategoriPA','prodi'])
            ->whereHas('penguji.dosenRoles', function ($query) use ($userId,$roleId) {
                $query->where('user_id', $userId);
            })
            ->get();
    
        // Ambil semua mahasiswa dari API
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);
    
        $mahasiswa_map = collect();
        if ($response->successful()) {
            $data = $response->json();
            $listMahasiswa = $data['data']['mahasiswa'] ?? [];
            $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
        }
    
        // Kumpulkan semua user_id dari KelompokMahasiswa
        $user_ids = $kelompoks->flatMap(function ($kelompok) {
            return $kelompok->KelompokMahasiswa->pluck('user_id');
        });
    
        // Ambil nilai individu
        $nilaiindividu = Nilai_Individu::whereIn('user_id', $user_ids)  
        ->where('penilai_id',$userId)
        ->where('role_id', 2)
        ->get()->keyBy('user_id');
        
        // Pasang nama, nim, dan nilai ke setiap mahasiswa
        $kelompoks->each(function ($kelompok) use ($mahasiswa_map, $nilaiindividu) {
            $kelompok->KelompokMahasiswa->each(function ($mhs) use ($mahasiswa_map, $nilaiindividu) {
                $data = $mahasiswa_map->get($mhs->user_id);
                $mhs->nama = $data['nama'] ?? 'N/A';
                $mhs->nim = $data['nim'] ?? 'N/A';
                $mhs->nilai_individu = $nilaiindividu->get($mhs->user_id)->nilai ?? null;
            });
        });
//dd($roleId);pages.Penguji.Nilai_Individu
        return view('pages.Penguji.Nilai_Individu.index', [
            'kelompoks' => $kelompoks,
             'nilaiindividu' => $nilaiindividu
        ]);
    }

    public function storepenguji1(Request $request){
        $userId = session('user_id');

        $request->validate([
            'user_id' =>'required',
            'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        // $nilai = Nilai_Individu::findOrFail($id);

        Nilai_Individu::create([
            'user_id' => $request->user_id,
            'penilai_id' => $userId, 
            'role_id' => 2,
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('penguji1.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }

    public function updatepenguji1(Request $request, $id){
       

        $request->validate([
            // 'user_id' =>'required',
            // 'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->update([
            
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('penguji1.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }
        
    public function destroypenguji1($id){
        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    //untuk penguji 2

    public function indexpenguji2()
    {
        $token = session('token');
        $userId = session('user_id');
        $roleId = session('role_id');
        
        // Ambil data kelompok
        $kelompoks = Kelompok::with(['penguji.dosenRoles', 'KelompokMahasiswa', 'nilais','kategoriPA','prodi'])
            ->whereHas('penguji.dosenRoles', function ($query) use ($userId,$roleId) {
                $query->where('user_id', $userId);
            })
            ->get();
    
        // Ambil semua mahasiswa dari API
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);
    
        $mahasiswa_map = collect();
        if ($response->successful()) {
            $data = $response->json();
            $listMahasiswa = $data['data']['mahasiswa'] ?? [];
            $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
        }
    
        // Kumpulkan semua user_id dari KelompokMahasiswa
        $user_ids = $kelompoks->flatMap(function ($kelompok) {
            return $kelompok->KelompokMahasiswa->pluck('user_id');
        });
    
        // Ambil nilai individu
        $nilaiindividu = Nilai_Individu::whereIn('user_id', $user_ids)  
        ->where('penilai_id',$userId)
        ->where('role_id', 4)
        ->get()->keyBy('user_id');
        
        // Pasang nama, nim, dan nilai ke setiap mahasiswa
        $kelompoks->each(function ($kelompok) use ($mahasiswa_map, $nilaiindividu) {
            $kelompok->KelompokMahasiswa->each(function ($mhs) use ($mahasiswa_map, $nilaiindividu) {
                $data = $mahasiswa_map->get($mhs->user_id);
                $mhs->nama = $data['nama'] ?? 'N/A';
                $mhs->nim = $data['nim'] ?? 'N/A';
                $mhs->nilai_individu = $nilaiindividu->get($mhs->user_id)->nilai ?? null;
            });
        });
//dd($roleId);
        return view('pages.Penguji.Nilai_Individu.indexp2', [
            'kelompoks' => $kelompoks,
             'nilaiindividu' => $nilaiindividu
        ]);
    }

    public function storepenguji2(Request $request){
        $userId = session('user_id');

        $request->validate([
            'user_id' =>'required',
            'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        // $nilai = Nilai_Individu::findOrFail($id);

        Nilai_Individu::create([
            'user_id' => $request->user_id,
            'penilai_id' => $userId, 
            'role_id' => 4,
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('penguji2.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }

    public function updatepenguji2(Request $request, $id){
       

        $request->validate([
            // 'user_id' =>'required',
            // 'penilai_id' => 'required',
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B1_total' => 'numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B2_total' => 'numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
            'B3_total' => 'numeric|min:0|max:100',
            'B_total' => 'numeric|min:0|max:45',
        ]);

        $B1_total= 0.02*($request->B11+$request->B12+$request->B13+$request->B14+$request->B15);
        $B2_total= 0.02*($request->B21+$request->B22+$request->B23+$request->B24+$request->B25);
        $B3_total= 0.25*$request->B31;
        $B_total =$B1_total+ $B2_total + $B3_total;

        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->update([
            
            'B11' => $request->B11,
            'B12' => $request->B12,
            'B13' => $request->B13,
            'B14' => $request->B14,
            'B15' => $request->B15,
            'B1_total' => $B1_total,
            'B21' => $request->B21,
            'B22' => $request->B22,
            'B23' => $request->B23,
            'B24' => $request->B24,
            'B25' => $request->B25,
            'B2_total' => $B2_total,
            'B31' => $request->B31,
            'B3_total' => $B3_total,
            'B_total' => $B_total,
        ]);
        return redirect()->route('penguji2.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }
        
    public function destroypenguji2($id){
        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

}

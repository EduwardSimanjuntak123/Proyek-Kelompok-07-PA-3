<?php

namespace App\Http\Controllers;
use App\Models\Kelompok;
use App\Models\Nilai_Bimbingan;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NilaiBimbingan_Controller extends Controller
{

    public function indexpembimbing1()
    {
        $token = session('token');
        $userId = session('user_id');
        $roleId = session('role_id');
        $KPAId = session('KPA_id');
   
        
        // Ambil data kelompok
        $kelompoks = Kelompok::with(['pembimbing.dosenRoles', 'KelompokMahasiswa', 'nilais'])
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
        $nilaiindividu = Nilai_Bimbingan::whereIn('user_id', $user_ids)  
        ->where('penilai_id',$userId)
        ->where('role_id', 3)
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
        return view('pages.Pembimbing.Nilai_Individu.indexp3', [
            'kelompoks' => $kelompoks,
             'nilaiindividu' => $nilaiindividu
        ]);
    }

    public function storepembimbing1(Request $request){
        $userId = session('user_id');
        
        $request->validate([
            'user_id' =>'required',
            'penilai_id' => 'required',
            'A1' => 'required|numeric|min:0|max:100',
            'A2' => 'required|numeric|min:0|max:100',
            'A3' => 'required|numeric|min:0|max:100',
            'A4' => 'required|numeric|min:0|max:100',
            'A5' => 'required|numeric|min:0|max:100',
            'Total' => 'numeric|min:0|max:100',
        ]);

        $Total = 0.1 * $request->A1
        + 0.1 * $request->A2
        + 0.15 * $request->A3
        + 0.4 * $request->A4
        + 0.25 * $request->A5;

        // $nilai = Nilai_Individu::findOrFail($id);
        // dd($request->all());    
        Nilai_Bimbingan::create([
            'user_id' => $request->user_id,
            'penilai_id' => $userId, 
            'role_id' => 3,
            'A1' => $request->A1,
            'A2' => $request->A2,
            'A3' => $request->A3,
            'A4' => $request->A4,
            'A5' => $request->A5,
            'Total' => $Total,
        ]);
        return redirect()->route('pembimbing1.NilaiBimbingan.index')->with('success', 'Nilai berhasil Disimpan');
    }

    public function updatepembimbing1(Request $request, $id){
       

        $request->validate([
            // 'user_id' =>'required',
            // 'penilai_id' => 'required',
            'A1' => 'required|numeric|min:0|max:100',
            'A2' => 'required|numeric|min:0|max:100',
            'A3' => 'required|numeric|min:0|max:100',
            'A4' => 'required|numeric|min:0|max:100',
            'A5' => 'required|numeric|min:0|max:100',
            'Total' => 'numeric|min:0|max:100',
           
        ]);

        $Total = 0.1 * $request->A1
        + 0.1 * $request->A2
        + 0.15 * $request->A3
        + 0.4 * $request->A4
        + 0.25 * $request->A5;

        $nilai = Nilai_Bimbingan::findOrFail($id);
        $nilai->update([
            
            'A1' => $request->A1,
            'A2' => $request->A2,
            'A3' => $request->A3,
            'A4' => $request->A4,
            'A5' => $request->A5,
            'Total' => $Total,
        ]);
        return redirect()->route('pembimbing1.NilaiBimbingan.index')->with('success', 'Nilai berhasil Disimpan');
    }
        
    public function destroypembimbing1($id){
        $nilai = Nilai_Bimbingan::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }




    public function indexpembimbing2()
    {
        $token = session('token');
        $userId = session('user_id');
        $roleId = session('role_id');
        
        // Ambil data kelompok
        $kelompoks = Kelompok::with(['pembimbing.dosenRoles', 'KelompokMahasiswa', 'nilais'])
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
        $nilaiindividu = Nilai_Bimbingan::whereIn('user_id', $user_ids)  
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

        return view('pages.pembimbing.Nilai_Individu.indexp3', [
            'kelompoks' => $kelompoks,
             'nilaiindividu' => $nilaiindividu
        ]);
    }

    public function storepembimbing2(Request $request){
        $userId = session('user_id');
        
        $request->validate([
            'user_id' =>'required',
            'penilai_id' => 'required',
            'A1' => 'required|numeric|min:0|max:100',
            'A2' => 'required|numeric|min:0|max:100',
            'A3' => 'required|numeric|min:0|max:100',
            'A4' => 'required|numeric|min:0|max:100',
            'A5' => 'required|numeric|min:0|max:100',
            'Total' => 'numeric|min:0|max:100',
        ]);

        $Total = 0.1 * $request->A1
        + 0.1 * $request->A2
        + 0.15 * $request->A3
        + 0.4 * $request->A4
        + 0.25 * $request->A5;

        // $nilai = Nilai_Individu::findOrFail($id);
        // dd($request->all());    
        Nilai_Bimbingan::create([
            'user_id' => $request->user_id,
            'penilai_id' => $userId, 
            'role_id' => 5,
            'A1' => $request->A1,
            'A2' => $request->A2,
            'A3' => $request->A3,
            'A4' => $request->A4,
            'A5' => $request->A5,
            'Total' => $Total,
        ]);
        return redirect()->route('pembimbing2.NilaiBimbingan.index')->with('success', 'Nilai berhasil Disimpan');
    }

    public function updatepembimbing2(Request $request, $id){
       

        $request->validate([
            // 'user_id' =>'required',
            // 'penilai_id' => 'required',
            'A1' => 'required|numeric|min:0|max:100',
            'A2' => 'required|numeric|min:0|max:100',
            'A3' => 'required|numeric|min:0|max:100',
            'A4' => 'required|numeric|min:0|max:100',
            'A5' => 'required|numeric|min:0|max:100',
            'Total' => 'numeric|min:0|max:100',
           
        ]);

        $Total = 0.1 * $request->A1
        + 0.1 * $request->A2
        + 0.15 * $request->A3
        + 0.4 * $request->A4
        + 0.25 * $request->A5;

        $nilai = Nilai_Bimbingan::findOrFail($id);
        $nilai->update([
            
            'A1' => $request->A1,
            'A2' => $request->A2,
            'A3' => $request->A3,
            'A4' => $request->A4,
            'A5' => $request->A5,
            'Total' => $Total,
        ]);
        return redirect()->route('pembimbing2.NilaiIndividu.index')->with('success', 'Nilai berhasil Disimpan');
    }
        
    public function destroypembimbing2($id){
        $nilai = Nilai_Bimbingan::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

}

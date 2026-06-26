<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use App\Models\pengumpulan_tugas;
use Illuminate\Http\Request;
use App\Models\Tugas;
class penguji_tugas_Controller extends Controller
{
    public function indexpenguji(){
        
        $user_id = session('user_id');
       $role_ids = [2,4];
       $prodi_ids = DosenRole::where('user_id', $user_id)
                          ->where('status', 'Aktif')
                          ->whereIn('role_id', $role_ids)
                          ->pluck('prodi_id');
        $TM_ids = DosenRole::where('user_id', $user_id)
                            ->where('status', 'Aktif')
                            ->whereIn('role_id', $role_ids)
                          ->pluck('TM_id');
        $KPA_ids = DosenRole::where('user_id', $user_id)
                          ->where('status', 'Aktif')
                          ->whereIn('role_id', $role_ids)
                          ->pluck('KPA_id');
        $kategoritugas = ['Revisi','Artefak']; 
        $prodi_ids = $prodi_ids->unique();
        $TM_ids = $TM_ids->unique();
        $KPA_ids = $KPA_ids->unique();
        $tugas = Tugas::with(['prodi', 'tahunMasuk', 'kategoriPA','dosenRoles'])
        ->whereIn('kategori_tugas',$kategoritugas)
         ->whereIn('prodi_id', $prodi_ids)
          ->whereIn('KPA_id', $KPA_ids)
           ->whereIn('TM_id', $TM_ids)
        ->get();  
        return view('pages.Penguji.tugas.index',compact('tugas'));
         

    }
    public function showpenguji($id)
    {
        $tugas = Tugas::findOrFail($id);
        // dd($tugas);
        return view('pages.Penguji.tugas.show', compact('tugas'));
    }

    public function index_penguji($id){
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');

        $artefak = pengumpulan_tugas::with(['kelompok.penguji','tugas'])
            ->where('tugas_id', $id)// ambil berdasarkan tugas_id = $id
            ->whereHas('kelompok',function ($query) use ($user_id){
                $query->whereHas('penguji', function ($q ) use ($user_id){
                    $q->where('user_id',$user_id);

                });
            }) 
            ->whereHas('tugas', function ($query) use ($prodi_id, $KPA_id, $TM_id) {
                $query->where('prodi_id', $prodi_id)
                      ->where('KPA_id', $KPA_id)
                      ->where('TM_id', $TM_id);
            })
            ->get();
        return view('pages.Penguji.tugas.show_submission', compact('artefak'));
    }
    public function formFeedback($id){
        $user_id = session('user_id');

        $artefak = pengumpulan_tugas::with('kelompok.penguji')
        ->where('id', $id)
        ->whereHas('kelompok.penguji', function($query) use ($user_id){
            $query->where('user_id', $user_id);
        })
        ->firstOrFail();
        return view('pages.Penguji.tugas.feedback_form', compact('artefak'));
    }
    public function submitFeedback(Request $request, $id)
    {
        $request->validate([
            'feedback_penguji'=>'required|string|max:1000',
        ]);

        $user_id = session('user_id');

        $artefak = pengumpulan_tugas :: with('kelompok.penguji')
        ->where('id', $id)
            ->whereHas('kelompok.penguji', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->firstOrFail();

        $artefak->feedback_penguji = $request -> feedback_penguji;
        $artefak->save();

        return redirect()->route('penguji.show.submitan', $artefak->tugas_id)
            ->with('success', 'Feedback berhasil dikirim.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\pengumpulan_tugas;
class Pembimbing_tugas_Controller extends Controller
{
    public function indexpembimbing(Request $request){
        $user_id = session('user_id');
        $role_ids = [3,5];
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
        $prodi_ids = $prodi_ids->unique();
        $TM_ids = $TM_ids->unique();
        $KPA_ids = $KPA_ids->unique();
        $tugas = Tugas::with(['prodi', 'tahunMasuk', 'kategoriPA','dosenRoles'])
         ->whereIn('prodi_id', $prodi_ids)
          ->whereIn('KPA_id', $KPA_ids)
           ->whereIn('TM_id', $TM_ids)
        ->get();  
            //  dd($tugas);
    
      return view('pages.Pembimbing.tugas.index',compact('tugas'));
       
    }
    public function showpembimbing($id)
    {
        $tugas = Tugas::findOrFail($id);
        // dd($tugas);
        return view('pages.Pembimbing.tugas.show', compact('tugas'));
    }
    public function formFeedback($id)
    {
        $user_id = session('user_id');

        $artefak = pengumpulan_tugas::with('kelompok.pembimbing')
            ->where('id', $id)
            ->whereHas('kelompok.pembimbing', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->firstOrFail();

        return view('pages.Pembimbing.tugas.feedback_form', compact('artefak'));
    }

    public function submitFeedback(Request $request, $id)
    {
        $request->validate([
            'feedback_pembimbing' => 'required|string|max:1000',
        ]);

        $user_id = session('user_id');

        $artefak = pengumpulan_tugas::with('kelompok.pembimbing')
            ->where('id', $id)
            ->whereHas('kelompok.pembimbing', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->firstOrFail();

        $artefak->feedback_pembimbing = $request->feedback_pembimbing;
        $artefak->save();

        return redirect()->route('pembimbing.show.submitan', $artefak->tugas_id)
            ->with('success', 'Feedback berhasil dikirim.');
    }

    public function index_pembimbing($id){
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');

        $artefak = pengumpulan_tugas::with(['kelompok.pembimbing','tugas'])
            ->where('tugas_id', $id)// ambil berdasarkan tugas_id = $id
            ->whereHas('kelompok',function ($query) use ($user_id){
                $query->whereHas('pembimbing', function ($q ) use ($user_id){
                    $q->where('user_id',$user_id);

                });
            }) 
            ->whereHas('tugas', function ($query) use ($prodi_id, $KPA_id, $TM_id) {
                $query->where('prodi_id', $prodi_id)
                      ->where('KPA_id', $KPA_id)
                      ->where('TM_id', $TM_id);
            })
            ->get();
        return view('pages.Pembimbing.tugas.show_submission', compact('artefak'));
    }
}

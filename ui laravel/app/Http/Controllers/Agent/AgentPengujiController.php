<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DosenRole;
use App\Models\Dosen;
use App\Models\Kelompok;
use App\Models\Pembimbing;
use App\Models\Penguji;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AgentPengujiController extends Controller
{

    public function index()
    {
        $userId = session('user_id');

        $roles = DosenRole::with('role','kategoriPA','TahunMasuk','Prodi','TahunAjaran')
            ->where('user_id',$userId)
            ->get()
            ->map(function($item){
                return [
                    'user_id' => $item->user_id,
                    'angkatan' => $item->TahunMasuk->Tahun_Masuk ?? '-',
                    'prodi' => $item->Prodi->nama_prodi ?? '-',
                    'role' => $item->role->role_name ?? '-',
                    'kategori_pa' => $item->kategoriPA->kategori_pa ?? '-',
                    'KPA_id' => $item->KPA_id,
                    'prodi_id' => $item->prodi_id,
                    'TM_id' => $item->TM_id,
                    'tahun_ajaran_id' => $item->tahun_ajaran_id,
                ];
            });

        $user = Dosen::where('user_id',$userId)->first();

        return view('pages.Koordinator.agent.agent-penguji',compact('roles','user'));
    }


    public function generate(Request $request)
    {

        try {

            $request->validate([
                'KPA_id' => 'required|integer',
                'prodi_id' => 'required|integer',
                'TM_id' => 'required|integer',
                'tahun_ajaran_id' => 'required|integer',
            ]);


            /*
            ===================================================
            AMBIL DATA KELOMPOK
            ===================================================
            */

            $kelompok = Kelompok::where('KPA_id',$request->KPA_id)
                ->where('prodi_id',$request->prodi_id)
                ->where('TM_id',$request->TM_id)
                ->where('tahun_ajaran_id',$request->tahun_ajaran_id)
                ->get(['id','nomor_kelompok']);


            /*
            ===================================================
            CEK KELOMPOK TANPA PEMBIMBING
            ===================================================
            */

            $kelompokTanpaPembimbing = Kelompok::where('KPA_id',$request->KPA_id)
                ->where('prodi_id',$request->prodi_id)
                ->where('TM_id',$request->TM_id)
                ->where('tahun_ajaran_id',$request->tahun_ajaran_id)
                ->doesntHave('pembimbing')
                ->count();

            if($kelompokTanpaPembimbing > 0){

                return response()->json([
                    'status'=>'error',
                    'message'=>"Ada $kelompokTanpaPembimbing kelompok yang belum memiliki pembimbing"
                ],400);

            }


            /*
            ===================================================
            DATA PEMBIMBING
            ===================================================
            */

            $pembimbing = Pembimbing::select('kelompok_id','user_id')->get();


            /*
            ===================================================
            DATA DOSEN YANG BISA JADI PENGUJI
            ===================================================
            */

            $dosen = Dosen::select('user_id','nama')->get();


            /*
            ===================================================
            FORMAT DATA KE JSON
            ===================================================
            */

            $payload = [
                'kelompok' => $kelompok,
                'pembimbing' => $pembimbing,
                'dosen' => $dosen
            ];

            Log::info("DATA DIKIRIM KE AI", $payload);


            /*
            ===================================================
            PANGGIL FASTAPI AI AGENT
            ===================================================
            */

            $response = Http::timeout(600)
                ->post('http://127.0.0.1:8001/generate-penguji',$payload);


            if(!$response->successful()){

                return response()->json([
                    'status'=>'error',
                    'message'=>'FastAPI tidak merespon'
                ],500);

            }


            $data = $response->json();

            Log::info("HASIL AI",$data);


            /*
            ===================================================
            SIMPAN HASIL KE DATABASE
            ===================================================
            */

            DB::beginTransaction();

            foreach($data['penguji'] as $item){

                foreach($item['penguji'] as $dosen_id){

                    Penguji::create([
                        'kelompok_id'=>$item['kelompok_id'],
                        'user_id'=>$dosen_id
                    ]);

                }

            }

            DB::commit();


            return response()->json([
                'status'=>'success',
                'message'=>'Penguji berhasil digenerate AI',
                'data'=>$data['penguji']
            ]);


        } catch(\Throwable $e){

            DB::rollBack();

            Log::error("Generate penguji error ".$e->getMessage());

            return response()->json([
                'status'=>'error',
                'message'=>'Terjadi error server'
            ],500);

        }

    }

}
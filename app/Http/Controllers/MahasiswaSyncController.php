<?php

namespace App\Http\Controllers;

use App\Services\MahasiswaSyncService;
use Illuminate\Http\Request;

class MahasiswaSyncController extends Controller
{
    public function sync(Request $request, MahasiswaSyncService $service)
    {
        $token = session('token');
        dd( "ini sudah login");

        if (!$token) {
            return response()->json([
                'message' => 'User belum login atau session CIS tidak ada'
            ], 401);
        }

        $total = $service->syncWithSession($token);

        return response()->json([
            'message' => 'Sinkronisasi mahasiswa berhasil',
            'total'   => $total
        ]);
    }
}
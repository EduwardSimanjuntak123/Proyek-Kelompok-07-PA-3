<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentController extends Controller
{

    public function context(Request $request)
    {
        return response()->json([
            "token" => session('token'),
            "user_id" => session('user_id'),
            "role" => session('role'),
            "prodi_id" => session('prodi_id'),
            "KPA_id" => session('KPA_id'),
            "TM_id" => session('TM_id')
        ]);
    }

}
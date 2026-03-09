<?php

namespace App\Http\Controllers;
use App\Models\DosenRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{

    public function index()
    {
        $userId = session('user_id');

        $roles = DosenRole::with('role', 'kategoriPA')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) use ($userId) {
                return [
                    'user_id' => $item->user_id,
                    'role' => $item->role->role_name,
                    'kategori_pa' => $item->kategoriPA->kategori_pa
                ];
            });

        // dd($roles);

        return view('pages.AI.index', compact('roles'));
    }

    public function send(Request $request)
    {

        $response = Http::post('http://127.0.0.1:8001/chat', [
            "message" => $request->message
        ]);

        return response()->json([
            "reply" => $response->json()['result'] ?? 'AI tidak menjawab'
        ]);

    }

}
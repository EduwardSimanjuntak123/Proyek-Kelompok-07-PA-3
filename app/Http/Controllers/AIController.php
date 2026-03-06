<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{

    public function index()
    {
        return view('pages.AI.index');
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
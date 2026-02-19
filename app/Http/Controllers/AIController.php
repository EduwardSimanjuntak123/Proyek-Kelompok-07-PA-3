<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function generateGroups(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8001/generate-groups', [
            'names' => $request->names,
            'group_size' => $request->group_size
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'AI Service Error',
                'detail' => $response->body()
            ], 500);
        }

        return response()->json($response->json());
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



class WhatsAppController extends Controller
{
    public function index(Request $request)
    {
    }

    public function create()
    {
    }
    public function store(Request $request)
    {

    }
    public function send(Request $request)
    {
        $BASE_URL = 'https://api.wachat-api.com/wachat_api/1.0/message';

        $response = Http::withHeaders([
            'APIKey' => 'F213E4CC9B967301E7D60D5646947286'
        ])->post($BASE_URL, [
                    'destination' => $request->nomor,
                    'message' => $request->pesan,
                    'queue' => '13538-177987908032603'
                ]);

        if ($response->successful()) {

            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $response->body()
        ], 500);
    }

    public function edit($encryptedId)
    {

    }

    public function update(Request $request, $encryptedId)
    {

    }



    public function destroy($id)
    {

    }


    public function show($id)
    {

    }

}

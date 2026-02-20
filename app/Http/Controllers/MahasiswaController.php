<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of mahasiswa dari API.
     */
    public function index()
    {
        $token = session('token');

        // Request ke API eksternal
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);

        $mahasiswa = collect();

        if ($response->successful()) {
            $data = $response->json();
            $mahasiswa = collect($data['data']['mahasiswa'] ?? []);
        }

        return view('pages.BAAK.listMahasiswa.index', compact('mahasiswa'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

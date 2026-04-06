<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pengumpulan_tugas;

class FeedbackController extends Controller
{
//     public function create($id)
// {
//     try {
//         $userID = session('user_id');
//         $token = session('token');

//         if (!$userID || !$token) {
//             return redirect()->route('login')->with('error', 'Sesi telah berakhir');
//         }

//         $artefak = pengumpulan_tugas::findOrFail($id);

//     } catch (Exception $e) {
//         Log::error('Error loading feedback form: ' . $e->getMessage());
//         return back()->with('error', 'Gagal memuat form feedback');
//     }

//     return view('pages.feedback.create', compact('artefak'));
// }

// public function store(Request $request)
// {
//     try {
//         $userID = session('user_id');

//         if (!$userID) {
//             return redirect()->route('login')->with('error', 'Sesi telah berakhir');
//         }

//         $validated = $request->validate([
//             'pengumpulan_tugas_id' => 'required|exists:pengumpulan_tugas,id',
//             'feedback' => 'required|string|max:1000',
//         ]);

//         Feedback::create([
//             'pengumpulan_tugas_id' => $validated['pengumpulan_tugas_id'],
//             'feedback' => $validated['feedback'],
//             'user_id' => $userID,
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);

//         return redirect()->route('artefak.index')->with('success', 'Feedback berhasil ditambahkan');
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return back()->withErrors($e->validator)->withInput();
//     } catch (Exception $e) {
//         Log::error('Error storing feedback: ' . $e->getMessage());
//         return back()->with('error', 'Gagal menyimpan feedback')->withInput();
//     }
// }
}

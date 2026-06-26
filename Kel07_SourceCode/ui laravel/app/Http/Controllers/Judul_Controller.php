<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\DosenRole;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Role;
use App\Models\Tugas;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Models\kategoriPA;
use App\Models\TahunMasuk;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\JudulProyekAkhir;
use App\Models\KelompokMahasiswa;

class Judul_Controller extends Controller
{

   public function index(Request $request)
{
    $user_id = session('user_id');

    $kelompok_id = KelompokMahasiswa::where('user_id', $user_id)
        ->value('kelompok_id');
    

    $judul = JudulProyekAkhir::where('kelompok_id', $kelompok_id)
        ->first();

    return view(
        'pages.Mahasiswa.Judul.index',
        compact('judul')
    );
}
    public function create()
    {
        return view('pages.Mahasiswa.Judul.create');
    }
}

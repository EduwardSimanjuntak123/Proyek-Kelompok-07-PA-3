<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use App\Models\DosenRole;
use App\Models\Kelompok;
use App\Models\KelompokMahasiswa;
class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // Mengarahkan ke view login
    }
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);
    
        $client = new Client();
        $url = env('API_URL') . "jwt-api/do-auth";
    
        try {
            // Melakukan request ke API untuk otentikasi
            $response = $client->post($url, [
                'form_params' => [
                    'username' => $request->input('username'),
                    'password' => $request->input('password'),
                ],
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 30,
            ]);
    
            // Decode response body
            $body = json_decode($response->getBody(), true);
    
            if (!$body['result']) {
                return redirect()->back()->withErrors(['login' => 'Username dan password tidak sesuai.'])->withInput();
            }
    
            // Ambil data user dan detail user
            $userTemp = $body['user'];
            $detailUserResponse = $this->getUserDetail($userTemp['user_id'], $userTemp['role'], $body['token']);
            $responseDetailUser = json_decode($detailUserResponse->getContent());
    
            if ($responseDetailUser->success === 'User valid!') {
                // Simpan data user di session
                $userData = [
                    'user_id' => $userTemp['user_id'],
                    'role' => $userTemp['role'],
                    'token' => $body['token'],
                    'name' => $responseDetailUser->details[0]->nama ?? '',
                    'email' => $responseDetailUser->details[0]->email ?? '',
                    'isLoggin' => true,
                ];
                session::put($userData); 
    
                // Check if the role is 'Dosen' (Lecturer)
                if ($userTemp['role'] == 'Dosen') {
                    // Fetch the active DosenRole
                    $dosenRole = DosenRole::where('user_id', $userTemp['user_id'])
                                          ->where('status', 'Aktif')
                                          ->first();
    
                    if ($dosenRole) {
                        // Simpan ke session
                        session([
                            'prodi_id' => $dosenRole->prodi_id,
                            'KPA_id' => $dosenRole->KPA_id,
                            'TM_id' => $dosenRole->TM_id,
                            'role_id' => $dosenRole->role_id,
                        ]);
    
                        // Dosen roles logic for redirection
                        $dosenRoles = DosenRole::where('user_id', $userTemp['user_id'])->pluck('role_id')->toArray();
                        session(['dosen_roles' => $dosenRoles]);
    
                        if (in_array('1', $dosenRoles)) {
                            return redirect()->route('dashboard.koordinator');
                        } elseif (in_array('2', $dosenRoles) || in_array('4', $dosenRoles)) {
                            return redirect()->route('dashboard.penguji');
                        } elseif (in_array('3', $dosenRoles) || in_array('5', $dosenRoles)) {
                            return redirect()->route('dashboard.pembimbing');
                        } else {
                            return redirect()->route('login.form')->withErrors(['login' => 'Role tidak valid.']);
                        }
                    } else {
                        return redirect()->route('login.form')->withErrors(['login' => 'Role Dosen tidak ditemukan atau tidak aktif.']);
                    }
                }
    
                // Redirect berdasarkan role pengguna
                if ($userTemp['role'] == 'Mahasiswa') {
                    $kelompokMahasiswa = KelompokMahasiswa ::where('user_id',$userTemp['user_id'])
                    ->join('kelompok', 'kelompok_mahasiswa.kelompok_id', '=', 'kelompok.id')
                    ->where('status','Aktif')
                    ->select('kelompok_mahasiswa.*', 'kelompok.KPA_id', 'kelompok.prodi_id', 'kelompok.TM_id')
                    ->first();
                    if (!$kelompokMahasiswa) {
                        return redirect()->route('login.form')->withErrors(['Login' => 'Anda belum tergabung dalam kelompok aktif.'])->withInput();;
                    }
                    
                        session([
                            'kelompok_id' => $kelompokMahasiswa->kelompok_id,
                            'prodi_id' => $kelompokMahasiswa->prodi_id,
                            'KPA_id' => $kelompokMahasiswa->KPA_id,
                            'TM_id' => $kelompokMahasiswa->TM_id,
                        ]);
                    
                    $kelompok = KelompokMahasiswa::where('user_id', $userTemp['user_id'])
                    ->pluck('kelompok_id')
                    ->toArray();
    
                    return redirect()->route('dashboard.mahasiswa');
                    
                } elseif ($userTemp['role'] == 'Staff') {
                    return redirect()->route('dashboard.BAAK');
                } else {
                    return redirect()->route('login.form')->withErrors(['login' => 'Role tidak valid.']);
                }
            } else {
                return redirect()->back()->withErrors(['login' => 'Gagal mendapatkan detail user.'])->withInput();
            }
        } catch (RequestException $e) {
            Log::error('RequestException: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghubungkan ke server.'])->withInput();
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan internal.'])->withInput();
        }
    }

    public function getUserDetail($user_id, $role, $token)
    {
        $url = env('API_URL') . ($role == 'Mahasiswa' ? "library-api/mahasiswa?userid=" : "library-api/pegawai?userid=") . $user_id;
        $client = new Client();

        try {
     
            // Melakukan request ke API untuk mengambil detail user
            $response = $client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);

            // Decode response body
            $data = json_decode($response->getBody(), true);
            $detailUser = $data['data'][$role == 'Mahasiswa' ? 'mahasiswa' : 'pegawai'] ?? [];

            if (empty($detailUser)) {
                return response()->json(['error' => 'Data user tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => 'User valid!',
                'details' => $detailUser
            ], 200);
        } catch (RequestException $e) {
            Log::error('RequestException: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mendapatkan data user.'], $e->getResponse()->getStatusCode() ?? 500);
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan internal.'], 500);
        }
    }

    public function logout(Request $request)
    {
        // Hapus semua session pengguna
        $request->session()->flush();
    
        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login.form')->with('success', 'Anda telah logout.');
    }


    public function profile($user_id, $role, $token){
         $url = env('API_URL') . ($role == 'Mahasiswa' ? "library-api/mahasiswa?userid=" : "library-api/pegawai?userid=") . $user_id;
        $client = new Client();

        try {
     
            // Melakukan request ke API untuk mengambil detail user
            $response = $client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);

            // Decode response body
            $data = json_decode($response->getBody(), true);
            $detailUser = $data['data'][$role == 'Mahasiswa' ? 'mahasiswa' : 'pegawai'] ?? [];

            if (empty($detailUser)) {
                return response()->json(['error' => 'Data user tidak ditemukan.'], 404);
            }

            // return response()->json([
            //     'success' => 'User valid!',
            //     'details' => $detailUser
            // ], 200);
             return view('pages.profile',compact('detailUser','role'));
        } catch (RequestException $e) {
            Log::error('RequestException: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mendapatkan data user.'], $e->getResponse()->getStatusCode() ?? 500);
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan internal.'], 500);
        }
       
    }
}

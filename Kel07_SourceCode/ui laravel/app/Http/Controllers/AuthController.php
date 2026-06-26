<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Jobs\SyncDosenJob;
use App\Jobs\SyncMatkulJob;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Exception;
use GuzzleHttp\Exception\RequestException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\DosenRole;
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

        // Check if DEMO_MODE is enabled
        if (env('DEMO_MODE') === true || env('DEMO_MODE') === 'true') {
            return $this->demoLogin($request);
        }

        // Production mode - use external API
        return $this->externalApiLogin($request);
    }

    /**
     * Demo login untuk testing tanpa external API
     * PENTING: Tidak membuat akun baru, hanya authenticate akun existing
     * Support: mahasiswa, dosen, staff (demo), chandra.simanjuntak (real staff)
     */
    private function demoLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Demo password untuk demo users
        $demoPassword = 'password123';
        $defaultPassword = 'default_password_123';

        // Try to find staff dengan username chandra.simanjuntak (REAL ACCOUNT)
        if (strtolower($username) === 'chandra.simanjuntak' || strtolower($username) === 'chandra') {
            // Cari staff user dengan email chandra.simanjuntak@del.ac.id
            $staffUser = User::where('email', 'chandra.simanjuntak@del.ac.id')->first();
            
            if (!$staffUser) {
                return redirect()->back()->withErrors(['login' => 'Akun staff tidak ditemukan.'])->withInput();
            }

            // Verify password using Hash::check (bekerja dengan bcrypt)
            if (!Hash::check($password, $staffUser->password)) {
                return redirect()->back()->withErrors(['login' => 'Username dan password tidak sesuai.'])->withInput();
            }

            $user = $staffUser;
            
            $userData = [
                'user_id' => $staffUser->id,
                'role' => 'Staff',
                'name' => $staffUser->name,
                'email' => $staffUser->email,
                'isLoggin' => true,
                'token' => 'demo-token-' . time(),
            ];
        }
        // Try to find mahasiswa by username
        else if (strtolower($username) === 'mahasiswa' || strpos(strtolower($username), 'mahasiswa') !== false) {
            // Validate demo password
            if ($password !== $demoPassword && $password !== $defaultPassword) {
                return redirect()->back()->withErrors(['login' => 'Username dan password tidak sesuai.'])->withInput();
            }

            // Get first available mahasiswa from database
            $mahasiswa = \DB::table('mahasiswa')
                ->whereNotNull('email')
                ->where('email', '!=', '-')
                ->first();
            
            if (!$mahasiswa) {
                return redirect()->back()->withErrors(['login' => 'Tidak ada data mahasiswa dengan email valid di database.'])->withInput();
            }

            // PENTING: Cari user existing dengan email dari mahasiswa table
            $user = User::where('email', $mahasiswa->email)->first();
            
            if (!$user) {
                return redirect()->back()->withErrors(['login' => 'Akun mahasiswa belum di-setup. Jalankan: php artisan db:seed --class=MigrateExistingUsersSeeder'])->withInput();
            }

            $userData = [
                'user_id' => $mahasiswa->user_id, // Gunakan mahasiswa.user_id untuk relasi kelompok
                'role' => 'Mahasiswa',
                'name' => $mahasiswa->nama ?: $user->name,
                'email' => $user->email,
                'isLoggin' => true,
                'token' => 'demo-token-' . time(),
            ];
        } 
        // Try to find dosen
        else if (strtolower($username) === 'dosen' || strpos(strtolower($username), 'dosen') !== false) {
            // Validate demo password
            if ($password !== $demoPassword && $password !== $defaultPassword) {
                return redirect()->back()->withErrors(['login' => 'Username dan password tidak sesuai.'])->withInput();
            }

            $dosen = \DB::table('dosen')
                ->whereNotNull('email')
                ->where('email', '!=', '-')
                ->first();
            
            if (!$dosen) {
                return redirect()->back()->withErrors(['login' => 'Tidak ada data dosen dengan email valid di database.'])->withInput();
            }

            // PENTING: Cari user existing dengan email dari dosen table
            $user = User::where('email', $dosen->email)->first();
            
            if (!$user) {
                return redirect()->back()->withErrors(['login' => 'Akun dosen belum di-setup. Jalankan: php artisan db:seed --class=MigrateExistingUsersSeeder'])->withInput();
            }

            $userData = [
                'user_id' => $dosen->user_id,
                'role' => 'Dosen',
                'name' => $dosen->nama ?: $user->name,
                'email' => $user->email,
                'isLoggin' => true,
                'token' => 'demo-token-' . time(),
            ];
        }
        // Try to find staff (DEMO)
        else if (strtolower($username) === 'staff' || strpos(strtolower($username), 'staff') !== false) {
            // Validate demo password
            if ($password !== $demoPassword && $password !== $defaultPassword) {
                return redirect()->back()->withErrors(['login' => 'Username dan password tidak sesuai.'])->withInput();
            }

            $userData = [
                'user_id' => 3001,
                'role' => 'Staff',
                'name' => 'Staff BAAK Test',
                'email' => 'staff@test.com',
                'isLoggin' => true,
                'token' => 'demo-token-' . time(),
            ];

            // Cari atau buat staff user
            $user = User::where('email', $userData['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt($demoPassword),
                ]);
            }
        }
        else {
            return redirect()->back()->withErrors(['login' => 'Username tidak valid. Gunakan: mahasiswa, dosen, staff, atau chandra.simanjuntak.'])->withInput();
        }

        // Set session data
        Session::put($userData);

        // Login to Laravel Auth
        Auth::login($user);

        // Redirect berdasarkan role
        if ($userData['role'] == 'Mahasiswa') {
            return redirect()->route('dashboard.mahasiswa');
        } elseif ($userData['role'] == 'Dosen') {
            session(['dosen_roles' => [1]]);
            return redirect()->route('dashboard.koordinator');
        } elseif ($userData['role'] == 'Staff') {
            return redirect()->route('dashboard.BAAK');
        }

        return redirect()->route('dashboard.mahasiswa');
    }

    /**
     * External API login (Production mode)
     */
    private function externalApiLogin(Request $request)
    {
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
                Session::put($userData);
                //    SyncDosenJob::dispatch($body['token']);
                // SYNC DATA DOSEN DARI CIS
                // app(\App\Services\DosenSyncService::class)
                //     ->syncWithSession($body['token']);
                // Check if the role is 'Dosen' (Lecturer)
                if ($userTemp['role'] == 'Dosen') {
                    // Fetch the active DosenRole
                    $dosenRole = DosenRole::where('user_id', $userTemp['user_id'])
                        ->where('status', 'Aktif')
                        ->first();

                    if ($dosenRole) {
                        // Simpan primary role ke session (untuk default context)
                        session([
                            'prodi_id' => $dosenRole->prodi_id,
                            'KPA_id' => $dosenRole->KPA_id,
                            'TM_id' => $dosenRole->TM_id,
                            'role_id' => $dosenRole->role_id,
                            'tahun_ajaran_id' => $dosenRole->tahun_ajaran_id,
                        ]);

                        // ✅ Query SEMUA roles yang aktif (tidak hanya yang pertama)
                        $dosenRoles = DosenRole::where('user_id', $userTemp['user_id'])
                            ->where('status', 'Aktif')
                            ->pluck('role_id')
                            ->toArray();
                        session(['dosen_roles' => $dosenRoles]);

                        // Redirect ke primary role berdasarkan priority
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
                // ===== TAMBAHKAN INI =====
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'] ?: $request->username,
                        'password' => bcrypt(str()->random(16)), // dummy
                    ]
                );

                // LOGIN KE LARAVEL (INI KUNCI UTAMA)
                Auth::login($user);
                // ========================
                // sync dosen di background
                Bus::chain([
                    new SyncDosenJob($body['token']),
                    new SyncMatkulJob($body['token']),
                ])->dispatch();
                // Redirect berdasarkan role pengguna
                if ($userTemp['role'] == 'Mahasiswa') {
                    $kelompokMahasiswa = KelompokMahasiswa::where('user_id', $userTemp['user_id'])
                        ->join('kelompok', 'kelompok_mahasiswa.kelompok_id', '=', 'kelompok.id')
                        ->where('status', 'Aktif')
                        ->select('kelompok_mahasiswa.*', 'kelompok.KPA_id', 'kelompok.prodi_id', 'kelompok.TM_id')
                        ->first();
                    if (!$kelompokMahasiswa) {
                        return redirect()->route('login.form')->withErrors(['Login' => 'Anda belum tergabung dalam kelompok aktif.'])->withInput();
                        ;
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


    public function profile($user_id, $role, $token)
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

            // return response()->json([
            //     'success' => 'User valid!',
            //     'details' => $detailUser
            // ], 200);
            return view('pages.profile', compact('detailUser', 'role'));
        } catch (RequestException $e) {
            Log::error('RequestException: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mendapatkan data user.'], $e->getResponse()->getStatusCode() ?? 500);
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan internal.'], 500);
        }

    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\DosenRole;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckDosenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        // Ambil semua role dosen dari session
        $dosenRoles = Session::get('dosen_roles', []);

        // Jika belum login atau tidak punya role apapun
        if (empty($dosenRoles)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Cek apakah dosen punya salah satu dari role yang diizinkan
        if (!array_intersect($allowedRoles, $dosenRoles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}

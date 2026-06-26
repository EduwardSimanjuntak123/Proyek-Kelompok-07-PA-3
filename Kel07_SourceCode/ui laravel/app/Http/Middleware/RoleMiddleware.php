<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Ambil role dari session
        $role = Session::get('role');

        if (!$role) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Periksa apakah role user termasuk dalam daftar role yang diizinkan
        if (!in_array($role, $roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}

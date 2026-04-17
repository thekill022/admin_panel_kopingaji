<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'SUPERADMIN') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Hanya SuperAdmin.'], 403);
            }
            abort(403, 'Akses ditolak. Hanya SuperAdmin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'instructor'])) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk admin.');
        }

        return $next($request);
    }
}

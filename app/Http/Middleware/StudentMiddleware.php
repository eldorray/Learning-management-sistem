<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admins may open the student area to see what students see.
        if (!auth()->check() || !in_array(auth()->user()->role, ['student', 'admin'], true)) {
            abort(403, 'Halaman ini hanya untuk siswa.');
        }

        return $next($request);
    }
}

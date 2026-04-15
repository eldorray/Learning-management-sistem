<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'parent') {
            abort(403, 'Halaman ini hanya untuk orang tua / wali siswa.');
        }

        return $next($request);
    }
}

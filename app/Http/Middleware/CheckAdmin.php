<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kalo belum ada session admin yang login, usir ke halaman login admin
        if (!session()->has('id_admin')) {
            return redirect()->route('admin.login')->with('error', 'Akses Ditolak! Area khusus Admin PadelZone.');
        }

        return $next($request);
    }
}
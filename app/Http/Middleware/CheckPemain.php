<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPemain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika tidak ada session id_pemain, tendang balik ke halaman login
        if (!session()->has('id_pemain')) {
            return redirect()->route('login')->with('error', 'Eits! Silakan login terlebih dahulu untuk melakukan booking lapangan.');
        }

        return $next($request);
    }
}
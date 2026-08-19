<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemain;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // [BARU] Tampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // [BARU] Proses pendaftaran pemain baru ke database
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:pemain,email',
            'password' => 'required|string|min:6'
        ]);

        // Simpan data ke tabel pemain dengan password yang aman (Bcrypt)
        $pemain = Pemain::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password) // Enkripsi password otomatis
        ]);

        // Setelah sukses daftar, otomatis buatkan session login biar ga repot login lagi
        session([
            'id_pemain' => $pemain->id_pemain,
            'nama_pemain' => $pemain->nama
        ]);

        return redirect()->route('booking.index')->with('success', 'Akun berhasil dibuat! Selamat datang di PadelZone.');
    }

    // Proses verifikasi akun dari database
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cari email pemain di database
        $pemain = Pemain::where('email', $request->email)->first();

        // Cek apakah pemain ada dan password-nya cocok (Hash Bcrypt)
        if ($pemain && Hash::check($request->password, $pemain->password)) {
            // Simpan data login ke session komputer
            session([
                'id_pemain' => $pemain->id_pemain,
                'nama_pemain' => $pemain->nama
            ]);

            return redirect()->route('booking.index')->with('success', 'Selamat Datang kembali, ' . $pemain->nama . '!');
        }

        // Jika gagal, balikin ke halaman login
        return redirect()->back()->with('error', 'Email atau Password salah! Silakan cek kembali.');
    }

    // Proses logout / hapus session
    public function logout()
    {
        session()->flush(); // Hapus semua session login
        return redirect()->route('booking.index')->with('success', 'Berhasil logout.');
    }

    // ================= FITUR OTENTIKASI ADMIN =================

    public function showAdminLogin()
    {
        return view('admin.login');
    }

    public function adminLogin(Request $request)
    {
        $request->validate(['username' => 'required', 'password' => 'required']);
        
        // Cari admin berdasarkan username
        $admin = Admin::where('username', $request->username)->first();

        // Cek kecocokan password dengan enkripsi Bcrypt di database
        if ($admin && Hash::check($request->password, $admin->password)) {
            session(['id_admin' => $admin->id_admin, 'nama_admin' => $admin->nama_admin]);
            return redirect()->route('admin.dashboard')->with('success', 'Selamat bekerja, ' . $admin->nama_admin . '!');
        }
        
        return redirect()->back()->with('error', 'Username atau Password Admin salah!');

        dd([
            'Data Admin di DB' => $admin,
            'Password yang lu ketik' => $request->password,
            'Apakah Password Cocok?' => Hash::check($request->password, $admin->password ?? '')
        ]);
    }

    public function adminLogout()
    {
        session()->forget(['id_admin', 'nama_admin']); // Hanya hapus session admin
        return redirect()->route('admin.login')->with('success', 'Admin berhasil logout.');
    }
}
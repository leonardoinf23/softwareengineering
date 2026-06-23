<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;

// Fitur Beranda (Bisa diakses publik tanpa login)
Route::get('/', [BookingController::class, 'index'])->name('booking.index');

// Fitur Otentikasi Akun Pemain
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Proteksi Keamanan: Wajib Login Terlebih Dahulu
Route::middleware(['pemain.auth'])->group(function () {
    Route::get('/booking/{id}', [BookingController::class, 'showForm'])->name('booking.form');
    Route::post('/booking/submit', [BookingController::class, 'store'])->name('booking.submit');
    Route::get('/riwayat', [BookingController::class, 'history'])->name('booking.history');
    Route::post('/riwayat/bayar/{id}', [BookingController::class, 'uploadPayment'])->name('booking.pay');
});

// Panel Dashboard Manajemen Admin Klub
Route::get('/admin/dashboard', [BookingController::class, 'adminDashboard'])->name('admin.dashboard');
Route::post('/admin/verify/{id}', [BookingController::class, 'verifyBooking'])->name('admin.verify');

// ================= PANEL ADMIN =================

// Rute Login Admin (Tanpa Proteksi)
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.process');
Route::get('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

// Rute Dashboard & Verifikasi (Wajib Login Admin)
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/dashboard', [BookingController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/admin/verify/{id}', [BookingController::class, 'verifyBooking'])->name('admin.verify');
});
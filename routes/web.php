<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;

// ================= FITUR PUBLIK (TANPA LOGIN) =================
Route::get('/', [BookingController::class, 'index'])->name('booking.index');


// ================= OTENTIKASI AKUN PEMAIN =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// ================= OTENTIKASI AKUN ADMIN =================
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.process');
Route::get('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');


// ================= JALUR KHUSUS ROBOT MIDTRANS (WEBHOOK) =================
// Ditaruh di paling luar agar Midtrans bisa nembak kesini tanpa terhalang login pemain/admin
Route::post('/api/midtrans/callback', [BookingController::class, 'midtransCallback']);


// ================= PROTEKSI: WAJIB LOGIN PEMAIN =================
Route::middleware(['pemain.auth'])->group(function () {
    Route::get('/booking/{id}', [BookingController::class, 'showForm'])->name('booking.form');
    Route::post('/booking/submit', [BookingController::class, 'store'])->name('booking.submit');
    Route::get('/riwayat', [BookingController::class, 'history'])->name('booking.history');
    Route::post('/riwayat/bayar/{id}', [BookingController::class, 'uploadPayment'])->name('booking.pay');
});


// ================= PROTEKSI: WAJIB LOGIN ADMIN =================
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/dashboard', [BookingController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/admin/verify/{id}', [BookingController::class, 'verifyBooking'])->name('admin.verify');
});
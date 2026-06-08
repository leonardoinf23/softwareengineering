<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

// ================= FITUR PEMAIN =================
Route::get('/', [BookingController::class, 'index'])->name('booking.index');
Route::get('/booking/{id}', [BookingController::class, 'showForm'])->name('booking.form');
Route::post('/booking/submit', [BookingController::class, 'store'])->name('booking.submit');

// Riwayat Pemesanan & Kirim Bukti Bayar
Route::get('/riwayat', [BookingController::class, 'history'])->name('booking.history');
Route::post('/riwayat/bayar/{id}', [BookingController::class, 'uploadPayment'])->name('booking.pay');

// ================= FITUR ADMIN =================
Route::get('/admin/dashboard', [BookingController::class, 'adminDashboard'])->name('admin.dashboard');
Route::post('/admin/verify/{id}', [BookingController::class, 'verifyBooking'])->name('admin.verify');
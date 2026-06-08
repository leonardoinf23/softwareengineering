<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use App\Models\Booking;
use App\Models\Pembayaran;

class BookingController extends Controller
{
    public function index()
    {
        $lapangans = Lapangan::all();
        return view('booking.index', compact('lapangans'));
    }

    public function showForm($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        return view('booking.form', compact('lapangan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lapangan' => 'required',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'durasi' => 'required|integer|min:1|max:5' // Validasi durasi baru
        ]);

        $jam_mulai = $request->jam_mulai;
        $durasi = $request->durasi;
        
        // Hitung jam selesai berdasarkan input durasi jam dinamis
        $jam_selesai = date('H:i:s', strtotime($jam_mulai . " +{$durasi} hour"));

        // ALGORITMA ANTI BENTROK MULTI-JAM (Overlapping Period Validation)
        $bentrok = Booking::where('id_lapangan', $request->id_lapangan)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['Submitted', 'Verified'])
            ->where('jam_mulai', '<', $jam_selesai)   //-- Jam mulai sewa baru sebelum jam selesai sewa yang ada
            ->where('jam_selesai', '>', $jam_mulai) // -- Jam selesai sewa baru sesudah jam mulai sewa yang ada
            ->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Waduh, slot waktu pada durasi tersebut sudah terisi sebagian/seluruhnya oleh user lain!');
        }

        Booking::create([
            'id_pemain' => 1, 
            'id_lapangan' => $request->id_lapangan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'status' => 'Created'
        ]);

        return redirect()->route('booking.history')->with('success', 'Draft booking berhasil dibuat! Silakan bayar sesuai total durasi.');
    }

    // [BARU] Tampilkan Riwayat Booking Pemain
    public function history()
    {
        $bookings = Booking::where('id_pemain', 1)->with(['lapangan', 'pembayaran'])->orderBy('id_booking', 'DESC')->get();
        return view('booking.history', compact('bookings'));
    }

    // [BARU] Proses Upload Bukti Pembayaran oleh Pemain
    public function uploadPayment(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'metode_pembayaran' => 'required'
        ]);

        $booking = Booking::findOrFail($id);

        // Hitung selisih jam untuk tahu durasi sewa asli
        $jam_awal = strtotime($booking->jam_mulai);
        $jam_akhir = strtotime($booking->jam_selesai);
        $durasi_jam = ($jam_akhir - $jam_awal) / 3600;

        // KALKULASI TOTAL BIAYA: Harga Lapangan x Durasi Jam
        $total_bayar = $booking->lapangan->harga_per_jam * $durasi_jam;

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);

            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar' => $total_bayar, // Menyimpan total kalkulasi durasi
                'bukti_transfer' => $filename,
                'status_pembayaran' => 'Pending'
            ]);

            $booking->update(['status' => 'Submitted']);
        }

        return redirect()->back()->with('success', 'Bukti transaksi berhasil dikirim!');
    }

    // [BARU] Dashboard Utama Admin (Melihat Semua Booking)
    public function adminDashboard()
    {
        $bookings = Booking::with(['lapangan', 'pemain', 'pembayaran'])->orderBy('id_booking', 'DESC')->get();
        return view('admin.dashboard', compact('bookings'));
    }

    // [BARU] Validasi Pembayaran oleh Admin (Mengubah status menjadi sukses)
    public function verifyBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Verified']);

        if ($booking->pembayaran) {
            $booking->pembayaran->update(['status_pembayaran' => 'Success']);
        }

        return redirect()->back()->with('success', 'Booking & Pembayaran berhasil diverifikasi secara valid!');
    }
}
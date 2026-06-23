<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use App\Models\Booking;
use App\Models\Pembayaran;

class BookingController extends Controller
{
    /**
     * Tampilkan Halaman Utama (Daftar Lapangan)
     * Dapat diakses oleh publik tanpa login
     */
    public function index()
    {
        $lapangans = Lapangan::all();
        return view('booking.index', compact('lapangans'));
    }

    /**
     * Tampilkan Form Pilihan Tanggal & Jam Booking
     * Diproteksi Middleware (Wajib Login)
     */
    public function showForm(int $id)
    {
        $lapangan = Lapangan::findOrFail($id);

        // [BARU] Ambil data jadwal yang sudah laku dibooking (mulai hari ini ke depan)
        $jadwalTerisi = Booking::where('id_lapangan', $id)
            ->where('tanggal', '>=', date('Y-m-d'))
            ->whereIn('status', ['Submitted', 'Verified'])
            ->orderBy('tanggal', 'ASC')
            ->orderBy('jam_mulai', 'ASC')
            ->get();

        // Lempar data lapangan dan jadwalTerisi ke view form
        return view('booking.form', compact('lapangan', 'jadwalTerisi'));
    }

    /**
     * Proses Validasi dan Simpan Draft Transaksi Booking
     * Diproteksi Middleware (Wajib Login)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_lapangan' => 'required',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'durasi' => 'required|integer|min:1|max:5' // Mengakomodasi sewa hingga 5 jam
        ]);

        $jam_mulai = $request->jam_mulai;
        $durasi = $request->durasi;
        
        // Menghitung jam selesai berdasarkan durasi jam dinamis secara otomatis
        $jam_selesai = date('H:i:s', strtotime($jam_mulai . " +{$durasi} hour"));

        // ALGORITMA ANTI BENTROK MULTI-JAM (Overlapping Period Validation)
        $bentrok = Booking::where('id_lapangan', $request->id_lapangan)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['Submitted', 'Verified'])
            ->where('jam_mulai', '<', $jam_selesai)   // Jam mulai sewa baru sebelum jam selesai sewa yang ada
            ->where('jam_selesai', '>', $jam_mulai)  // Jam selesai sewa baru sesudah jam mulai sewa yang ada
            ->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Waduh, slot waktu pada durasi tersebut sudah terisi sebagian/seluruhnya oleh user lain!');
        }

        // Simpan data draft booking ke database
        Booking::create([
            'id_pemain' => session('id_pemain'), // Mengambil ID pengguna dari session login secara dinamis
            'id_lapangan' => $request->id_lapangan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'status' => 'Created' // Menggunakan state awal 'Created'
        ]);

        return redirect()->route('booking.history')->with('success', 'Draft booking berhasil dibuat! Silakan bayar sesuai total durasi.');
    }

    /**
     * Tampilkan Riwayat Pemesanan Milik Pemain Terkait
     * Diproteksi Middleware (Wajib Login)
     */
    public function history()
    {
        // Menampilkan riwayat transaksi secara dinamis berdasarkan ID session login pemain
        $bookings = Booking::where('id_pemain', session('id_pemain'))
            ->with(['lapangan', 'pembayaran'])
            ->orderBy('id_booking', 'DESC')
            ->get();

        return view('booking.history', compact('bookings'));
    }

    /**
     * Proses Upload Bukti Pembayaran Fisik oleh Pemain
     * Diproteksi Middleware (Wajib Login)
     */
    public function uploadPayment(Request $request, int $id) // PERBAIKAN: Ditambah int
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'metode_pembayaran' => 'required'
        ]);

        $booking = Booking::findOrFail($id);

        // Menghitung selisih jam untuk mendeteksi durasi sewa asli
        $jam_awal = strtotime($booking->jam_mulai);
        $jam_akhir = strtotime($booking->jam_selesai);
        $durasi_jam = ($jam_akhir - $jam_awal) / 3600;

        // KALKULASI TOTAL BIAYA AKHIR: Harga Per Jam Lapangan x Jumlah Durasi Jam Sewa
        $total_bayar = $booking->lapangan->harga_per_jam * $durasi_jam;

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Pindahkan file bukti transfer fisik ke folder public/uploads komputer lokal
            $file->move(public_path('uploads'), $filename);

            // Daftarkan entri record baru ke tabel pembayaran
            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar' => $total_bayar,
                'bukti_transfer' => $filename,
                'status_pembayaran' => 'Pending'
            ]);

            // Mutasi status state booking menjadi 'Submitted' (Menunggu Tinjauan Admin)
            $booking->update(['status' => 'Submitted']);
        }

        return redirect()->back()->with('success', 'Bukti transaksi berhasil dikirim! Menunggu validasi admin.');
    }

    /**
     * Dashboard Utama Rekap Data Panel Admin
     * Digunakan untuk monitoring seluruh aktivitas booking
     */
    public function adminDashboard()
    {
        $bookings = Booking::with(['lapangan', 'pemain', 'pembayaran'])->orderBy('id_booking', 'DESC')->get();
        return view('admin.dashboard', compact('bookings'));
    }

    /**
     * Validasi Kelayakan Pembayaran Keuangan oleh Admin
     * Mengubah status reservasi menjadi final (Verified & Lunas)
     */
    public function verifyBooking(int $id) // PERBAIKAN: Ditambah int
    {
        $booking = Booking::findOrFail($id);
        
        // Perbarui status booking menjadi Verified
        $booking->update(['status' => 'Verified']);

        // Jika data pembayaran terkait ditemukan, nyatakan statusnya Success
        if ($booking->pembayaran) {
            $booking->pembayaran->update(['status_pembayaran' => 'Success']);
        }

        return redirect()->back()->with('success', 'Booking & Pembayaran berhasil diverifikasi secara valid!');
    }
}
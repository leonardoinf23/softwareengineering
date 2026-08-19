<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use App\Models\Booking;
use App\Models\Pembayaran;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification; // BARU: Dipakai untuk menangkap sinyal otomatis dari Midtrans

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

        // Ambil data jadwal yang sudah laku dibooking (mulai hari ini ke depan)
        $jadwalTerisi = Booking::where('id_lapangan', $id)
            ->where('tanggal', '>=', date('Y-m-d'))
            ->whereIn('status', ['Submitted', 'Verified'])
            ->orderBy('tanggal', 'ASC')
            ->orderBy('jam_mulai', 'ASC')
            ->get();

        return view('booking.form', compact('lapangan', 'jadwalTerisi'));
    }

    /**
     * Proses Validasi, Registrasi ke Midtrans, dan Simpan Draft Transaksi Booking
     * Diproteksi Middleware (Wajib Login)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_lapangan' => 'required',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'durasi' => 'required|integer|min:1|max:5'
        ]);

        $lapangan = Lapangan::findOrFail($request->id_lapangan);
        $jam_mulai = $request->jam_mulai;
        $durasi = $request->durasi;
        
        // Menghitung jam selesai berdasarkan durasi jam dinamis secara otomatis
        $jam_selesai = date('H:i:s', strtotime($jam_mulai . " +{$durasi} hour"));

        // ALGORITMA ANTI BENTROK MULTI-JAM (Overlapping Period Validation)
        $bentrok = Booking::where('id_lapangan', $request->id_lapangan)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['Submitted', 'Verified'])
            ->where('jam_mulai', '<', $jam_selesai)   
            ->where('jam_selesai', '>', $jam_mulai)  
            ->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Waduh, slot waktu pada durasi tersebut sudah terisi sebagian/seluruhnya oleh user lain!');
        }

        // ================= PERUBAHAN INTEGRASI MIDTRANS =================
        
        // 1. Kalkulasi total biaya sewa lapangan
        $total_bayar = $lapangan->harga_per_jam * $durasi;

        // 2. Buat Order ID unik gabungan teks, timestamp, dan ID pemain
        $orderId = 'PADEL-' . time() . '-' . session('id_pemain');

        // 3. Set konfigurasi kredensial Midtrans Sandbox
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        Config::$is3ds = env('MIDTRANS_IS_3DS');

        // 4. Susun payload data parameter sesuai standarisasi Midtrans Snap API
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $total_bayar,
            ],
            'customer_details' => [
                'first_name' => session('nama_pemain'),
                'email' => session('email_pemain') ?? 'pemain@padelzone.com',
            ],
            'item_details' => [
                [
                    'id' => $lapangan->id_lapangan,
                    'price' => (int) $lapangan->harga_per_jam,
                    'quantity' => (int) $durasi,
                    'name' => $lapangan->nama_lapangan
                ]
            ]
        ];

        try {
            // 5. Tembak API Midtrans untuk mendapatkan Snap Token
            $snapToken = Snap::getSnapToken($params);

            // 6. Simpan data reservasi ke tabel booking
            $booking = Booking::create([
                'id_pemain' => session('id_pemain'),
                'id_lapangan' => $request->id_lapangan,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai,
                'status' => 'Created'
            ]);

            // 7. Daftarkan entri rekam pembayaran awal di tabel pembayaran
            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'metode_pembayaran' => 'Midtrans Payment Gateway',
                'jumlah_bayar' => $total_bayar,
                'bukti_transfer' => $orderId, // Order ID disimpan di sini agar mempermudah pelacakan webhook
                'status_pembayaran' => 'Pending'
            ]);

            // 8. Alihkan ke riwayat transaksi sambil melempar snap_token
            return redirect()->route('booking.history')->with([
                'success' => 'Draft booking berhasil dibuat! Selesaikan pembayaran melalui jendela Midtrans.',
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke server Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Riwayat Pemesanan Milik Pemain Terkait
     * Diproteksi Middleware (Wajib Login)
     */
    public function history()
    {
        $bookings = Booking::where('id_pemain', session('id_pemain'))
            ->with(['lapangan', 'pembayaran'])
            ->orderBy('id_booking', 'DESC')
            ->get();

        return view('booking.history', compact('bookings'));
    }

    /**
     * Proses Upload Bukti Pembayaran Fisik oleh Pemain (Metode Manual Cadangan)
     * Diproteksi Middleware (Wajib Login)
     */
    public function uploadPayment(Request $request, int $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'metode_pembayaran' => 'required'
        ]);

        $booking = Booking::findOrFail($id);

        $jam_awal = strtotime($booking->jam_mulai);
        $jam_akhir = strtotime($booking->jam_selesai);
        $durasi_jam = ($jam_akhir - $jam_awal) / 3600;

        $total_bayar = $booking->lapangan->harga_per_jam * $durasi_jam;

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);

            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar' => $total_bayar,
                'bukti_transfer' => $filename,
                'status_pembayaran' => 'Pending'
            ]);

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
     * Validasi Kelayakan Pembayaran Keuangan oleh Admin (Verifikasi Manual)
     * Mengubah status reservasi menjadi final (Verified & Lunas)
     */
    public function verifyBooking(int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Verified']);

        if ($booking->pembayaran) {
            $booking->pembayaran->update(['status_pembayaran' => 'Success']);
        }

        return redirect()->back()->with('success', 'Booking & Pembayaran berhasil diverifikasi secara valid!');
    }

    /**
     * WEBHOOK CALLBACK AUTOMATION (BARU)
     * Menangkap sinyal dari server Midtrans dan memperbarui status database secara otomatis
     */
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Notifikasi bermasalah'], 400);
        }

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;

        // Cari record pembayaran berdasarkan Order ID unik yang tersimpan
        $pembayaran = Pembayaran::where('bukti_transfer', $orderId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Data transaksi tidak ditemukan'], 404);
        }

        $booking = Booking::find($pembayaran->id_booking);

        // Mutasi status relasi tabel secara otomatis berdasarkan respon Midtrans
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $pembayaran->update(['status_pembayaran' => 'Success']);
            $booking->update(['status' => 'Verified']);
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $pembayaran->update(['status_pembayaran' => 'Failed']);
            $booking->update(['status' => 'Canceled']);
        } else if ($transactionStatus == 'pending') {
            $pembayaran->update(['status_pembayaran' => 'Pending']);
        }

        return response()->json(['message' => 'Callback diproses otomatis oleh PadelZone!']);
    }
}
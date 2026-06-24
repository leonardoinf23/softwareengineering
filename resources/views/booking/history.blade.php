<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sewa Lapangan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans">

    <nav class="bg-indigo-900 text-white p-4 shadow-md">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">🎾 PadelZone UPJ</h1>
            <a href="{{ route('booking.index') }}" class="text-sm bg-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-600 transition">&larr; Dashboard</a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold mb-6">Riwayat Booking Anda</h2>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @foreach($bookings as $b)
                <div class="bg-white rounded-xl shadow border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-lg font-bold">{{ $b->lapangan->nama_lapangan }}</h3>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $b->status == 'Verified' ? 'bg-emerald-100 text-emerald-800' : ($b->status == 'Created' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                {{ $b->status == 'Created' ? 'Belum Bayar' : $b->status }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-600">
                            Tanggal: <strong class="text-slate-800">{{ date('d-m-Y', strtotime($b->tanggal)) }}</strong> | 
                            Jam: <strong class="text-slate-800">{{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }} WIB</strong>
                            (@php
                                $durasi = (strtotime($b->jam_selesai) - strtotime($b->jam_mulai)) / 3600;
                                echo $durasi . " Jam";
                            @endphp)
                        </p>
                        <p class="text-sm text-slate-600 mt-1">
                            Total Biaya: <strong class="text-indigo-600">Rp {{ number_format($b->lapangan->harga_per_jam * $durasi, 0, ',', '.') }}</strong>
                        </p>
                    </div>

                    <div class="w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0 flex items-center justify-end">
                        @if($b->status == 'Created')
                            <button onclick="bayarMidtrans('{{ session('snap_token') }}')" 
                                    class="w-full md:w-auto bg-indigo-600 text-white text-xs font-bold py-2.5 px-5 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                💳 Bayar Sekarang via Midtrans
                            </button>
                        @else
                            <div class="text-right">
                                <p class="text-xs text-slate-500 italic">
                                    Status Pembayaran: 
                                    <strong class="{{ ($b->pembayaran->status_pembayaran ?? '') == 'Success' ? 'text-emerald-600' : 'text-amber-600' }} font-bold not-italic">
                                        {{ $b->pembayaran->status_pembayaran ?? 'Pending' }}
                                    </strong>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <script type="text/javascript">
        function bayarMidtrans(token) {
            if(!token || token === '') {
                alert("Token pembayaran tidak ditemukan. Jika pop-up tidak muncul otomatis setelah membuat booking baru, silakan lakukan proses booking ulang.");
                return;
            }
            
            window.snap.pay(token, {
                onSuccess: function(result){
                    alert("Mantap! Pembayaran Berhasil Dikonfirmasi.");
                    window.location.reload();
                },
                onPending: function(result){
                    alert("Sewa diproses! Segera selesaikan transfer tagihan Anda.");
                    window.location.reload();
                },
                onError: function(result){
                    alert("Transaksi gagal ditolak oleh gateway. Silakan coba lagi.");
                }
            });
        }

        // AUTO-TRIGGER TRIGGER: Berjalan otomatis mendeteksi operan token dari redirect store controller
        @if(session('snap_token'))
            window.onload = function() {
                bayarMidtrans("{{ session('snap_token') }}");
            };
        @endif
    </script>

</body>
</html>
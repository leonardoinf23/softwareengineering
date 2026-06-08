<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sewa Lapangan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="space-y-4">
            @foreach($bookings as $b)
                <div class="bg-white rounded-xl shadow border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-lg font-bold">{{ $b->lapangan->nama_lapangan }}</h3>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $b->status == 'Verified' ? 'bg-emerald-100 text-emerald-800' : ($b->status == 'Submitted' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                {{ $b->status }}
                            </span>
                        </div>
                         <p class="text-sm text-slate-600">
                            Tanggal: <strong class="text-slate-800">{{ $b->tanggal }}</strong> | 
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

                    <div class="w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0">
                        @if($b->status == 'Created')
                            <form action="{{ route('booking.pay', $b->id_booking) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                @csrf
                                <select name="metode_pembayaran" required class="w-full bg-slate-50 border rounded p-1.5 text-xs">
                                    <option value="Transfer Bank">Transfer Bank Mandiri</option>
                                    <option value="E-Wallet">OVO / GoPay</option>
                                </select>
                                <input type="file" name="bukti_transfer" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <button type="submit" class="w-full bg-indigo-600 text-white text-xs font-bold py-1.5 rounded hover:bg-indigo-700 transition">Kirim Bukti Bayar</button>
                            </form>
                        @else
                            <p class="text-xs text-slate-500 italic">Status Pembayaran: <strong class="text-slate-700">{{ $b->pembayaran->status_pembayaran ?? 'Pending' }}</strong></p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </main>

</body>
</html>
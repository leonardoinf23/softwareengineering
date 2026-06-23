<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard PadelZone</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 text-slate-100 font-sans">

   <nav class="bg-slate-950 p-4 border-b border-slate-800 shadow-md">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wider text-emerald-400">🛠️ CONTROL PANEL ADMIN</h1>
            
            <div class="flex gap-4 items-center">
                <span class="text-xs bg-slate-800 px-3 py-1.5 rounded border border-slate-700 text-slate-300">
                    👤 {{ session('nama_admin') }}
                </span>
                <a href="{{ route('admin.logout') }}" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded transition shadow">
                    Logout
                </a>
            </div>
        </div>
    </nav>
    
    <main class="max-w-6xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold mb-6 border-b border-slate-800 pb-2">Daftar Pengajuan Reservasi Lapangan</h2>

        @if(session('success'))
            <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-300 p-4 mb-6 rounded shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto bg-slate-950 border border-slate-800 rounded-xl shadow-xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900 text-slate-400 uppercase text-xs tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Pemain</th>
                        <th class="p-4">Lapangan</th>
                        <th class="p-4">Jadwal Main</th>
                        <th class="p-4">Bukti Transaksi</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($bookings as $b)
                        <tr class="hover:bg-slate-900/50 transition">
                            <td class="p-4 font-mono text-slate-500">#{{ $b->id_booking }}</td>
                            <td class="p-4 font-semibold text-slate-200">{{ $b->pemain->nama ?? 'User' }}</td>
                            <td class="p-4 text-slate-300">{{ $b->lapangan->nama_lapangan }}</td>
                            <td class="p-4 text-xs text-slate-400">
                                {{ $b->tanggal }} <br>
                                <span class="text-slate-500">{{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }} WIB</span>
                            </td>
                            <td class="p-4">
                                @if($b->pembayaran && $b->pembayaran->bukti_transfer)
                                    <a href="{{ asset('uploads/' . $b->pembayaran->bukti_transfer) }}" target="_blank" class="text-xs text-indigo-400 hover:underline flex items-center gap-1">
                                        👁️ Lihat Gambar Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-slate-600 italic">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($b->status == 'Submitted')
                                    <form action="{{ route('admin.verify', $b->id_booking) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-3 py-1.5 text-xs rounded transition shadow">
                                            ✓ Verifikasi Sukses
                                        </button>
                                    </form>
                                @elseif($b->status == 'Verified')
                                    <span class="text-xs font-bold text-emerald-400 bg-emerald-950/50 px-2 py-1 rounded border border-emerald-900">Verified & Lunas</span>
                                @else
                                    <span class="text-xs text-slate-500">Menunggu Pembayaran</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
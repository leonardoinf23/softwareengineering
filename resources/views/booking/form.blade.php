<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jadwal Main - PadelZone</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    
    <nav class="bg-gradient-to-r from-indigo-950 to-slate-900 text-white p-4 shadow-lg sticky top-0 z-50 border-b border-indigo-800">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-extrabold tracking-widest flex items-center gap-2">
                <span class="text-2xl">🎾</span> PADELZONE
            </h1>
            <a href="{{ route('booking.index') }}" class="text-sm font-semibold bg-white/10 hover:bg-white/20 backdrop-blur-md px-4 py-2 rounded-full transition duration-300 border border-white/10">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl shadow-sm mb-6 flex items-center gap-3">
                <span class="text-xl">⚠️</span>
                <div>
                    <h4 class="font-bold text-sm">Gagal Booking</h4>
                    <p class="text-xs mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden h-fit">
                
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white px-8 py-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-10 transform translate-x-4 -translate-y-4">
                        <span class="text-9xl">🏟️</span>
                    </div>
                    <span class="inline-block text-[10px] font-bold tracking-widest bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full uppercase mb-3 border border-white/30">
                        Form Reservasi
                    </span>
                    <h2 class="text-3xl font-extrabold">{{ $lapangan->nama_lapangan }}</h2>
                    <p class="text-indigo-100 font-medium mt-2 text-lg flex items-center gap-2">
                        💳 Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} <span class="text-sm font-normal opacity-80">/ Jam</span>
                    </p>
                </div>

                <form action="{{ route('booking.submit') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    <input type="hidden" name="id_lapangan" value="{{ $lapangan->id_lapangan }}">

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <span class="bg-indigo-100 text-indigo-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">1</span>
                            Tentukan Tanggal Bermain
                        </label>
                        <input type="date" name="tanggal" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-medium focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <span class="bg-indigo-100 text-indigo-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">2</span>
                            Pilih Jam Mulai (WIB)
                        </label>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @php
                                $slots = [];
                                for($i = 8; $i <= 23; $i++) {
                                    $jamFormat = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $slots["{$jamFormat}:00:00"] = "{$jamFormat}:00";
                                }
                            @endphp

                            @foreach($slots as $value => $label)
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="jam_mulai" value="{{ $value }}" required class="peer sr-only">
                                    <div class="border-2 border-slate-200 bg-white rounded-xl p-3 text-center transition-all duration-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:shadow-md peer-checked:shadow-indigo-100 group-hover:border-indigo-300">
                                        <span class="text-sm font-bold text-slate-500 peer-checked:text-indigo-700 transition-colors">{{ $label }}</span>
                                    </div>
                                    <div class="absolute -top-2 -right-2 bg-indigo-600 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full opacity-0 peer-checked:opacity-100 transition-opacity shadow-sm border-2 border-white">
                                        ✓
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <span class="bg-indigo-100 text-indigo-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">3</span>
                            Pilih Durasi Bermain
                        </label>
                        <div class="relative">
                            <select name="durasi" required class="w-full appearance-none bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-bold focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer">
                                <option value="1">⏱️ 1 Jam (Sesi Singkat)</option>
                                <option value="2">⏱️ 2 Jam (Rekomendasi)</option>
                                <option value="3">⏱️ 3 Jam (Turnamen Mini)</option>
                                <option value="4">⏱️ 4 Jam (Booking Setengah Hari)</option>
                                <option value="5">⏱️ 5 Jam (Booking Full)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                ▼
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-extrabold py-4 px-6 rounded-xl transition-all duration-300 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-1 text-center tracking-wider text-sm uppercase">
                        Konfirmasi & Lanjut Pembayaran &rarr;
                    </button>
                </form>
            </div>

            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden sticky top-24">
                    
                    <div class="bg-slate-900 text-white px-6 py-5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📅</span>
                            <h3 class="font-bold text-sm uppercase tracking-wider">Jadwal Terisi</h3>
                        </div>
                        <span class="bg-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-500/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Live
                        </span>
                    </div>
                    
                    <div class="p-4 bg-indigo-50/50 border-b border-slate-100 flex items-start gap-3">
                        <span class="text-indigo-500 text-lg">💡</span>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Hindari memilih jam yang bersinggungan dengan jadwal di bawah ini agar bookinganmu berhasil.
                        </p>
                    </div>

                    <div class="p-4 max-h-[500px] overflow-y-auto bg-slate-50/50 custom-scrollbar">
                        @if($jadwalTerisi->isEmpty())
                            <div class="text-center py-10 px-4">
                                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto shadow-sm border border-slate-100 mb-3">
                                    <span class="text-3xl">✨</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Lapangan Masih Kosong!</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Belum ada reservasi masuk. Jadilah yang pertama booking lapangan ini.</p>
                            </div>
                        @else
                            <ul class="space-y-3">
                                @foreach($jadwalTerisi as $jadwal)
                                    <li class="bg-white border border-slate-200 p-4 rounded-2xl flex flex-col shadow-sm hover:shadow-md transition duration-300 group">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-extrabold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                                                {{ date('d M Y', strtotime($jadwal->tanggal)) }}
                                            </span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $jadwal->status == 'Verified' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                                                {{ $jadwal->status == 'Verified' ? '✓ LUNAS' : '⏳ PENDING' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-800">
                                            <span class="text-lg text-slate-400 group-hover:text-indigo-500 transition-colors">⏰</span>
                                            <span class="text-sm font-extrabold">
                                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </main>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</body>
</html>
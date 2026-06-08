<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padel Court Booking System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans">

    <nav class="bg-indigo-900 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wider uppercase flex items-center gap-2">
                🎾 PadelZone UPJ
            </h1>
            <div class="flex gap-4 text-sm font-semibold">
                <a href="#" class="hover:text-indigo-200 transition">Home</a>
                <a href="{{ route('booking.history') }}" class="hover:text-indigo-200 transition">Riwayat Booking</a>
                <a href="#" class="bg-emerald-500 hover:bg-emerald-600 px-3 py-1.5 rounded transition shadow">Login Pemain</a>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-indigo-800 to-slate-900 text-white py-12 px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-3">Sewa Lapangan Padel Sat-Set!</h2>
        <p class="text-indigo-200 max-w-xl mx-auto text-sm md:text-base">
            Pilih slot waktu terbaikmu, kunci lapangannya, dan nikmati permainan padel yang seru tanpa takut jadwal bentrok.
        </p>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <h3 class="text-2xl font-bold mb-6 text-slate-900 border-l-4 border-emerald-500 pl-3">
            Daftar Lapangan Tersedia
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($lapangans as $lapangan)
                <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden hover:shadow-lg transition flex flex-col justify-between">
                    <div class="bg-slate-200 h-40 flex items-center justify-center text-slate-400 font-bold text-lg relative">
                        <span class="absolute top-3 right-3 bg-indigo-100 text-indigo-800 text-xs px-2.5 py-1 rounded-full font-semibold">
                            Ready
                        </span>
                        📷 Image Lapangan Padel
                    </div>
                    
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">
                                {{ $lapangan->nama_lapangan }}
                            </h4>
                            <p class="text-emerald-600 font-bold text-xl mb-4">
                                Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} <span class="text-xs text-slate-500 font-normal">/ Jam</span>
                            </p>
                        </div>

                        <a href="{{ route('booking.form', $lapangan->id_lapangan) }}" class="block text-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-sm">
                            Booking Sekarang
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <footer class="bg-slate-900 text-slate-400 py-6 mt-16 border-t border-slate-800 text-center text-xs">
        <p>&copy; 2026 PadelZone UPJ. All Rights Reserved. Crafted for Software Engineering Project.</p>
    </footer>

</body>
</html>
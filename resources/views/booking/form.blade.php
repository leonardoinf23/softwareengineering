<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jadwal Main Padel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans">

    <nav class="bg-indigo-900 text-white p-4 shadow-md">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wider uppercase">🎾 PadelZone UPJ</h1>
            <a href="{{ route('booking.index') }}" class="text-sm bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded transition">
                &larr; Kembali
            </a>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto px-4 py-10">
        
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="bg-indigo-800 text-white px-6 py-6">
                <span class="text-xs font-bold tracking-widest bg-indigo-900 text-indigo-200 px-2.5 py-1 rounded-full uppercase">Form Reservasi</span>
                <h2 class="text-2xl font-bold mt-2">{{ $lapangan->nama_lapangan }}</h2>
                <p class="text-indigo-200 text-sm mt-1">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} / Jam</p>
            </div>

            <form action="{{ route('booking.submit') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id_lapangan" value="{{ $lapangan->id_lapangan }}">

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">1. Pilih Tanggal Bermain</label>
                    <input type="date" name="tanggal" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">2. Pilih Jam Mulai Main</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $slots = ['15:00:00' => '15:00 WIB', '16:00:00' => '16:00 WIB', '17:00:00' => '17:00 WIB', '18:00:00' => '18:00 WIB', '19:00:00' => '19:00 WIB', '20:00:00' => '20:00 WIB'];
                        @endphp

                        @foreach($slots as $value => $label)
                            <label class="relative border border-slate-200 bg-slate-50 rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 transition group">
                                <input type="radio" name="jam_mulai" value="{{ $value }}" required class="peer sr-only">
                                <span class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600">{{ $label }}</span>
                                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-indigo-600 rounded-xl pointer-events-none transition"></div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">3. Durasi Bermain</label>
                    <select name="durasi" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="1">1 Jam Bermain</option>
                        <option value="2">2 Jam Bermain</option>
                        <option value="3">3 Jam Bermain</option>
                        <option value="4">4 Jam Bermain</option>
                        <option value="5">5 Jam Bermain</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl transition shadow-md hover:shadow-lg text-center tracking-wider">
                    KONFIRMASI BOOKING SEKARANG &rarr;
                </button>
            </form>
        </div>
    </main>

</body>
</html>
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
            <div class="flex gap-4 text-sm font-semibold items-center">
                <a href="{{ route('booking.index') }}" class="hover:text-indigo-200 transition">Home</a>
                <a href="{{ route('booking.history') }}" class="hover:text-indigo-200 transition">Riwayat Booking</a>
                
                @if(session()->has('id_pemain'))
                    <span class="text-emerald-400 font-medium">👋 {{ session('nama_pemain') }}</span>
                    <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded transition text-xs shadow">Logout</a>
                @else
                    <a href="{{ route('login') }}" class="bg-emerald-500 hover:bg-emerald-600 px-3 py-1.5 rounded transition shadow">Login Pemain</a>
                @endif
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-6">
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <header class="bg-gradient-to-r from-indigo-800 to-slate-900 text-white py-12 px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-3">Sewa Lapangan Padel Sat-Set!</h2>
        <p class="text-indigo-200 max-w-xl mx-auto text-sm md:text-base">
            Pilih slot waktu terbaikmu, kunci lapangannya, dan nikmati permainan padel yang seru tanpa takut jadwal bentrok.
        </p>

        <section class="max-w-6xl mx-auto px-4 pt-12 pb-4">
            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">Tiga Langkah Mudah</h3>
                <div class="w-16 h-1 bg-emerald-500 mx-auto mt-2 rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:-translate-y-1 transition duration-300">
                    <div class="text-5xl mb-4">📅</div>
                    <h4 class="font-bold text-slate-900 mb-2">1. Pilih Jadwal</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Pilih lapangan favoritmu, tentukan tanggal, dan amankan slot jam kosong tanpa takut bentrok.</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:-translate-y-1 transition duration-300">
                    <div class="text-5xl mb-4">💳</div>
                    <h4 class="font-bold text-slate-900 mb-2">2. Pembayaran</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Selesaikan pembayaran sesuai durasi sewa, lalu unggah bukti transfer ke sistem.</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:-translate-y-1 transition duration-300">
                    <div class="text-5xl mb-4">🎾</div>
                    <h4 class="font-bold text-slate-900 mb-2">3. Let's Play!</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Tunggu konfirmasi admin, datang ke lapangan tepat waktu, dan selamat berolahraga!</p>
                </div>
            </div>
        </section>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <h3 class="text-2xl font-bold mb-6 text-slate-900 border-l-4 border-emerald-500 pl-3">
            Daftar Lapangan Tersedia
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
           @foreach($lapangans as $lapangan)
                <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden hover:shadow-lg transition flex flex-col justify-between group">
                    
                    <div onclick="openModal('{{ $lapangan->id_lapangan }}', '{{ $lapangan->nama_lapangan }}', '{{ $lapangan->harga_per_jam }}')" class="bg-gradient-to-br from-indigo-500 to-purple-600 h-44 flex flex-col items-center justify-center text-white font-bold text-lg relative cursor-pointer">
                        <span class="absolute top-3 right-3 bg-emerald-500 text-white text-xs px-2.5 py-1 rounded-full font-semibold shadow">Lihat Fasilitas</span>
                        <span class="text-4xl mb-2 group-hover:scale-110 transition duration-300">🏟️</span>
                        <span class="text-sm tracking-wide opacity-90">Klik untuk Detail</span>
                    </div>
                    
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-3">
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">{{ $lapangan->nama_lapangan }}</h4>
                            <p class="text-emerald-600 font-bold text-xl">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} <span class="text-xs text-slate-500 font-normal">/ Jam</span></p>
                        </div>
                        
                        <div class="space-y-2">
                            <button onclick="openModal('{{ $lapangan->id_lapangan }}', '{{ $lapangan->nama_lapangan }}', '{{ $lapangan->harga_per_jam }}')" class="w-full bg-slate-100 hover:bg-indigo-50 text-indigo-600 border border-indigo-100 font-semibold py-2 rounded-xl text-sm transition">
                                🔎 Detail & Fasilitas Lapangan
                            </button>
                            
                            <a href="{{ route('booking.form', $lapangan->id_lapangan) }}" class="block text-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-xl text-sm transition shadow-sm">
                                🎾 Booking Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <div id="facilityModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-100 transform scale-95 transition-transform duration-300">
            
            <div class="bg-indigo-900 text-white p-6 relative">
                <button onclick="closeModal()" class="absolute top-4 right-4 text-white/70 hover:text-white text-xl font-bold">&times;</button>
                <span class="text-xs bg-indigo-800 text-emerald-400 font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Spesifikasi Lapangan</span>
                <h3 id="modalTitle" class="text-2xl font-bold mt-2">Nama Lapangan</h3>
                <p id="modalPrice" class="text-indigo-200 text-sm mt-1">Harga</p>
            </div>

            <div class="p-6 space-y-4">
                <h4 class="font-bold text-slate-800 text-sm tracking-wide uppercase">✨ Fasilitas Premium Include:</h4>
                
                <div id="modalFacilities" class="grid grid-cols-1 gap-2.5 text-sm text-slate-600">
                    </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 flex gap-2 mt-4">
                    <span>💡</span>
                    <p>Sewa sudah termasuk peminjaman raket standar klub dan 3 buah bola padel baru per sesi.</p>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex gap-3 border-t border-slate-100">
                <button onclick="closeModal()" class="w-1/3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2.5 rounded-xl transition text-sm">
                    Tutup
                </button>
                <a id="modalBookingBtn" href="#" class="w-2/3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-center py-2.5 rounded-xl transition text-sm shadow-md block">
                    Booking Lapangan &rarr;
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-slate-900 text-slate-400 py-6 mt-16 text-center text-xs">
        <p>&copy; 2026 PadelZone UPJ. All Rights Reserved. Crafted for Software Engineering Project.</p>
    </footer>

    <script>
        // Data fasilitas berdasarkan tipe lapangan
        const fasilitasData = {
            'indoor': [
                '🏢 Lapangan Indoor Anti Hujan & Panas',
                '🌿 Karpet Turf Lapangan Standar World Padel Tour (WPT)',
                '💡 Lampu LED Stadion High-Lumen (Anti Silau)',
                '❄️ Kipas Exhaust Industri Sekelas Hangar (Sirkulasi Sejuk)',
                '🚿 Dekat Akses Kamar Mandi Bilas AC'
            ],
            'outdoor': [
                '☀️ Lapangan Outdoor View Pemandangan Segar',
                '🌿 Karpet Turf Monofilament Premium',
                '💡 Lampu Sorot Malam Hari Komersial',
                '🥤 Dekat dengan Tenant Kantin & Resto Klub',
                '🚗 Akses Parkir Mobil Langsung di Samping Lapangan'
            ]
        };

        function openModal(id, name, price) {
            const modal = document.getElementById('facilityModal');
            const modalContent = modal.querySelector('.transform');
            
            // Set teks nama dan harga
            document.getElementById('modalTitle').innerText = name;
            document.getElementById('modalPrice').innerText = 'Rp ' + parseInt(price).toLocaleString('id-ID') + ' / Jam';
            
            // Set link action tombol booking
            document.getElementById('modalBookingBtn').href = '/booking/' + id;

            // Render list fasilitas baru
            const tipe = name.toLowerCase().includes('indoor') ? 'indoor' : 'outdoor';
            const listContainer = document.getElementById('modalFacilities');
            listContainer.innerHTML = ''; 

            fasilitasData[tipe].forEach(item => {
                let div = document.createElement('div');
                div.className = 'flex items-center gap-2 bg-slate-50 p-2 rounded-lg border border-slate-100 font-medium text-slate-700';
                div.innerHTML = item;
                listContainer.appendChild(div);
            });

            // Animasi memunculkan modal
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }
        
        function closeModal() {
            const modal = document.getElementById('facilityModal');
            const modalContent = modal.querySelector('.transform');
            
            // Animasi menghilangkan modal
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pemain - PadelZone</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center px-4 font-sans">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="bg-indigo-900 text-white p-6 text-center">
            <h2 class="text-2xl font-bold">🎾 PadelZone UPJ</h2>
            <p class="text-indigo-200 text-xs mt-1">Buat akun baru untuk mulai sewa lapangan padel</p>
        </div>

        <form action="{{ route('register.process') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-100 text-red-700 text-xs p-3 rounded border border-red-200">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Masukkan nama lengkap" required value="{{ old('nama') }}"
                       class="w-full bg-slate-50 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Alamat Email</label>
                <input type="email" name="email" placeholder="contoh@email.com" required value="{{ old('email') }}"
                       class="w-full bg-slate-50 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password (Min. 6 Karakter)</label>
                <input type="password" name="password" placeholder="••••••••" required
                       class="w-full bg-slate-50 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-lg text-sm tracking-wider transition shadow">
                DAFTAR AKUN BARU
            </button>
            
            <p class="text-center text-xs text-slate-500 pt-2">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Login di sini</a>
            </p>
        </form>
    </div>

</body>
</html>
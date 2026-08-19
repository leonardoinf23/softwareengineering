<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pemain - PadelZone</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center px-4 font-sans">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="bg-indigo-900 text-white p-6 text-center">
            <h2 class="text-2xl font-bold">🎾 PadelZone UPJ</h2>
            <p class="text-indigo-200 text-xs mt-1">Silakan masuk untuk melanjutkan reservasi lapangan</p>
        </div>

        <form action="{{ route('login.process') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            @if(session('error'))
                <div class="bg-red-100 text-red-700 text-sm p-3 rounded border border-red-200">{{ session('error') }}</div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Alamat Email</label>
                <input type="email" name="email" placeholder="daffa@example.com" required class="w-full bg-slate-50 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-50 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg text-sm tracking-wider transition shadow">
                MASUK SEKARANG
            </button>
            
            <a href="{{ route('booking.index') }}" class="block text-center text-xs text-slate-500 hover:underline pt-2">&larr; Kembali ke Beranda</a>

            <p class="text-center text-xs text-slate-500 pt-2">
                Belum punya akun pemain? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Daftar sekarang</a>
            </p>
        </form>
    </div>

</body>
</html>
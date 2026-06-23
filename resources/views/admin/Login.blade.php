<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PadelZone</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 h-screen flex items-center justify-center px-4 font-sans">

    <div class="max-w-md w-full bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 overflow-hidden">
        <div class="bg-slate-950 text-white p-6 text-center border-b border-slate-700">
            <h2 class="text-2xl font-bold text-emerald-500">🛠️ ADMIN PORTAL</h2>
            <p class="text-slate-400 text-xs mt-1">Sistem Manajemen PadelZone UPJ</p>
        </div>

        <form action="{{ route('admin.login.process') }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            @if(session('error'))
                <div class="bg-red-500/10 text-red-400 text-sm p-3 rounded border border-red-500/50">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="bg-emerald-500/10 text-emerald-400 text-sm p-3 rounded border border-emerald-500/50">{{ session('success') }}</div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Username</label>
                <input type="text" name="username" placeholder="Masukkan username admin" required class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl text-sm tracking-widest uppercase transition shadow-md mt-2">
                Masuk Dashboard &rarr;
            </button>
        </form>
    </div>

</body>
</html>
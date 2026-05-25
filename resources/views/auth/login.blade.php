<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-slate-100">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900">ERP <span class="text-blue-600">System</span></h1>
        <p class="text-slate-500 mt-2">Silakan masuk ke akun  Anda</p>
    </div>

    <form action="/login" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
            <input type="email" name="email" id="email" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                   placeholder="nama@email.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi</label>
            <input type="password" name="password" id="password" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                   placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-slate-600">
                <input type="checkbox" class="mr-2 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Ingat saya
            </label>
            <a href="#" class="text-sm font-medium text-blue-600 hover:underline">Lupa sandi?</a>
        </div>

        <button type="submit"
                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-slate-200 transition transform hover:-translate-y-0.5">
            Masuk Sekarang
        </button>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-100 text-center text-sm text-slate-500">
        &copy; 2026 ERP. Hak Cipta Dilindungi.
    </div>
</div>

</body>
</html><?php

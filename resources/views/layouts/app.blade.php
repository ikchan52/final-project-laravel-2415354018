<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ERP System - Microcredential Laravel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="layoutHandler()">

<div class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col transition-all duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <div class="p-6 text-xl font-bold border-b border-slate-800">
            ERP <span class="text-blue-400">System</span>
        </div>

        <nav class="mt-6 flex-1 px-4 space-y-2">
            <a href="/dashboard" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                <span class="ml-3">Dashboard</span>
            </a>

            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'staff')
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2 ml-3">Management</div>

                <a href="/customers" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                    <span class="ml-3">Data Pelanggan</span>
                </a>

                <a href="/services" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                    <span class="ml-3">Layanan Internet</span>
                </a>

                <a href="/subscriptions" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                    <span class="ml-3">Data Langganan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'customer')
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2 ml-3">Portal</div>
                <a href="/my-subscription" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                    <span class="ml-3">Langganan Saya</span>
                </a>
            @endif
        </nav>
        <button @click="logout()" class="text-rose-600 font-black uppercase tracking-widest text-xs hover:text-rose-800 transition-colors">
            Logout
        </button>

        <script>
            function logout() {
                if (!confirm('Yakin ingin keluar dari sistem?')) return;

                // Arahkan ke endpoint /api/logout dengan method POST
                fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // Jangan lupa sertakan CSRF Token agar tidak error 419
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            // Setelah logout berhasil di server, hapus session di client dan balik ke login
                            window.location.href = '/login';
                        }
                    })
                    .catch(error => console.error('Logout failed:', error));
            }
        </script>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white border-b h-16 flex items-center justify-between px-8">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-600">
                Menu
            </button>

            <div class="text-sm text-gray-500">
                Selamat datang, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function layoutHandler() {
        return {
            sidebarOpen: true,
            init() {
                // Ritual CSRF Cookie
                fetch('/sanctum/csrf-cookie').then(() => {
                    console.log('Sanctum: CSRF Cookie Securely Fetched.');
                });
            }
        }
    }
</script>
</body>
</html>

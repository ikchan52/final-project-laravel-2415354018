@extends('layouts.app')

@section('content')
    <div x-data="dashboardHandler()" x-init="fetchStats()" class="w-full">

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-500">Ringkasan Sistem</h1>
            <p class="text-slate-500 mt-1 font-medium italic">Pantau performa operasional ERP secara real-time.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Pelanggan</div>
                <div class="text-4xl font-black text-slate-900 mt-1" x-text="stats.total_customers || 0">0</div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Langganan Aktif</div>
                <div class="text-4xl font-black text-emerald-600 mt-1" x-text="stats.active_subscriptions || 0">0</div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Layanan</div>
                <div class="text-4xl font-black text-slate-900 mt-1" x-text="stats.total_services || 0">0</div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status Sistem</div>
                <div class="text-2xl font-black text-emerald-500 mt-2 uppercase">Online</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Langganan Terbaru</h2>
                <a href="/subscriptions" class="text-xs font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-slate-50/50 text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Pelanggan</th>
                        <th class="px-8 py-5">Layanan</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Tanggal Berakhir</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <template x-if="loading">
                        <tr><td colspan="4" class="px-8 py-20 text-center text-slate-400 font-bold animate-pulse uppercase text-xs tracking-widest">Sinkronisasi Data Dashboard...</td></tr>
                    </template>

                    <template x-if="!loading && recent_subscriptions.length > 0">
                        <template x-for="sub in recent_subscriptions" :key="sub.id">
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="font-black text-slate-900" x-text="sub.customer_name"></div>
                                    <div class="text-[10px] font-bold text-blue-600 uppercase tracking-tighter" x-text="sub.customer_uid"></div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-700" x-text="sub.service_name"></div>
                                    <div class="text-[10px] font-medium text-slate-400" x-text="sub.price_formatted"></div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                              :class="{
                                                'bg-emerald-100 text-emerald-700': sub.status === 'active',
                                                'bg-amber-100 text-amber-700': sub.status === 'trial',
                                                'bg-rose-100 text-rose-700': sub.status === 'isolir' || sub.status === 'expired'
                                            }">
                                            <span class="w-1 h-1 rounded-full mr-1.5" :class="sub.status === 'active' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                            <span x-text="sub.status"></span>
                                        </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="text-slate-900 font-black" x-text="sub.end_date"></div>
                                    <div class="text-[10px] text-slate-400 uppercase font-bold">Jatuh Tempo</div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template x-if="!loading && recent_subscriptions.length === 0">
                        <tr><td colspan="4" class="px-8 py-20 text-center text-slate-400 font-medium">Belum ada aktivitas langganan saat ini.</td></tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function dashboardHandler() {
            return {
                stats: { total_customers: 0, active_subscriptions: 0, total_services: 0 },
                recent_subscriptions: [],
                loading: false,

                async fetchStats() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/dashboard');
                        const result = await response.json();

                        if (response.ok) {
                            // Perbaikan: Mapping langsung ke result (karena di Controller tidak pakai wrapping 'data')
                            this.stats = result.stats;
                            this.recent_subscriptions = result.recent_subscriptions;
                        }
                    } catch (error) {
                        console.error("Dashboard Load Error:", error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endsection

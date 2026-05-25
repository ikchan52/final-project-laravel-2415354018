@extends('layouts.app')

@section('content')
    <div x-data="subscriptionHandler()" x-init="initData()" class="w-full">

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Data Langganan</h1>
                    <p class="text-slate-500 mt-1 font-medium">Monitoring status dan masa aktif layanan pelanggan.</p>
                </div>
                <button @click="openModal('create')" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl shadow-xl hover:bg-blue-700 transition-all active:scale-95">
                    + Tambah Langganan
                </button>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Customer ID</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Pelanggan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Layanan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Mulai</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Berakhir</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <template x-if="loading">
                        <tr><td colspan="7" class="px-8 py-20 text-center text-slate-400 font-bold animate-pulse">Menyelaraskan data langganan...</td></tr>
                    </template>

                    <template x-if="!loading && subscriptions.length > 0">
                        <template x-for="sub in subscriptions" :key="sub.id">
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-blue-600 tracking-tighter" x-text="sub.customer_uid"></div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-900" x-text="sub.customer_name"></div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-700" x-text="sub.service_name"></div>
                                    <div class="text-[10px] text-slate-400 font-bold" x-text="sub.price_formatted"></div>
                                </td>
                                <td class="px-8 py-6 text-center text-xs font-medium text-slate-500" x-text="sub.start_date"></td>
                                <td class="px-8 py-6 text-center">
                                    <div class="text-sm font-bold text-slate-900" x-text="sub.end_date"></div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                          :class="{
                                              'bg-emerald-100 text-emerald-700': sub.status === 'active',
                                              'bg-rose-100 text-rose-700': sub.status === 'expired' || sub.status === 'isolir',
                                              'bg-amber-100 text-amber-700': sub.status === 'trial'
                                          }">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2"
                                              :class="{
                                                  'bg-emerald-500': sub.status === 'active',
                                                  'bg-rose-500': sub.status === 'expired' || sub.status === 'isolir',
                                                  'bg-amber-500': sub.status === 'trial'
                                              }"></span>
                                        <span x-text="sub.status"></span>
                                    </span>
                                <td class="px-8 py-6 text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-4">
                                        <template x-if="sub.status !== 'active'">
                                            <button @click="updateStatus(sub.id, 'active')"
                                                    class="text-emerald-600 hover:text-emerald-800 font-black text-[10px] uppercase tracking-[0.1em] transition-colors">
                                                Aktifkan
                                            </button>
                                        </template>

                                        <template x-if="sub.status === 'active'">
                                            <button @click="updateStatus(sub.id, 'isolir')"
                                                    class="text-amber-600 hover:text-amber-800 font-black text-[10px] uppercase tracking-[0.1em] transition-colors">
                                                Isolir
                                            </button>
                                        </template>

                                        <span class="text-slate-200">|</span>

                                        <button @click="updateStatus(sub.id, 'dismantle')"
                                                class="text-slate-400 hover:text-rose-600 font-black text-[10px] uppercase tracking-[0.1em] transition-colors">
                                            Dismantle
                                        </button>

                                        <button @click="deleteSubscription(sub.id)"
                                                class="text-rose-600 hover:text-rose-800 font-black text-[10px] uppercase tracking-[0.1em] transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template x-if="!loading && subscriptions.length === 0">
                        <tr><td colspan="7" class="px-8 py-20 text-center text-slate-400 font-medium">Belum ada data langganan yang aktif.</td></tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="closeModal()"></div>
            <div class="relative bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10" @click.stop>
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">Aktivasi Langganan</h3>
                    <p class="text-slate-400 text-sm">Pilih pelanggan dan paket layanan untuk memulai.</p>
                </div>

                <form @submit.prevent="saveSubscription" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Pelanggan</label>
                        <select x-model="form.customer_id" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-4 focus:ring-blue-100 outline-none appearance-none">
                            <option value="">-- Cari Pelanggan --</option>
                            <template x-for="user in customers" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Layanan Internet</label>
                        <select x-model="form.service_id" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-4 focus:ring-blue-100 outline-none">
                            <option value="">-- Pilih Paket --</option>
                            <template x-for="service in services" :key="service.id">
                                <option :value="service.id" x-text="service.name + ' (' + (service.price_formatted || 'Rp ' + service.price) + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                            <input type="date" x-model="form.start_date" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-4 focus:ring-blue-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Masa Berlaku</label>
                            <input type="date" x-model="form.end_date" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-4 focus:ring-blue-100 outline-none">
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="closeModal()" class="flex-1 py-4 font-bold text-slate-400 hover:text-slate-600 transition-colors">Batal</button>
                        <button type="submit" :disabled="loading" class="flex-[2] py-4 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:bg-slate-800 transition-all uppercase tracking-widest text-sm flex items-center justify-center">
                            <span x-show="loading" class="animate-spin mr-2">...</span>
                            <span x-text="loading ? 'Memproses...' : 'Aktifkan Sekarang'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function subscriptionHandler() {
            return {
                subscriptions: [],
                customers: [],
                services: [],
                loading: false,
                modalOpen: false,
                form: {
                    customer_id: '', // Diperbaiki dari user_id ke customer_id
                    service_id: '',
                    start_date: '',
                    end_date: '',
                    status: 'active'
                },

                async initData() {
                    this.loading = true;
                    try {
                        await Promise.all([
                            this.fetchSubscriptions(),
                            this.fetchCustomers(),
                            this.fetchServices()
                        ]);
                    } catch (e) {
                        console.error("Gagal inisialisasi data:", e);
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchSubscriptions() {
                    const r = await fetch('/api/subscriptions');
                    const res = await r.json();
                    if (r.ok) this.subscriptions = res.data;
                },

                async fetchCustomers() {
                    const r = await fetch('/api/customers');
                    const res = await r.json();
                    if (r.ok) this.customers = res.data;
                },

                async fetchServices() {
                    const r = await fetch('/api/services');
                    const res = await r.json();
                    if (r.ok) this.services = res.data;
                },

                openModal() {
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = 'auto';
                },

                async saveSubscription() {
                    this.loading = true;
                    try {
                        const r = await fetch('/api/subscriptions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await r.json();

                        if (r.ok) {
                            this.closeModal();
                            await this.fetchSubscriptions();
                            alert("Langganan berhasil diaktifkan!");
                        } else {
                            const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                            alert("Gagal menyimpan:\n" + errorMsg);
                        }
                    } catch (e) {
                        console.error("Koneksi Error:", e);
                        alert("Terjadi kesalahan koneksi ke server.");
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteSubscription(id) {
                    if (!confirm('Apakah Anda yakin ingin memutus langganan ini?')) return;
                    try {
                        const r = await fetch(`/api/subscriptions/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                        });
                        if (r.ok) this.fetchSubscriptions();
                    } catch (e) {
                        console.error("Gagal menghapus:", e);
                    }
                },

                async updateStatus(id, newStatus) {
                    let confirmMsg = `Yakin ingin mengubah status ke ${newStatus}?`;
                    if (newStatus === 'dismantle') confirmMsg = "PERINGATAN: Dismantle akan memutus layanan secara permanen. Lanjutkan?";

                    if (!confirm(confirmMsg)) return;

                    try {
                        const r = await fetch(`/api/subscriptions/${id}`, {
                            method: 'PUT', // Kita gunakan method PUT sesuai SubscriptionController
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ status: newStatus })
                        });

                        if (r.ok) {
                            await this.fetchSubscriptions();
                            alert(`Status berhasil diubah menjadi ${newStatus}`);
                        } else {
                            alert("Gagal memperbarui status.");
                        }
                    } catch (e) {
                        console.error("Error update status:", e);
                    }
                }
            }
        }
    </script>
@endsection

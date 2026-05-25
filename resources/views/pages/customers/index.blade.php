@extends('layouts.app')

@section('content')
    <div x-data="customerHandler()" x-init="fetchCustomers()" class="w-full">

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Data Pelanggan</h1>
                    <p class="text-slate-500 mt-1 font-medium">Manajemen pelanggan.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="relative w-full sm:w-80">
                        <input type="text" x-model="search" @input.debounce.500ms="fetchCustomers()"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                               placeholder="Cari pelanggan...">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <button @click="openModal('create')"
                            class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-100 transition-all active:scale-95">
                        + Tambah Pelanggan
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Customer ID</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Pelanggan</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Alamat Pelanggan</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Telepon</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                    <template x-if="loading">
                        <tr><td colspan="8" class="px-6 py-20 text-center text-slate-400 font-bold animate-pulse">Menyelaraskan data pelanggan...</td></tr>
                    </template>

                    <template x-if="!loading && customers.length > 0">
                        <template x-for="customer in customers" :key="customer.id">
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-6 text-slate-400 font-medium" x-text="customer.id"></td>
                                <td class="px-6 py-6 font-bold text-blue-600 tracking-tight" x-text="customer.customer_id"></td>
                                <td class="px-6 py-6 font-bold text-slate-900" x-text="customer.name"></td>
                                <td class="px-6 py-6 text-slate-500 text-xs" x-text="customer.address || '-'"></td>
                                <td class="px-6 py-6 text-slate-600 font-medium" x-text="customer.email"></td>
                                <td class="px-6 py-6 text-slate-600 font-medium" x-text="customer.phone"></td>
                                <td class="px-6 py-6 text-center">
                                    <template x-if="customer.status">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                              :class="customer.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2" :class="customer.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                            <span x-text="customer.status"></span>
                                        </span>
                                    </template>
                                    <template x-if="!customer.status">
                                        <span class="text-slate-300 italic text-xs">No Status</span>
                                    </template>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <button @click="openModal('edit', customer)" class="text-blue-600 hover:text-blue-800 font-bold">Edit</button>
                                        <button @click="deleteCustomer(customer.id)" class="text-rose-600 hover:text-rose-800 font-bold">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template x-if="!loading && customers.length === 0">
                        <tr><td colspan="8" class="px-6 py-20 text-center text-slate-400">Belum ada data pelanggan yang terdaftar.</td></tr>
                    </template>
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Halaman <span class="text-slate-900" x-text="page"></span></div>
                <div class="flex gap-2">
                    <button @click="prevPage()" :disabled="page === 1" class="px-5 py-2 text-xs font-black rounded-xl border bg-white shadow-sm disabled:opacity-30">Sebelumnya</button>
                    <button @click="nextPage()" :disabled="!hasMore" class="px-5 py-2 text-xs font-black rounded-xl border bg-white shadow-sm disabled:opacity-30">Berikutnya</button>
                </div>
            </div>
        </div>

        <div x-show="modalOpen"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
             x-cloak>

            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <div class="relative bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all"
                 @click.stop>
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight" x-text="modalMode === 'create' ? 'Pelanggan Baru' : 'Edit Data Pelanggan'"></h3>
                    <button @click="closeModal()" class="text-slate-300 hover:text-slate-900 text-3xl font-light">&times;</button>
                </div>

                <form @submit.prevent="saveCustomer" class="p-10 space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Nama Lengkap</label>
                            <input type="text" x-model="form.name" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Email</label>
                                <input type="email" x-model="form.email" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Telepon</label>
                                <input type="text" x-model="form.phone" required class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Alamat Lengkap</label>
                            <textarea x-model="form.address" rows="3" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all"></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Status Akun</label>
                            <select x-model="form.status" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                                <option value="active">Active (Aktif)</option>
                                <option value="inactive">Inactive (Non-aktif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="closeModal()" class="flex-1 py-4 font-bold text-slate-400 hover:text-slate-600 transition-colors">Batal</button>
                        <button type="submit" :disabled="loading" class="flex-[2] py-4 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 uppercase tracking-widest text-sm flex items-center justify-center">
                            <span x-show="loading" class="animate-spin mr-2">...</span>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Data'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function customerHandler() {
            return {
                customers: [], search: '', page: 1, hasMore: false, loading: false,
                modalOpen: false, modalMode: 'create',
                form: { id: null, name: '', email: '', phone: '', address: '', status: 'active' },

                async fetchCustomers() {
                    this.loading = true;
                    try {
                        const r = await fetch(`/api/customers?search=${this.search}&page=${this.page}`);
                        const res = await r.json();
                        if (r.ok) { this.customers = res.data; this.hasMore = res.links.next !== null; }
                    } catch (e) { console.error("Error Fetching:", e); } finally { this.loading = false; }
                },

                openModal(mode, data = null) {
                    this.modalMode = mode;
                    this.form = mode === 'edit' ? { ...data } : { id: null, name: '', email: '', phone: '', address: '', status: 'active' };
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = 'auto';
                },

                async saveCustomer() {
                    this.loading = true;
                    const url = this.modalMode === 'create' ? '/api/customers' : `/api/customers/${this.form.id}`;
                    const method = this.modalMode === 'create' ? 'POST' : 'PUT';

                    try {
                        const r = await fetch(url, {
                            method,
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
                            await this.fetchCustomers();
                            alert(this.modalMode === 'create' ? "Pelanggan berhasil ditambahkan!" : "Data pelanggan berhasil diperbarui!");
                        } else {
                            const errMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                            alert("Gagal menyimpan:\n" + errMsg);
                        }
                    } catch (e) {
                        console.error("Save Error:", e);
                        alert("Terjadi kesalahan koneksi ke server.");
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteCustomer(id) {
                    if (!confirm('Hapus pelanggan ini?')) return;
                    try {
                        const r = await fetch(`/api/customers/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                        });
                        if (r.ok) this.fetchCustomers();
                    } catch (e) { console.error("Delete Error:", e); }
                },

                nextPage() { if (this.hasMore) { this.page++; this.fetchCustomers(); } },
                prevPage() { if (this.page > 1) { this.page--; this.fetchCustomers(); } }
            }
        }
    </script>
@endsection

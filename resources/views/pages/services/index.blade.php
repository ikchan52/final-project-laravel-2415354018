@extends('layouts.app')

@section('content')
    <div x-data="serviceHandler()" x-init="fetchServices()" class="w-full">

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Katalog Layanan</h1>
                    <p class="text-slate-500 mt-1 font-medium">Manajemen Layanan.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="relative w-full sm:w-80">
                        <input type="text" x-model="search" @input.debounce.500ms="fetchServices()"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                               placeholder="Cari nama layanan...">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <button @click="openModal('create')"
                            class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-100 transition-all active:scale-95 whitespace-nowrap">
                        + Tambah Layanan
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Layanan</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Harga</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Deskripsi</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <template x-if="loading">
                        <tr><td colspan="5" class="px-8 py-20 text-center text-slate-400 font-bold animate-pulse">Menyelaraskan Katalog...</td></tr>
                    </template>

                    <template x-if="!loading && services.length > 0">
                        <template x-for="service in services" :key="service.id">
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-900 text-base" x-text="service.name"></div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter" x-text="'SKU-' + service.id"></div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="font-black text-blue-600 text-base" x-text="service.price_formatted"></div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-slate-500 text-xs italic max-w-xs truncate" :title="service.description" x-text="service.description || '-'"></div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter"
                                          :class="service.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                          x-text="service.status"></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <button @click="openModal('edit', service)" class="text-blue-600 hover:text-blue-800 font-bold">Edit</button>
                                        <button @click="deleteService(service.id)" class="text-rose-600 hover:text-rose-800 font-bold">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template x-if="!loading && services.length === 0">
                        <tr><td colspan="5" class="px-8 py-20 text-center text-slate-400 font-medium">Layanan tidak ditemukan.</td></tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" x-cloak>
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <div class="relative bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all" @click.stop>
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight" x-text="modalMode === 'create' ? 'Layanan Baru' : 'Edit Layanan'"></h3>
                    <button @click="closeModal()" class="text-slate-300 hover:text-slate-900 text-3xl font-light">&times;</button>
                </div>

                <form @submit.prevent="saveService" class="p-10 space-y-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Nama Layanan</label>
                            <input type="text" x-model="form.name" required
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Harga Bulanan (Rp)</label>
                            <input type="number" x-model="form.price" required
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Deskripsi Layanan</label>
                            <textarea x-model="form.description" rows="3"
                                      class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all"
                                      placeholder="Detail paket..."></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Status</label>
                            <select x-model="form.status" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 outline-none transition-all">
                                <option value="active">Active (Aktif)</option>
                                <option value="inactive">Inactive (Non-aktif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="closeModal()" class="flex-1 py-4 font-bold text-slate-400 hover:text-slate-600 transition-colors">Batal</button>
                        <button type="submit" :disabled="loading" class="flex-[2] py-4 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:bg-slate-800 transition-all active:scale-95 uppercase tracking-widest text-sm flex items-center justify-center">
                            <span x-show="loading" class="animate-spin mr-2">...</span>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Paket'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function serviceHandler() {
            return {
                services: [],
                search: '',
                loading: false,
                modalOpen: false,
                modalMode: 'create',
                form: { id: null, name: '', description: '', price: '', status: 'active' },

                async fetchServices() {
                    this.loading = true;
                    try {
                        const r = await fetch(`/api/services?search=${this.search}`);
                        const res = await r.json();
                        if (r.ok) this.services = res.data;
                    } catch (e) {
                        console.error("Gagal mengambil data:", e);
                    } finally {
                        this.loading = false;
                    }
                },

                openModal(mode, data = null) {
                    this.modalMode = mode;
                    this.form = mode === 'edit' ? { ...data } : { id: null, name: '', description: '', price: '', status: 'active' };
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = 'auto';
                },

                async saveService() {
                    this.loading = true;
                    const url = this.modalMode === 'create' ? '/api/services' : `/api/services/${this.form.id}`;
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
                            await this.fetchServices();
                            alert(this.modalMode === 'create' ? "Paket baru berhasil ditambah!" : "Paket berhasil diperbarui!");
                        } else {
                            // Error handling agar tidak 'brick'
                            const errMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                            alert("Gagal menyimpan:\n" + errMsg);
                        }
                    } catch (e) {
                        console.error("Koneksi error:", e);
                        alert("Terjadi kesalahan koneksi ke server.");
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteService(id) {
                    if (!confirm('Hapus paket ini dari katalog?')) return;
                    try {
                        const r = await fetch(`/api/services/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                        });
                        if (r.ok) this.fetchServices();
                    } catch (e) {
                        console.error("Gagal menghapus:", e);
                    }
                }
            }
        }
    </script>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">Kelola Menu & Stok</h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '', showAddModal: false, showEditModal: false, editForm: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm transition">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <input x-model="search" type="text" placeholder="Cari nama menu..." class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-1/3 text-sm">
                
                <button @click="showAddModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-lg font-semibold shadow-sm transition text-sm flex justify-center">
                    + Tambah Menu Baru
                </button>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-transparent dark:border-slate-700 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-300 uppercase text-xs border-b dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Nama Menu</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Harga (Rp)</th>
                            <th class="px-6 py-4">Stok</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition" 
                            x-show="search === '' || '{{ strtolower(addslashes($product->name)) }}'.includes(search.toLowerCase())">
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $product->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs font-semibold uppercase">{{ $product->type }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-indigo-600 dark:text-indigo-400">
                                {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 {{ $product->stock > 0 ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400' }} rounded text-sm font-bold">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="editForm = { id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', type: '{{ addslashes($product->type) }}', price: {{ $product->price }}, stock: {{ $product->stock }} }; showEditModal = true" class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 text-xs font-medium transition duration-200">
                                    Edit
                                </button>
                                
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" @submit.prevent="if(confirm('Yakin bro mau hapus menu ini secara permanen?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded hover:bg-rose-100 dark:hover:bg-rose-900/50 text-xs font-medium transition duration-200">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($products->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada menu di database. Yuk tambahin! 🍔</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Modal Tambah Menu -->
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="showAddModal" @click="showAddModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                    </div>
                    <div x-show="showAddModal" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf
                            <div class="px-6 py-5">
                                <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white mb-4">Tambah Menu Kosumsi / Barang</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Item</label>
                                        <input type="text" name="name" required placeholder="Contoh: Chitato Sapi Panggang" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori (Type)</label>
                                        <select name="type" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="snack">Snack (Makanan)</option>
                                            <option value="drink">Drink (Minuman)</option>
                                            <option value="other">Lainnya (Barang / Controller)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga Jual (Rp)</label>
                                        <input type="number" name="price" required min="0" placeholder="5000" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kuantitas Stok Awal</label>
                                        <input type="number" name="stock" required min="0" placeholder="10" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex justify-end space-x-3 border-t border-slate-200 dark:border-slate-700">
                                <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm transition">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">Simpan Menu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Menu -->
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                    </div>
                    <div x-show="showEditModal" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                        <form :action="'/products/' + editForm.id" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="px-6 py-5">
                                <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white mb-4">Edit Data Menu</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Item</label>
                                        <input type="text" name="name" x-model="editForm.name" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori (Type)</label>
                                        <select name="type" x-model="editForm.type" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="snack">Snack (Makanan)</option>
                                            <option value="drink">Drink (Minuman)</option>
                                            <option value="other">Lainnya (Barang / Controller)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga Jual (Rp)</label>
                                        <input type="number" name="price" x-model="editForm.price" required min="0" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sisa Stok (Bisa diisi manual)</label>
                                        <input type="number" name="stock" x-model="editForm.stock" required min="0" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex justify-end space-x-3 border-t border-slate-200 dark:border-slate-700">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm transition">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

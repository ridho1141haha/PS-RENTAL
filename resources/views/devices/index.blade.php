<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">Daftar PlayStation</h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '', showAddModal: false, showEditModal: false, editForm: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm transition">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <input x-model="search" type="text" placeholder="Cari nama device..." class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-1/3 text-sm">
                
                <button @click="showAddModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-lg font-semibold shadow-sm transition text-sm flex justify-center">
                    + Tambah Slot PS
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($devices as $device)
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm hover:shadow-lg transition-transform transform hover:-translate-y-1 p-5 border border-slate-100 dark:border-slate-700" 
                     x-show="search === '' || '{{ strtolower($device->name) }}'.includes(search.toLowerCase())">
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $device->name }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $device->type }}</p>
                        </div>
                        @if($device->status == 'available')
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-semibold">Available</span>
                        @else
                            <span class="px-3 py-1 bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400 rounded-full text-xs font-semibold">Occupied</span>
                        @endif
                    </div>
                    
                    <div class="text-indigo-600 dark:text-indigo-400 font-bold mb-4">
                        Rp {{ number_format($device->price_per_hour, 0, ',', '.') }} <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">/ jam</span>
                    </div>
                    
                    <div class="flex justify-end space-x-2 border-t pt-4 border-slate-50 dark:border-slate-700">
                        <button @click="editForm = { id: {{ $device->id }}, name: '{{ addslashes($device->name) }}', type: '{{ addslashes($device->type) }}', price_per_hour: {{ $device->price_per_hour }}, status: '{{ $device->status }}' }; showEditModal = true" class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 text-sm font-medium transition duration-200">Edit</button>
                        
                        <form action="{{ route('devices.destroy', $device) }}" method="POST" @submit.prevent="if(confirm('Yakin bro mau hapus PS ini?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded hover:bg-rose-100 dark:hover:bg-rose-900/50 text-sm font-medium transition duration-200">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Modal Tambah PS -->
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="showAddModal" @click="showAddModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                    </div>
                    <div x-show="showAddModal" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                        <form action="{{ route('devices.store') }}" method="POST">
                            @csrf
                            <div class="px-6 py-5">
                                <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white mb-4">Tambah PlayStation Baru</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Layar / Alias</label>
                                        <input type="text" name="name" required placeholder="Contoh: Meja 1 / TV Besar" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe PS</label>
                                        <select name="type" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="PS3">PS3</option>
                                            <option value="PS4">PS4</option>
                                            <option value="PS5">PS5</option>
                                            <option value="PC">PC Gaming</option>
                                            <option value="Nintendo Switch">Nintendo Switch</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga per Jam (Rp)</label>
                                        <input type="number" name="price_per_hour" required min="0" placeholder="5000" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex justify-end space-x-3 border-t border-slate-200 dark:border-slate-700">
                                <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm transition">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">Simpan PS</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Edit PS -->
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                    </div>
                    <div x-show="showEditModal" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                        <!-- Menggunakan dynamic action url -->
                        <form :action="'/devices/' + editForm.id" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="px-6 py-5">
                                <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white mb-4">Edit Listing PS</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Layar / Alias</label>
                                        <input type="text" name="name" x-model="editForm.name" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe PS</label>
                                        <select name="type" x-model="editForm.type" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="PS3">PS3</option>
                                            <option value="PS4">PS4</option>
                                            <option value="PS5">PS5</option>
                                            <option value="PC">PC Gaming</option>
                                            <option value="Nintendo Switch">Nintendo Switch</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga per Jam (Rp)</label>
                                        <input type="number" name="price_per_hour" x-model="editForm.price_per_hour" required min="0" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status Ketersediaan</label>
                                        <select name="status" x-model="editForm.status" required class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="available">Available (Siap Pakai)</option>
                                            <option value="occupied">Occupied (Lagi Main)</option>
                                            <option value="maintenance">Maintenance (Rusak/Diperbaiki)</option>
                                        </select>
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

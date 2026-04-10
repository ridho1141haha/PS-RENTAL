<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Kasir Kantin F&B 🍜🥤
        </h2>
    </x-slot>

    <div class="py-12" x-data="fnbLogic()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Area Pemesanan -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 lg:col-span-2 border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Pilih Menu</h3>
                
                @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm transition">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="mb-4 p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg text-sm transition">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($products as $product)
                    <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3 hover:shadow-md transition cursor-pointer" @click="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})">
                        <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mb-1">{{ $product->name }}</div>
                        <div class="text-indigo-600 dark:text-indigo-400 font-bold text-sm mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Stok: {{ $product->stock }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Struk & Bayar -->
            <div class="bg-slate-50 dark:bg-slate-900 rounded-xl shadow-sm p-6 lg:col-span-1 h-fit border border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Keranjang Kasir</h3>

                <form action="{{ route('fnb.store') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-400 mb-1">Nama Pembeli (Opsional)</label>
                        <input type="text" name="guest_name" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm text-sm" placeholder="Contoh: Meja 2 / Budi">
                    </div>

                    <div class="min-h-[150px] mb-4">
                        <template x-if="items.length === 0">
                            <div class="text-slate-500 dark:text-slate-400 text-sm text-center py-8">Keranjang masih kosong</div>
                        </template>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex justify-between items-center mb-3 text-sm">
                                <div class="flex-1">
                                    <div class="font-medium text-slate-800 dark:text-slate-200" x-text="item.name"></div>
                                    <div class="text-indigo-600 dark:text-indigo-400" x-text="'Rp ' + formatPrice(item.price * item.qty)"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="decreaseQty(index)" class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center hover:bg-slate-300 dark:hover:bg-slate-600 dark:text-white transition">-</button>
                                    <span class="w-4 text-center font-semibold text-slate-700 dark:text-slate-300" x-text="item.qty"></span>
                                    <button type="button" @click="increaseQty(index)" class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center hover:bg-slate-300 dark:hover:bg-slate-600 dark:text-white transition">+</button>
                                </div>
                                
                                <!-- Hidden inputs for backend via standard form submit -->
                                <input type="hidden" :name="'items['+index+'][id]'" :value="item.id">
                                <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.qty">
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">Total Tagihan</span>
                            <span class="text-xl font-bold text-slate-900 dark:text-white" x-text="'Rp ' + formatPrice(cartTotal)">Rp 0</span>
                        </div>
                        
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-400 mb-1">Metode Bayar</label>
                        <select name="payment_method" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm text-sm mb-4">
                            <option value="Cash">Cash Uang Tunai</option>
                            <option value="QRIS">QRIS / Transfer</option>
                        </select>

                        <button type="button" @click="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-sm transition disabled:opacity-50" :disabled="items.length === 0">
                            Bayar Sekarang 🛒
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pesanan Online Pending -->
        @if(isset($pendingOrders) && $pendingOrders->isNotEmpty())
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl shadow-sm p-6 overflow-x-auto border-2 border-amber-400 dark:border-amber-600 relative">
                <h3 class="text-lg font-bold text-amber-900 dark:text-amber-400 mb-4 flex items-center">
                    <span class="animate-pulse mr-2 text-xl">🔔</span> Antrean Pesanan Online Masuk!
                </h3>
                <table class="w-full text-left text-sm text-slate-800 dark:text-slate-300">
                    <thead class="bg-amber-100 dark:bg-amber-900/50 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Waktu Masuk</th>
                            <th class="px-4 py-3">Nama Customer</th>
                            <th class="px-4 py-3">Rincian Order</th>
                            <th class="px-4 py-3">Metode Bayar</th>
                            <th class="px-4 py-3">Tagihan</th>
                            <th class="px-4 py-3 rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingOrders as $pending)
                        <tr class="border-b border-amber-200/50 dark:border-amber-800/50 hover:bg-amber-100/50 dark:hover:bg-amber-800/30 transition">
                            <td class="px-4 py-3 font-semibold">{{ $pending->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 font-bold">{{ $pending->guest_name }}</td>
                            <td class="px-4 py-3">
                                <ul class="list-disc list-inside text-xs">
                                    @foreach($pending->items as $item)
                                        <li>{{ $item->product->name }} (<span class="font-bold">{{ $item->quantity }}x</span>)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 {{ $pending->payment_method == 'QRIS' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400' }} rounded text-xs font-black uppercase">{{ $pending->payment_method }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg">
                                Rp {{ number_format($pending->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('fnb.complete', $pending) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow-sm text-xs flex items-center transition" onclick="return confirm('Peringatan: Pastikan Uang Cash Diterima atau QRIS Sudah Masuk!! Lanjutkan?')">
                                        Selesai & Berikan Item ✔️
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Tabel Riwayat -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 overflow-x-auto border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Riwayat Transaksi Terakhir</h3>
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-300 uppercase text-xs border-b dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3">Rincian</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3 rounded-r-lg">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                        <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium dark:text-slate-300">{{ $trx->guest_name }}</td>
                            <td class="px-4 py-3">
                                <ul class="list-disc list-inside text-xs">
                                    @foreach($trx->items as $item)
                                        <li>{{ $item->product->name }} ({{ $item->quantity }}x)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-xs font-semibold">{{ $trx->payment_method }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        @if($transactions->isEmpty())
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada transaksi kantin.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function fnbLogic() {
            return {
                items: [],
                
                addItem(id, name, price, stock) {
                    let existing = this.items.find(i => i.id === id);
                    if (existing) {
                        if (existing.qty < stock) {
                            existing.qty++;
                        } else {
                            alert('Stok '+name+' nggak cukup!');
                        }
                    } else {
                        if (stock > 0) {
                            this.items.push({id: id, name: name, price: price, qty: 1, stock: stock});
                        }
                    }
                },
                
                increaseQty(index) {
                    if (this.items[index].qty < this.items[index].stock) {
                        this.items[index].qty++;
                    }
                },
                
                decreaseQty(index) {
                    if (this.items[index].qty > 1) {
                        this.items[index].qty--;
                    } else {
                        this.items.splice(index, 1);
                    }
                },
                
                get cartTotal() {
                    return this.items.reduce((total, item) => total + (item.price * item.qty), 0);
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                submit() {
                    if(this.items.length > 0) {
                        document.getElementById('checkout-form').submit();
                    }
                }
            }
        }
    </script>
</x-app-layout>

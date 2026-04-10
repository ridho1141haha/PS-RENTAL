<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Order F&B (Self-Service) 🍜🥤
        </h2>
    </x-slot>

    <div class="py-12" x-data="fnbLogic()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Area Pilih Menu -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 lg:col-span-2 border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Katalog Makanan & Minuman</h3>
                
                @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-xl text-sm transition font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="mb-4 p-4 bg-rose-50 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400 rounded-xl text-sm transition font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($products as $product)
                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:shadow-lg dark:hover:bg-slate-700/50 transition cursor-pointer flex flex-col justify-between h-full" @click="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})">
                        <div>
                            <span class="inline-block px-2 py-1 bg-slate-100 dark:bg-slate-700 text-[10px] font-bold text-slate-500 dark:text-slate-400 rounded uppercase tracking-wider mb-2">{{ $product->type }}</span>
                            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm mb-1 leading-tight">{{ $product->name }}</div>
                        </div>
                        <div class="mt-3">
                            <div class="text-indigo-600 dark:text-indigo-400 font-extrabold mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="text-xs font-medium {{ $product->stock > 5 ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-500' }}">Tersedia: {{ $product->stock }}</div>
                        </div>
                    </div>
                    @endforeach
                    @if($products->isEmpty())
                        <div class="col-span-2 md:col-span-3 text-center text-slate-500 py-8">Kantin lagi kosong bro :(</div>
                    @endif
                </div>
            </div>

            <!-- Keranjang Pesenan -->
            <div class="bg-indigo-50/50 dark:bg-slate-900 rounded-xl shadow-sm p-6 lg:col-span-1 h-fit border border-indigo-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-indigo-100 dark:border-slate-800 pb-2 flex justify-between items-center">
                    <span>Keranjang Anda</span>
                    <span class="bg-indigo-600 text-white text-xs px-2 py-1 rounded-full" x-show="items.length > 0" x-text="items.length + ' Item'"></span>
                </h3>

                <form action="{{ route('fnb.store') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <div class="min-h-[120px] mb-4">
                        <template x-if="items.length === 0">
                            <div class="text-slate-500 dark:text-slate-400 text-sm flex flex-col items-center justify-center py-8">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Pilih menu di samping</span>
                            </div>
                        </template>

                        <ul class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <li class="flex justify-between items-center text-sm bg-white dark:bg-slate-800 p-3 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700">
                                    <div class="flex-1 pr-2">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 leading-tight" x-text="item.name"></div>
                                        <div class="text-indigo-600 dark:text-indigo-400 font-medium text-xs mt-0.5" x-text="'Rp ' + formatPrice(item.price * item.qty)"></div>
                                    </div>
                                    <div class="flex items-center bg-slate-100 dark:bg-slate-700 rounded-lg p-0.5">
                                        <button type="button" @click="decreaseQty(index)" class="w-7 h-7 rounded bg-white dark:bg-slate-600 shadow-sm flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-500 font-bold dark:text-white transition">-</button>
                                        <span class="w-6 text-center font-bold text-slate-700 dark:text-slate-200 text-xs" x-text="item.qty"></span>
                                        <button type="button" @click="increaseQty(index)" class="w-7 h-7 rounded bg-white dark:bg-slate-600 shadow-sm flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-500 font-bold dark:text-white transition">+</button>
                                    </div>
                                    
                                    <input type="hidden" :name="'items['+index+'][id]'" :value="item.id">
                                    <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.qty">
                                </li>
                            </template>
                        </ul>
                    </div>

                    <div x-show="items.length > 0" x-transition class="border-t border-indigo-100 dark:border-slate-800 pt-4 mb-4">
                        <div class="flex justify-between items-center mb-5">
                            <span class="font-bold text-slate-700 dark:text-slate-300">Total Tagihan</span>
                            <span class="text-2xl font-black text-indigo-700 dark:text-indigo-400" x-text="'Rp ' + formatPrice(cartTotal)">Rp 0</span>
                        </div>
                        
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="Cash" x-model="paymentMethod" class="peer sr-only">
                                <div class="rounded-lg border-2 border-slate-200 dark:border-slate-700 p-3 text-center peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 dark:peer-checked:border-indigo-400 transition">
                                    <div class="font-bold text-sm text-slate-800 dark:text-slate-200">Cash / Tunai</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="QRIS" x-model="paymentMethod" class="peer sr-only">
                                <div class="rounded-lg border-2 border-slate-200 dark:border-slate-700 p-3 text-center peer-checked:border-emerald-600 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/30 dark:peer-checked:border-emerald-400 transition">
                                    <div class="font-bold text-sm text-slate-800 dark:text-slate-200">QRIS</div>
                                </div>
                            </label>
                        </div>

                        <!-- Tombol Order Normal -->
                        <button type="button" @click="handleOrderClick()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md transition disabled:opacity-50">
                            Pesan Sekarang 🚀
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Berjalan (Menunggu Kasir) -->
        @if($myActiveOrders->isNotEmpty())
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="bg-indigo-600 dark:bg-indigo-900/50 rounded-xl shadow-lg p-6 overflow-hidden relative">
                <!-- Decorative background elements -->
                <div class="absolute -right-10 -top-10 opacity-20">
                    <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 22h14M5 2h14M6 2v20M18 2v20M9 6h6M9 10h6M9 14h6M9 18h6"/></svg>
                </div>

                <h3 class="text-xl font-black text-white mb-2 flex items-center">
                    <span class="animate-pulse mr-2">⏳</span> Pesanan Anda Sedang Menunggu
                </h3>
                <p class="text-indigo-100 text-sm mb-6 max-w-2xl">Silakan datangi meja kasir untuk mengambil pesanan Anda di bawah ini. Harap siapkan pembayaran jika memilih metode Cash.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($myActiveOrders as $order)
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg p-4 text-white">
                        <div class="flex justify-between items-start mb-3 border-b border-white/20 pb-2">
                            <div>
                                <span class="bg-white/20 text-xs px-2 py-0.5 rounded font-bold uppercase">{{ $order->payment_method }}</span>
                                <div class="text-xs text-indigo-200 mt-1">{{ $order->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <ul class="text-sm space-y-1">
                            @foreach($order->items as $item)
                                <li class="flex justify-between">
                                    <span>{{ $item->product->name }} <span class="text-indigo-200 font-bold ml-1">x{{ $item->quantity }}</span></span>
                                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- QRIS Modal -->
        <div x-show="showQrisModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showQrisModal" class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm" aria-hidden="true"></div>
                
                <div x-show="showQrisModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full overflow-hidden border border-slate-200 dark:border-slate-700 relative">
                    
                    <div class="bg-emerald-500 p-4 text-center">
                        <h3 class="text-xl font-black text-white">Scan QRIS</h3>
                        <p class="text-emerald-100 text-sm font-medium">Pembayaran PS Rental & F&B</p>
                    </div>
                    
                    <div class="p-6 flex flex-col items-center">
                        <!-- Dummy QR Image Box -->
                        <div class="w-48 h-48 bg-slate-100 dark:bg-white rounded-xl flex border-4 border-emerald-500 p-3 mb-4 items-center justify-center">
                            <div class="w-full h-full border-4 border-dashed border-slate-300 flex items-center justify-center text-slate-400 font-bold text-center leading-tight">
                                GUNAKAN IMAGE QRIS ASLI DI SINI
                            </div>
                        </div>
                        
                        <div class="text-center mb-6 w-full">
                            <p class="text-slate-500 dark:text-slate-400 text-xs uppercase font-bold mb-1">Total yang harus dibayar</p>
                            <div class="text-3xl font-black text-slate-800 dark:text-white" x-text="'Rp ' + formatPrice(cartTotal)"></div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 w-full mb-6 text-xs text-yellow-800 dark:text-yellow-400 text-center font-medium">
                            ⚠️ Setelah sukses transfer, jgn lupa konfirmasi!
                        </div>

                        <div class="flex flex-col w-full gap-2">
                            <button type="button" @click="submitDirectly()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md transition">
                                ✔️ Saya Sudah Transfer
                            </button>
                            <button type="button" @click="showQrisModal = false" class="w-full py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-semibold transition">
                                Batal / Ubah Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function fnbLogic() {
            return {
                items: [],
                paymentMethod: 'Cash',
                showQrisModal: false,
                
                addItem(id, name, price, stock) {
                    let existing = this.items.find(i => i.id === id);
                    if (existing) {
                        if (existing.qty < stock) {
                            existing.qty++;
                        } else {
                            alert('Sisa stok '+name+' cuma ada '+stock+' bro!');
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

                handleOrderClick() {
                    if(this.items.length === 0) return;
                    
                    if(this.paymentMethod === 'QRIS') {
                        this.showQrisModal = true;
                    } else {
                        // Cash direct submit
                        if(confirm('Pesan sekarang pakai Tunai? Bawa uang pas ke meja kasir ya!')) {
                            this.submitDirectly();
                        }
                    }
                },

                submitDirectly() {
                    // Disable buttons to prevent double submit
                    document.getElementById('checkout-form').submit();
                }
            }
        }
    </script>
</x-app-layout>

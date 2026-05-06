<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">Sewa PlayStation</h2>
    </x-slot>

    <div class="py-12" x-data="rentalLogic()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 lg:col-span-1 h-fit border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Buat Sewa Baru</h3>
                
                @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm transition">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-4 p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg text-sm transition">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('rentals.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Console</label>
                        <select name="device_id" x-model="selectedDevice" @change="calculate" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Pilih PS --</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" data-price="{{ $device->price_per_hour }}" {{ $device->status == 'occupied' ? 'disabled' : '' }}>
                                    {{ $device->name }} - Rp{{ number_format($device->price_per_hour, 0, ',', '.') }}/jam {{ $device->status == 'occupied' ? '🔴 (Dipakai)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Sewa</label>
                        <select name="type" x-model="rentalType" @change="calculate" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="fixed">Paket Aktif (Berhenti Tepat Waktu)</option>
                            <option value="open_bill">Main Bebas (Loss Doll / Buka Billing)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mulai Main</label>
                        <input type="datetime-local" name="start_time" x-model="start" @change="calculate" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm">
                    </div>

                    <div class="mb-4" x-show="rentalType === 'fixed'">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Target Selesai</label>
                        <input type="datetime-local" name="end_time" x-model="end" @change="calculate" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm">
                    </div>

                    <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg shadow-inner flex flex-col" x-show="rentalType === 'fixed'">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Estimasi Tagihan:</span>
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="formattedPrice">Rp 0</span>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-lg font-semibold shadow-sm transition">
                        Konfirmasi & Main 🎮
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 lg:col-span-2 overflow-x-auto border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Riwayat Rental</h3>
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-300 uppercase text-xs border-b dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Pemain</th>
                            <th class="px-4 py-3">Device</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Total Tagihan</th>
                            <th class="px-4 py-3 rounded-r-lg">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rentals as $rental)
                        <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3 font-medium dark:text-slate-300">{{ $rental->user->name }}</td>
                            <td class="px-4 py-3">{{ $rental->device->name }}</td>
                            <td class="px-4 py-3">
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $rental->start_time->format('H:i') }}</span> - 
                                @if($rental->end_time)
                                    <span class="text-rose-600 dark:text-rose-400 font-medium">{{ $rental->end_time->format('H:i') }}</span>
                                @else
                                    <span class="text-amber-600 dark:text-amber-400 font-medium whitespace-nowrap">Belum Selesai ({{ $rental->type == 'open_bill' ? 'Loss Doll' : 'Fixed' }})</span>
                                @endif
                                <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $rental->start_time->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                                @if($rental->status == 'completed' || $rental->type == 'fixed')
                                    Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                                @else
                                    Rp - (Berjalan)
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($rental->status == 'completed' || ($rental->type == 'fixed' && $rental->end_time && $rental->end_time <= now()))
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-md text-xs font-semibold">Selesai</span>
                                        @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('rentals.print', $rental->id) }}" target="_blank" class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-md text-xs font-semibold transition flex items-center shadow-sm border border-indigo-100 dark:border-indigo-800">
                                            🖨️ Cetak
                                        </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 rounded-md text-xs font-semibold">Berjalan</span>
                                        <form action="{{ route('rentals.update', $rental->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('Selesaikan rental ini sekarang?')" class="text-xs font-semibold text-white bg-rose-500 hover:bg-rose-600 px-2 py-1 rounded shadow-sm transition">
                                                Stop
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $rentals->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function rentalLogic() {
            return {
                selectedDevice: '',
                rentalType: 'fixed',
                start: '',
                end: '',
                total: 0,
                
                get formattedPrice() {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(this.total);
                },

                calculate() {
                    if (this.rentalType === 'open_bill') {
                        this.total = 0;
                        return;
                    }

                    if (this.selectedDevice && this.start && this.end) {
                        let t1 = new Date(this.start);
                        let t2 = new Date(this.end);
                        
                        if (t2 > t1) {
                            let ms = t2 - t1;
                            let hrs = Math.ceil(ms / (1000 * 60 * 60)); 
                            hrs = hrs < 1 ? 1 : hrs; 
                            
                            let selectNode = document.querySelector('select[name="device_id"]');
                            let activeOpt = selectNode.options[selectNode.selectedIndex];
                            let price = parseInt(activeOpt.getAttribute('data-price') || 0);

                            this.total = hrs * price;
                        } else {
                            this.total = 0;
                        }
                    } else {
                        this.total = 0;
                    }
                }
            }
        }
    </script>
</x-app-layout>

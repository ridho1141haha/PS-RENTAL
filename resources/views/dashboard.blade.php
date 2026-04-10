<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">Dashboard Overview</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm hover:shadow-md transition p-6 flex items-center space-x-4 border border-transparent dark:border-slate-700">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Device</p>
                        <p class="text-2xl font-bold border-b border-indigo-100 dark:border-slate-700 pb-1 text-slate-800 dark:text-white">{{ $totalDevices }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm hover:shadow-md transition p-6 flex items-center space-x-4 border border-transparent dark:border-slate-700">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Available</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $availableDevices }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm hover:shadow-md transition p-6 flex items-center space-x-4 border border-transparent dark:border-slate-700">
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Occupied</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $occupiedDevices }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm hover:shadow-md transition p-6 flex items-center space-x-4 border border-transparent dark:border-slate-700">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Pendapatan Hari Ini</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">Rp {{ number_format($totalRevenueToday, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 overflow-x-auto border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">PS yang Sedang Main (Live Timer ⏲️)</h3>
                
                @if(session('success'))
                    <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-300 uppercase text-xs border-b dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Pemain</th>
                            <th class="px-4 py-3">Device (Harga/jam)</th>
                            <th class="px-4 py-3">Tipe & Mulai</th>
                            <th class="px-4 py-3">Realtime Timer ⏳</th>
                            <th class="px-4 py-3 rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeRentals as $rental)
                        <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3 font-medium dark:text-slate-300">{{ $rental->user->name }}</td>
                            <td class="px-4 py-3">
                                {{ $rental->device->name }} <br>
                                <span class="text-xs text-slate-400">Rp {{ number_format($rental->device->price_per_hour, 0, ',', '.') }}/jam</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 {{ $rental->type == 'fixed' ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400' }} rounded-md text-xs font-semibold">
                                    {{ $rental->type == 'fixed' ? 'Fixed/Paketan' : 'Open Bill (Loss Doll)' }}
                                </span>
                                <div class="mt-1 text-xs font-semibold text-slate-800 dark:text-slate-300">{{ $rental->start_time->format('H:i') }}</div>
                                @if($rental->type == 'fixed')
                                    <div class="text-xs text-slate-500">s/d {{ $rental->end_time->format('H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3" x-data="liveTimer('{{ $rental->start_time->toIso8601String() }}', '{{ $rental->type }}', '{{ $rental->end_time ? $rental->end_time->toIso8601String() : '' }}', {{ $rental->device->price_per_hour }})">
                                <div class="font-mono text-lg font-bold" :class="timeColor" x-text="timerDisplay">00:00:00</div>
                                <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1" x-text="estimatePriceDisplay">Rp 0</div>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('rentals.update', $rental->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Yakin mau checkout / berhentiin billing PS ini?')" class="text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 px-3 py-1.5 rounded-lg shadow-sm transition">
                                        Checkout 💰
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($activeRentals->isEmpty())
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada PS yang nyala bro. Gas tawarin temen! 🎮</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function liveTimer(startStr, type, endStr, pricePerHour) {
            return {
                start: new Date(startStr),
                end: endStr ? new Date(endStr) : null,
                now: new Date(),
                pricePerHour: pricePerHour,
                type: type,
                
                init() {
                    setInterval(() => {
                        this.now = new Date();
                    }, 1000);
                },

                get timerDisplay() {
                    if (this.type === 'fixed' && this.end) {
                        let ms = this.end - this.now;
                        if (ms <= 0) return "Habis Waktu ⌛";
                        
                        let h = Math.floor(ms / 3600000);
                        let m = Math.floor((ms % 3600000) / 60000);
                        let s = Math.floor((ms % 60000) / 1000);
                        return `Sisa ${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    } else {
                        let ms = this.now - this.start;
                        let h = Math.floor(ms / 3600000);
                        let m = Math.floor((ms % 3600000) / 60000);
                        let s = Math.floor((ms % 60000) / 1000);
                        return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    }
                },

                get estimatePriceDisplay() {
                    if (this.type === 'fixed') {
                        let ms = this.end - this.start;
                        let maxHours = Math.ceil(ms / 3600000);
                        maxHours = maxHours < 1 ? 1 : maxHours;
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(maxHours * this.pricePerHour);
                    } else {
                        let ms = this.now - this.start;
                        let hours = Math.ceil(ms / 3600000);
                        hours = hours < 1 ? 1 : hours;
                        return 'Est: Rp ' + new Intl.NumberFormat('id-ID').format(hours * this.pricePerHour);
                    }
                },

                get timeColor() {
                    if (this.type === 'fixed' && this.end) {
                        let ms = this.end - this.now;
                        if (ms <= 0) return "text-rose-600 dark:text-rose-400 animate-pulse";
                        if (ms < 600000) return "text-amber-500 dark:text-amber-400"; // < 10 mins
                        return "text-indigo-600 dark:text-indigo-400";
                    }
                    return "text-emerald-600 dark:text-emerald-400";
                }
            }
        }
    </script>
</x-app-layout>

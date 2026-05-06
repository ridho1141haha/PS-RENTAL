<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Booking PS (Reservasi) 🎮📅
        </h2>
    </x-slot>

    <div class="py-12" x-data="bookingLogic()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Form Booking -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 border border-transparent dark:border-slate-700 md:col-span-1 h-fit">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Buat Booking Baru</h3>
                
                @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm transition">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg text-sm transition">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-4 p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg text-sm transition">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Device PS</label>
                        <select name="device_id" x-model="selectedDevice" @change="calculate" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm" required>
                            <option value="">Pilih Mesin PS...</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" data-price="{{ $device->price_per_hour }}">{{ $device->name }} - Rp {{ number_format($device->price_per_hour, 0, ',', '.') }}/jam</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Main</label>
                        <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jam Mulai</label>
                        <input type="time" name="start_time" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Durasi (Jam)</label>
                        <input type="number" name="duration_hours" min="1" max="12" x-model="duration" @input="calculate" value="1" class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm" required>
                    </div>

                    <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 rounded-lg text-center">
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold block mb-1">Estimasi Harga Booking</span>
                        <span class="text-2xl font-black text-indigo-700 dark:text-indigo-300" x-text="formattedPrice">Rp 0</span>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-lg font-bold shadow-sm transition">
                        Kirim Request Booking 🚀
                    </button>
                </form>
            </div>

            <!-- List Jadwalku -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 md:col-span-2 border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Jadwal Booking Saya</h3>
                
                <div class="grid grid-cols-1 gap-4">
                    @forelse($myReservations as $res)
                        <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200">{{ $res->device->name }}</h4>
                                    
                                    @if($res->status == 'pending')
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400 text-xs font-bold rounded-full">⏳ Menyesuaikan Jadwal</span>
                                    @elseif($res->status == 'approved')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400 text-xs font-bold rounded-full">✅ Disetujui (Siap Main)</span>
                                    @elseif($res->status == 'rejected')
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400 text-xs font-bold rounded-full">❌ Ditolak (Penuh)</span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                    📅 {{ $res->booking_date->format('d M Y') }} | ⏰ {{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} (Selama {{ $res->duration_hours }} Jam)
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-500">Harga</div>
                                <div class="font-black text-lg text-indigo-600 dark:text-indigo-400">Rp {{ number_format($res->total_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">Belum ada history booking nih bro. Bikin jadwal mabar dulu gih! 👇</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        function bookingLogic() {
            return {
                selectedDevice: '',
                duration: 1,
                total: 0,
                
                get formattedPrice() {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(this.total);
                },

                calculate() {
                    let selectNode = document.querySelector('select[name="device_id"]');
                    if (this.selectedDevice && selectNode.selectedIndex > 0) {
                        let activeOpt = selectNode.options[selectNode.selectedIndex];
                        let price = parseInt(activeOpt.getAttribute('data-price') || 0);
                        let dur = parseInt(this.duration) || 0;
                        this.total = dur * price;
                    } else {
                        this.total = 0;
                    }
                }
            }
        }
    </script>
</x-app-layout>

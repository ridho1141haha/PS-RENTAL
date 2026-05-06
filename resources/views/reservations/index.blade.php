<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Manajemen Booking PS 📅 Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="p-4 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm transition">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg text-sm transition">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Request Booking Menunggu Keputusan -->
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl shadow-sm p-6 overflow-x-auto border-2 border-amber-400 dark:border-amber-600 relative">
                <h3 class="text-lg font-bold text-amber-900 dark:text-amber-400 mb-4 flex items-center">
                    <span class="animate-pulse mr-2 text-xl">⏳</span> Request Jadwal Perlu Di-ACC
                </h3>
                
                <table class="w-full text-left text-sm text-slate-800 dark:text-slate-300">
                    <thead class="bg-amber-100 dark:bg-amber-900/50 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Customer</th>
                            <th class="px-4 py-3">Device (PS)</th>
                            <th class="px-4 py-3">Jadwal Main</th>
                            <th class="px-4 py-3">Est. Duit</th>
                            <th class="px-4 py-3 rounded-r-lg">Keputusan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingReservations as $res)
                        <tr class="border-b border-amber-200/50 dark:border-amber-800/50 hover:bg-amber-100/50 dark:hover:bg-amber-800/30 transition">
                            <td class="px-4 py-3 font-bold">{{ $res->user->name }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $res->device->name }}</td>
                            <td class="px-4 py-3">
                                📅 <span class="font-bold text-amber-700 dark:text-amber-400">{{ $res->booking_date->format('d M y') }}</span><br>
                                ⏰ <span class="text-xs">{{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} (Selama {{ $res->duration_hours }} Jam)</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-lg text-indigo-700 dark:text-indigo-400">
                                Rp {{ number_format($res->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('reservations.status', $res) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow-sm text-xs transition" onclick="return confirm('ACC JADWAL INI?')">
                                            ✅ ACC Sikat!
                                        </button>
                                    </form>
                                    <form action="{{ route('reservations.status', $res) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded shadow-sm text-xs transition" onclick="return confirm('Tolak karena penuh?')">
                                            ❌ Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-amber-700 dark:text-amber-500 font-semibold">Tumben sepi Request, belom ada antrean Bro!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Main Log -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 overflow-x-auto border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Riwayat (Jadwal Terdaftar)</h3>
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-300 uppercase text-xs border-b dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Tgl Main</th>
                            <th class="px-4 py-3">Pemain</th>
                            <th class="px-4 py-3">Device & Jam</th>
                            <th class="px-4 py-3">Harga Pesen</th>
                            <th class="px-4 py-3 rounded-r-lg">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $res)
                        <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3">{{ $res->booking_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium dark:text-slate-300">{{ $res->user->name }}</td>
                            <td class="px-4 py-3">
                                {{ $res->device->name }}<br>
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} ({{ $res->duration_hours }} Jam)</span>
                            </td>
                            <td class="px-4 py-3 font-bold">
                                Rp {{ number_format($res->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($res->status == 'approved')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400 rounded text-xs font-semibold">Telah Disetujui</span>
                                @elseif($res->status == 'rejected')
                                    <span class="px-2 py-1 bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-400 rounded text-xs font-semibold">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $reservations->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

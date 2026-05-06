<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Laporan Omset & Promo 📈
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Pengaturan Banner Promo -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 border border-transparent dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2 flex items-center">
                    <span class="text-2xl mr-2">📢</span> Pengumuman / Banner Promo (Live)
                </h3>
                
                @if(session('success'))
                    <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('reports.promo') }}" method="POST" class="flex gap-4 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teks Banner</label>
                        <input type="text" name="promo_text" value="{{ $promo }}" placeholder="Kosongkan untuk menyembunyikan banner..." class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <p class="text-xs text-slate-500 mt-1">Teks ini bakal langsung muncul kelap-kelip di atas Dashboard semua Customer.</p>
                    </div>
                    <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-sm transition">
                        Update Banner
                    </button>
                </form>
            </div>

            <!-- Omzet Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Rental -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md p-6 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-indigo-100 font-medium mb-1">Omset Sewa PS (Bulan Ini)</p>
                        <p class="text-3xl font-black">Rp {{ number_format($totalRentalOmset, 0, ',', '.') }}</p>
                    </div>
                    <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16.5C21 16.88 20.79 17.21 20.47 17.38L12.57 21.82C12.41 21.94 12.21 22 12 22C11.79 22 11.59 21.94 11.43 21.82L3.53 17.38C3.21 17.21 3 16.88 3 16.5V7.5C3 7.12 3.21 6.79 3.53 6.62L11.43 2.18C11.59 2.06 11.79 2 12 2C12.21 2 12.41 2.06 12.57 2.18L20.47 6.62C20.79 6.79 21 7.12 21 7.5V16.5Z"></path></svg>
                </div>
                <!-- Total FNB -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-md p-6 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-emerald-100 font-medium mb-1">Omset Kantin (Bulan Ini)</p>
                        <p class="text-3xl font-black">Rp {{ number_format($totalFnbOmset, 0, ',', '.') }}</p>
                    </div>
                    <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M5 13L9 17L19 7"></path></svg>
                </div>
                <!-- Grand Total -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl shadow-md p-6 text-white relative overflow-hidden ring-2 ring-indigo-500/30">
                    <div class="relative z-10">
                        <p class="text-slate-400 font-medium mb-1">Grand Total (Bulan Ini)</p>
                        <p class="text-3xl font-black text-amber-400">Rp {{ number_format($totalRentalOmset + $totalFnbOmset, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Grafik Analytics -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 border border-transparent dark:border-slate-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Grafik Pendapatan Harian</h3>
                    
                    <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2">
                        <select name="month" class="rounded border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm py-1">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                        <select name="year" class="rounded border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm text-sm py-1">
                            @for($y=date('Y')-2; $y<=date('Y'); $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="px-3 py-1 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 rounded text-sm font-semibold transition dark:text-white">Filter</button>
                    </form>
                </div>

                <div class="h-[400px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const dataLabels = {!! json_encode($chartLabels) !!};
            const rentalData = {!! json_encode($chartRental) !!};
            const fnbData = {!! json_encode($chartFnb) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataLabels,
                    datasets: [
                        {
                            label: 'Pendapatan Sewa PS (Rp)',
                            data: rentalData,
                            backgroundColor: 'rgba(99, 102, 241, 0.8)', // Indigo
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Pendapatan Kantin F&B (Rp)',
                            data: fnbData,
                            backgroundColor: 'rgba(16, 185, 129, 0.8)', // Emerald
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

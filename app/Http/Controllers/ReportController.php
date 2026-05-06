<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Rental;
use App\Models\FnbTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $promo = Setting::where('key', 'promo_text')->first()->value ?? '';
        
        $month = request('month', date('m'));
        $year = request('year', date('Y'));

        // SQLite uses strftime for DATE extraction, but DATE() also works in modern sqlite.
        $rentalData = Rental::where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')->toArray();

        $fnbData = FnbTransaction::where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')->toArray();

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $chartLabels = [];
        $chartRental = [];
        $chartFnb = [];
        
        $totalRentalOmset = 0;
        $totalFnbOmset = 0;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            // Match the keys plucked from SQLite DATE() format (YYYY-MM-DD)
            $dateString = sprintf('%04d-%02d-%02d', $year, $month, $i);
            $chartLabels[] = $i;
            
            $rt = $rentalData[$dateString] ?? 0;
            $ft = $fnbData[$dateString] ?? 0;

            $chartRental[] = $rt;
            $chartFnb[] = $ft;

            $totalRentalOmset += $rt;
            $totalFnbOmset += $ft;
        }

        return view('reports.index', compact(
            'promo', 'chartLabels', 'chartRental', 'chartFnb', 
            'totalRentalOmset', 'totalFnbOmset', 'month', 'year'
        ));
    }

    public function updatePromo(Request $request)
    {
        $request->validate([
            'promo_text' => 'nullable|string|max:255'
        ]);

        Setting::updateOrCreate(
            ['key' => 'promo_text'],
            ['value' => $request->promo_text]
        );

        return back()->with('success', 'Teks Promo Banner berhasil di-update bro! 🚀');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Device;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RentalController extends Controller {
    public function index() {
        if (auth()->user()->role === 'admin') {
            $rentals = Rental::with(['user', 'device'])->latest()->paginate(10);
        } else {
            $rentals = Rental::with(['user', 'device'])->where('user_id', auth()->id())->latest()->paginate(10);
        }
        $devices = Device::all();
        return view('rentals.index', compact('rentals', 'devices'));
    }

    public function store(Request $request) {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'type' => 'required|in:fixed,open_bill',
            'start_time' => 'required|date',
        ]);

        if ($request->type === 'fixed') {
            $request->validate(['end_time' => 'required|date|after:start_time']);
        }

        $device = Device::findOrFail($request->device_id);

        if ($device->status !== 'available') {
            return back()->with('error', 'Waduh, PS-nya lagi dipakai bro! 🕹️');
        }

        $start = Carbon::parse($request->start_time);
        $total_price = 0;
        $end = null;

        if ($request->type === 'fixed') {
            $end = Carbon::parse($request->end_time);
            $hours = ceil($end->diffInMinutes($start) / 60);
            $hours = $hours > 0 ? $hours : 1; 
            $total_price = $hours * $device->price_per_hour;
        }

        Rental::create([
            'user_id' => auth()->id(),
            'device_id' => $device->id,
            'type' => $request->type,
            'status' => 'unpaid',
            'start_time' => $start,
            'end_time' => $end,
            'total_price' => $total_price,
        ]);

        $device->update(['status' => 'occupied']);

        return back()->with('success', 'Gas! PS berhasil dibooking bro. 🔥');
    }

    public function update(Request $request, Rental $rental) {
        // Kalo status udah completed, ya gausah diapa-apain 
        if ($rental->status === 'completed') {
            return back()->with('error', 'Waduh, rental udah lunas/kelar ini mah! 😅');
        }

        $end = now();
        $start = Carbon::parse($rental->start_time);
        
        // Itung total jam main dari awal ampe sekarang (pembulatan ke atas)
        $hours = ceil($end->diffInMinutes($start) / 60);
        $hours = $hours > 0 ? $hours : 1; 
        
        // Buat open_bill, update total harganya. Kalo fixed tetep bisa distop sebelum waktunya tapi bayarnya full? Sesuai di awal aja dah.
        // Tapi asumsikan aja kalo distop sekarang, bayar sejumlah jam main (untuk fair use).
        $total_price = $hours * $rental->device->price_per_hour;

        $rental->update([
            'end_time' => $rental->type === 'fixed' && $rental->end_time && $rental->end_time > $end ? $rental->end_time : $end,
            'total_price' => $rental->type === 'fixed' ? $rental->total_price : $total_price,
            'status' => 'completed',
        ]);

        $rental->device->update(['status' => 'available']);

        $finalPrice = $rental->type === 'fixed' ? $rental->total_price : $total_price;
        
        // Kasih poin loyalty: Rp 1.000 = 1 Poin
        if ($rental->user) {
            $earnedPoints = floor($finalPrice / 1000);
            $rental->user->increment('points', $earnedPoints);
        }

        return back()->with('success', 'Rental kelar! Gas bayar: Rp ' . number_format($finalPrice, 0, ',', '.') . ' 💰 (Dapet ' . $earnedPoints . ' Poin!)');
    }

    public function print(Rental $rental) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $rental->load(['user', 'device']);
        return view('print.receipt', [
            'type' => 'rental',
            'data' => $rental
        ]);
    }
}

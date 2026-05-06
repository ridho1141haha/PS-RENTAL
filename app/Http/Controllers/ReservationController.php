<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Device;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $pendingReservations = Reservation::with(['user', 'device'])->where('status', 'pending')->oldest()->get();
            $reservations = Reservation::with(['user', 'device'])->where('status', '!=', 'pending')->latest()->paginate(10);
            return view('reservations.index', compact('pendingReservations', 'reservations'));
        }

        $devices = Device::all();
        $myReservations = Reservation::with(['device'])->where('user_id', auth()->id())->latest()->get();
        return view('reservations.customer', compact('devices', 'myReservations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'duration_hours' => 'required|integer|min:1|max:12',
        ]);

        $device = Device::findOrFail($request->device_id);
        $totalPrice = $device->price_per_hour * $request->duration_hours;

        Reservation::create([
            'user_id' => auth()->id(),
            'device_id' => $device->id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'duration_hours' => $request->duration_hours,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Booking berhasil diajukan! Tunggu konfirmasi Admin ya bos. 🎮');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed'
        ]);

        $reservation->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            return back()->with('success', 'Booking disetujui! ✅');
        } elseif ($request->status === 'rejected') {
            return back()->with('error', 'Booking terpaksa ditolak! ❌');
        }

        return back()->with('success', 'Selesai didata.');
    }
}

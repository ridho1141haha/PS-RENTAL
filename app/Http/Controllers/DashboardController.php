<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Rental;

class DashboardController extends Controller {
    public function index() {
        $totalDevices = Device::count();
        $availableDevices = Device::where('status', 'available')->count();
        $occupiedDevices = Device::where('status', 'occupied')->count();
        $totalRentalToday = Rental::whereDate('created_at', today())->where('status', 'completed')->sum('total_price');
        $totalFnbToday = \App\Models\FnbTransaction::whereDate('created_at', today())->where('status', 'completed')->sum('total_price');
        $totalRevenueToday = $totalRentalToday + $totalFnbToday;
        
        $activeRentals = Rental::with(['user', 'device'])->where('status', 'unpaid')->get();

        return view('dashboard', compact('totalDevices', 'availableDevices', 'occupiedDevices', 'totalRevenueToday', 'activeRentals'));
    }
}

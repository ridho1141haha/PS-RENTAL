<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller {
    public function index() {
        $devices = Device::latest()->get();
        return view('devices.index', compact('devices'));
    }
    
    public function destroy(Device $device) {
        $device->delete();
        return back()->with('success', 'Device berhasil dihapus!');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100', // e.g., PS4, PS5
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        Device::create([
            'name' => $request->name,
            'type' => $request->type,
            'price_per_hour' => $request->price_per_hour,
            'status' => 'available' // default status
        ]);

        return back()->with('success', 'Mantap bro, PS baru berhasil ditambah!');
    }

    public function update(Request $request, Device $device) {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'price_per_hour' => 'required|numeric|min:0',
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        $device->update($request->only(['name', 'type', 'price_per_hour', 'status']));

        return back()->with('success', 'Listing PS berhasil diupdate!');
    }
}

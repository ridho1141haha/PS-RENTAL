<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create($request->only(['name', 'type', 'price', 'stock']));

        return back()->with('success', 'Wuhu! Menu jajanan/barang baru berhasil ditambah! 🍿');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($request->only(['name', 'type', 'price', 'stock']));

        return back()->with('success', 'Data menu berhasil diupdate bro!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Daaan ilang! Menu berhasil dihapus. 💨');
    }
}

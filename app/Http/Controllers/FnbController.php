<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\FnbTransaction;
use App\Models\FnbTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FnbController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();

        if (auth()->user()->role === 'admin') {
            $pendingOrders = FnbTransaction::with('items.product')->where('status', 'pending')->oldest()->get();
            $transactions = FnbTransaction::with('items.product')->where('status', 'completed')->latest()->paginate(10);
            return view('fnb.index', compact('products', 'transactions', 'pendingOrders'));
        }

        // For normal users (customer ordering)
        $myActiveOrders = FnbTransaction::with('items.product')->where('user_id', auth()->id())->where('status', 'pending')->latest()->get();
        return view('fnb.customer', compact('products', 'myActiveOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:Cash,QRIS',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} nggak cukup bro! Sisa: {$product->stock}");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $user = auth()->user();
            $status = $user->role === 'admin' ? 'completed' : 'pending';

            $transaction = FnbTransaction::create([
                'user_id' => $user->role === 'admin' ? null : $user->id,
                'guest_name' => $user->role === 'admin' ? ($request->guest_name ?? 'Guest') : $user->name,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'status' => $status,
            ]);

            foreach ($itemsData as $data) {
                $transaction->items()->create($data);
            }

            DB::commit();

            return back()->with('success', 'Mantap! Pesanan berhasil dibayar: Rp ' . number_format($totalPrice, 0, ',', '.') . ' 🍜🥤');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function markCompleted(FnbTransaction $fnbTransaction) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $fnbTransaction->update(['status' => 'completed']);

        $earnedPoints = 0;
        if ($fnbTransaction->user) {
            $earnedPoints = floor($fnbTransaction->total_price / 1000);
            $fnbTransaction->user->increment('points', $earnedPoints);
        }

        return back()->with('success', 'Pesanan online atas nama ' . $fnbTransaction->guest_name . ' selesai bro! ✅ (User dapet ' . $earnedPoints . ' Poin!)');
    }

    public function print(FnbTransaction $fnbTransaction) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $fnbTransaction->load(['user', 'items.product']);
        return view('print.receipt', [
            'type' => 'fnb',
            'data' => $fnbTransaction
        ]);
    }
}

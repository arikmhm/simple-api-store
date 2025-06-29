<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // --- TAMBAHKAN BARIS INI ---

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user:id,name', 'product:id,name,price,image_url'])
                        ->orderBy('created_at', 'desc')
                        ->get(); 

        return response()->json([
            'message' => 'All orders fetched successfully',
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'total_price' => $product->price * $validated['quantity'],
            'status' => 'menunggu',
        ]);

        $order->load(['user:id,name', 'product:id,name,price,image_url']);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }

    public function show(Order $order)
    {
        if (Auth::check() && (Auth::user()->id === $order->user_id || Auth::user()->role === 'admin')) {
             return response()->json([
                'message' => 'Order fetched successfully',
                'order' => $order->load(['user:id,name', 'product:id,name,price,image_url']),
            ]);
        }
        
        return response()->json(['message' => 'Unauthorized access to this order.'], 403);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,dibatalkan',
        ]);

        $order->update([
            'status' => $validated['status']
        ]);

        $order->load(['user:id,name', 'product:id,name,price,image_url']);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
            'order_id' => $order->id,
        ]);
    }

   public function userOrders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('product:id,name,price,image_url')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'User orders fetched successfully',
            'orders' => $orders,
        ]);
    }

}
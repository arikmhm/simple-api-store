<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    
    public function index()
    {
        return Order::with(['user:id,name', 'product:id,name,price'])->get();
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

    return response()->json($order, 201);
    }

    
    public function show(Order $order)
    {
        return $order->load(['user:id,name', 'product:id,name,price']);
    }

    
    public function update(Request $request, Order $order)
    {
        $request->validate([
        'status' => 'required|in:menunggu,diproses,selesai,dibatalkan',
    ]);

    $order->update([
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Order status updated',
        'order' => $order
    ]);
    }

    
    public function destroy(Order $order)
    {
        $order->delete();

    return response()->json(
        ['message' => 'Order deleted']
    );
    }

   public function userOrders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('product:id,name,price')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

}

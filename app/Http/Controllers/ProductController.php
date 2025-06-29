<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductController extends Controller
{
    // protected $middleware = [
    //     'auth:sanctum' => [
    //         'except' => ['index', 'show'],
    //     ],
    //     'admin' => [
    //         'only' => ['store', 'update', 'destroy'],
    //     ],
    // ];
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'message' => 'Product list fetched successfully',
            'products' => $products,
        ]);
    }


    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('products', 'public');
            $validate['image_url'] = $imagePath;
        }

        $product = Product::create($validate);
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }


    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Product fetched successfully',
            'product' => $product,
        ]);
    }



    public function update(Request $request, Product $product)
    {
        $validate = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:1000',
            'price' => 'sometimes|required|numeric|min:0',
            'image_url' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }

            $image = $request->file('image_url');
            $fileName = $image->hashName();
            $imagePath = 'products/' . $fileName;
            $image->storeAs('products', $fileName, 'public');
            $validate['image_url'] = $imagePath;
        }

        $product->update($validate);
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(){
        return [
            new Middleware(
                'auth:sanctum', 
                except: ['index', 'show']
            ),
        ];
    }
    public function index()
    {
        return Product::all();
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
        $product->refresh();

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,                         
            'image_url' => $product->full_image_url,       
        ], 201);
    }


    public function show(Product $product)
    {
        return response()->json([
            'product' => $product,                         
            'image_url' => $product->full_image_url,       
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
            $image = $request->file('image_url');
            $fileName = $image->hashName(); // unik
            $image->storeAs('products', $fileName, 'public'); // simpan ke folder products
            $validate['image_url'] = 'products/' . $fileName; // simpan path saja
        }


        $product->update($validate);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
            'image_url' => $product->full_image_url,
        ]);
    }
    public function destroy(Product $product)
    {
        $product->delete();
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {

        $products = Product::with('category');

        if (request('category')) {
            $products = Product::whereHas('category', function (Builder $query) {
                $query->where('name', request('category'));
            })->with('category');
        }

        if (request('search')) {
            $products = $products->where('name', 'like', '%' . request('search') . '%');
        }


        return new ProductResource($products->paginate(10));
    }

    public function store(ProductRequest $request)
    {

        $product = new Product();
        $product->code = $request->code;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category;
        $product->is_ready = true;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $product->image = $image->hashName();
        }

        $product->save();

        return new ProductResource($product);
    }

    public function show($id)
    {

        $product = Product::with('category')->findOrFail($id);

        return new ProductResource($product);
    }

    public function update($id, ProductRequest $request)
    {
        $product = Product::findOrFail($id);

        $product->code = $request->code;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category;
        $product->is_ready = true; // Misalnya, asumsikan produk selalu siap saat diupdate

        // Jika ada file gambar yang diunggah, simpan gambar baru
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());
            $product->image = $image->hashName();

            Storage::delete('public/products/' . basename($product->image));
        }

        $product->save();

        return new ProductResource($product);
    }

    public function destroy($id)
    {

        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::delete('public/products/' . basename($product->image));
        }

        $product->delete();

        return response()->json(null, 204);
    }
}

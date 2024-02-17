<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Whoops\Run;

class CartController extends Controller
{
    public function index() {
        $carts = Cart::with('product');
        if(request('productid')) {
            $carts = Cart::where('product_id', request('productid'))->with('product');
        }

        return response()->json([
            'data' => $carts->get()
        ]);;
    }

    public function store (Request $request) {

        Cart::create($request->all());

        return response()->json([
            'data' => 'success'
        ]);
    }

    public function update (Request $request, Cart $id) {

        $id->update([
            'amount' => $request->amount,
            'total_price' => $request->total_price
        ]);

        return response()->json([
            'success' => 'success di ubah'
        ]);
    }

    public function destroy(Request $request) {


        $cart = Cart::where('id', $request->cart)->first();
        $cart->delete();

        return response()->json([
            'status' => 200,
            'message' => 'telah di hapus'
        ], 200);
    }
}


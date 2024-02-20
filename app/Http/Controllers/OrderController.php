<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = order::with('order_detail.product');

        if (request('search')) {
            $orders = $orders->where('invoice', 'like', '%' . request('search') . '%');
        }


        return new OrderResource($orders->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        $products = json_decode($request->products);

        $order = new Order();

        $order->invoice = 'INV' . rand(100, 999) . date('Ymd');
        $order->total_pay = $request->total;
        $order->cash = $request->cash;

        $order->save();


        foreach ($products as $product) {

            $order_detail = new OrderDetail();
            $order_detail->order_id = $order->id;
            $order_detail->product_id = $product->id;
            $order_detail->quantity = $product->quantity;

            $order_detail->save();
        }

        return response()->json([
            'status' => true,
            'data' => $order,
            'message' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('order_detail.product')->findOrFail($id);

        return response()->json([
            'data' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->delete();

        return response()->json(null, 204);
    }
}

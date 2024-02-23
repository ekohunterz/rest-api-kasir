<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:general|read|create'])->only(['index', 'show', 'store']);
        $this->middleware(['permission:delete'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->date;


        $orders = order::with('order_detail.product')->orderBy('created_at', 'desc');

        if (request('search')) {
            $orders = $orders->where('invoice', 'like', '%' . request('search') . '%');
        }

        if (request('date')) {
            $orders = $orders->whereMonth('created_at', Carbon::parse($date)->month)->whereYear('created_at', Carbon::parse($date)->year);
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

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
Use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $role = auth()->user()->role_id;
        $user = auth()->user()->user_id;

        if($role == 1){
            $orders = Order::orderBy('order_status')->get();
        }else if($role == 2){
            $orders = Order::orderBy('order_status')->where('user_id', $user)->get();
        }

        return view('admin.order.index', ["orders" => $orders]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($order_id)
    {
        $details  = OrderDetail::where('order_id', $order_id)->with('product.images')->get();
        return view('admin.order.detail', ["details" => $details]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('admin.order.status', ["orders" => $order]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $order->update($request->all());
        return redirect('order');
    }
}

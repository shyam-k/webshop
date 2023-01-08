<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Http\Resources\OrdersResource;
use Illuminate\Support\Facades\Http;
use App\Payments\PaymentProcessor;


class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrdersResource::collection(Orders::paginate(5));
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
     * @param  \App\Http\Requests\StoreOrdersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrdersRequest $request)
    {            
        $order = Orders::create([
            'customer_id' => $request->customer_id,
            'payed' => 0
        ]);

        return response()->json([
            'status' => true,
            'message' => "Order Created successfully!",
            'order' => $order
        ], 200);
        return new OrdersResource($order); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show(Orders $orders)
    {
        return new OrdersResource($orders);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrdersRequest $request, Orders $orders)
    {
        $orders->update([
            'customer_id' => $request->customer_id,
        ]);

        return new OrdersResource($orders);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy(Orders $orders)
    {
        $orders->delete();
        return response()->json([
            'status' => true,
            'message' => "Order Deleted successfully!",
        ], 200);
    }

    /**
     * add the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, Orders $orders)
    {

       //s dd($orders->products->sum('price'));
        $orders->products()->attach([$request->product_id]);
        
        $orders->update([
            'product_id' => $request->product_id,
        ]);

        return new OrdersResource($orders);
    }

    /**
     * add the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request, Orders $orders)
    {
        $gateway = PaymentProcessor::create('Superpay');
        if(!$orders->payed){
            if($gateway->purchase($orders)){
                $orders->update([
                    'payed' => 1,
                ]);
                return response()->json([
                    'status' => true,
                    'message' => "Payment has been completed successfully!",
                    'order' => $orders
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => "Payment has not been completed successfully!",
                    'order' => $orders
                ], 200);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => "The payment was done already for this order",
                'order' => $orders
            ], 200);
        }
    }
}

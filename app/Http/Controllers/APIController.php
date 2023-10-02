<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    public function getProductList() {
        $products = Product::with(['user', 'category', 'images'])
                        ->orderBy('created_at', 'asc')
                        ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 404,
                'error' => 'No products found in the database',
            ]);
        }

        $data = [];

        foreach ($products as $product) {
            $productImages = $product->images->take(3);

            $productImageData = [];
            foreach ($productImages as $productImage) {
                $productImageData[] = [
                    'image_path' => $productImage->image_path
                ];
            }

            $data[] = [
                'umkm_id' => $product->user->user_id,
                'umkm_name' => $product->user->name,
                'umkm_contact' => $product->user->contact,
                'umkm_account_number' => $product->user->account_number,
                'umkm_account_bank' => $product->user->account_bank,
                'umkm_account_bank_nama' => $product->user->account_bank_name,
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_description' => $product->description,
                'product_stock' => $product->stock,
                'product_price' => $product->price,
                'product_unit' => $product->unit,
                'product_category' => $product->category->category_name,
                'product_images' => $productImageData,
            ];
        }

        return response()->json([
            'status' => 200,
            'error' => null,
            'data' => $data,
        ]);
    }

    public function getProductDetail(Request $request){
        $product = Product::find($request->product_id);

        if(!$product){
            return response()->json([
                'status' => 404,
                'error' => 'No products found in the database',
            ]);
        }

        $data = [];

        $data[] = [
            'umkm_name'                 => $product->user->name,
            'product_id'                => $product->product_id,
            'product_name'              => $product->product_name,
            'product_description'       => $product->description,
            'product_stock'             => $product->stock,
            'product_price'             => $product->price,
            'product_unit'              => $product->unit,
            'product_category'          => $product->category->category_name,
            'product_images'            => [],
            'umkm_recommendations'      => [],
            'category_recommendations'  => []
        ];

        $product_images = ProductImage::where('product_id', $product->product_id)->get();

        if($product_images->isEmpty()){
            return response()->json([
                'status'        => 404,
                'error'         => 'Product image not found',
                'product_id'    => $product->product_id
            ]);
        }

        foreach ($product_images as $product_image) {
            $data[0]['product_images'][] = [
                'product_image_id' => $product_image->product_image_id,
                'image_path' => $product_image->image_path,
            ];
        }

        $umkm_recommendations = Product::latest()->take(5)->where('user_id', $product->user_id)->whereNotIn('product_id', [$product->product_id])->get();

        if($umkm_recommendations->isEmpty()){
            return response()->json([
                'status' => 200,
                'message' => 'No recommendations available from the same store',
                'data' => $data,
            ]);
        }

        foreach ($umkm_recommendations as $umkm_recommendation) {
            $umkm_recommendation_product_image = ProductImage::where('product_id', $umkm_recommendation->product_id)->first();

            if(!$umkm_recommendation_product_image){
                return response()->json([
                    'status'        => 404,
                    'error'         => 'Product image for umkm recommendation not found',
                    'product_id'    => $umkm_recommendation->product_id
                ]);
            }

            $data[0]['umkm_recommendations'][] = [
                'umkm_name'             => $umkm_recommendation->user->name,
                'product_id'            => $umkm_recommendation->product_id,
                'product_name'          => $umkm_recommendation->product_name,
                'product_description'   => $umkm_recommendation->description,
                'product_stock'         => $umkm_recommendation->stock,
                'product_price'         => $umkm_recommendation->price,
                'product_unit'          => $umkm_recommendation->unit,
                'product_image_path'    => $umkm_recommendation_product_image->image_path,
                'product_category'      => $umkm_recommendation->category->category_name
            ];
        }

        $category_recommendations = Product::latest()->take(5)->where('category_id', $product->category_id)->whereNotIn('product_id', [$product->product_id])->get();

        if($category_recommendations->isEmpty()){
            return response()->json([
                'status' => 200,
                'message' => 'No recommendations available from the same category',
                'data' => $data,
            ]);
        }

        foreach ($category_recommendations as $category_recommendation) {
            $category_recommendation_product_image = ProductImage::where('product_id', $category_recommendation->product_id)->first();

            if(!$category_recommendation_product_image){
                return response()->json([
                    'status'        => 404,
                    'error'         => 'Product image for umkm recommendation not found',
                    'product_id'    => $category_recommendation->product_id
                ]);
            }

            $category_recommendation_price = $category_recommendation->price;
            $category_recommendation_formattedPrice = number_format($category_recommendation_price, 0, ',', '.');

            $data[0]['category_recommendations'][] = [
                'umkm_name'             => $category_recommendation->user->name,
                'product_id'            => $category_recommendation->product_id,
                'product_name'          => $category_recommendation->product_name,
                'product_description'   => $category_recommendation->description,
                'product_stock'         => $category_recommendation->stock,
                'product_price'         => $category_recommendation->price,
                'product_unit'          => $category_recommendation->unit,
                'product_image_path'    => $category_recommendation_product_image->image_path,
                'product_category'      => $category_recommendation->category->category_name
            ];
        }

        return response()->json([
            'status' => 200,
            'error' => null,
            'data' => $data,
        ]);
    }

    public function placeOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:50',
            'customer_phone' => 'required|max:15',
            'shipping_address' => 'required',
            'postal_code' => 'required',
            'umkm_id' => 'required',
            'order_details' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $order_code         = $this->generateOrderCode();
        $order_date         = Carbon::now()->format('Y-m-d');
        $order_details      = $request->order_details;
        $price_amount       = 0;
        $order_status       = 1;
        $customer_name      = $request->customer_name;
        $customer_phone     = $request->customer_phone;
        $shipping_address   = $request->shipping_address;
        $postal_code        = $request->postal_code;
        $umkm_id            = $request->umkm_id;

        try{
            $order = Order::create([
                'order_code'        => $order_code,
                'order_date'        => $order_date,
                'order_status'      => $order_status,
                'customer_name'     => $customer_name,
                'customer_phone'    => $customer_phone,
                'shipping_address'  => $shipping_address,
                'postal_code'       => $postal_code,
                'user_id'           => $umkm_id
            ]);

            foreach($order_details as $order_detail){
                $product_id = $order_detail['product_id'];

                $product = Product::find($product_id);

                if(!$product){
                    return response()->json([
                        'status' => 404,
                        'error' => 'No products found in the database',
                    ]);
                }

                $product_price = $product->price;
                $product_amount = $order_detail['amount'];
                $product_subtotal = ($product_price * $product_amount);
                $price_amount += $product_subtotal;

                $order_id = $order->order_id;

                try{
                    OrderDetail::create([
                        'product_id'        => $product_id,
                        'price'             => $product_price,
                        'amount'            => $product_amount,
                        'subtotal'          => $product_subtotal,
                        'order_id'          => $order_id
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to save order detail data',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            $order->price_amount = $price_amount;
            $order->save();

            $data =[
                'order_id'          => $order->order_id,
                'order_code'        => $order->order_code,
                'order_date'        => $order->order_date,
                'order_status'      => $order->order_status,
                'customer_name'     => $order->customer_name,
                'customer_phone'    => $order->customer_phone,
                'shipping_address'  => $order->shipping_address,
                'postal_code'       => $order->postal_code
            ];

            return response()->json([
                'status' => 201,
                'error' => null,
                'data' => $data,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save order data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateOrderCode(){
        $counter = Order::max('order_id') + 1;
        $orderCode = 'ORD' . str_pad($counter, 5, '0', STR_PAD_LEFT);
        return $orderCode;
    }

    public function orderHistory(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:50',
            'customer_phone' => 'required|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $customer_name  = $request->customer_name;
        $customer_phone = $request->customer_phone;
        $data           = [];

        $orders = Order::where('customer_name', $customer_name)->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No Order data for user with name'.' '.$customer_name,
            ], 200);
        }

        $ordersFiltered = $orders->where('customer_phone', $customer_phone);

        if ($ordersFiltered->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No Order data not for user with name '.$customer_name.' and phone number '.$customer_phone,
            ], 200);
        }

        if($ordersFiltered->isEmpty()){
            return response()->json([
                'status' => 'success',
                'message' => 'No order history data found',
                'data' => []
            ], 200);
        }

        $ordersFiltered = $ordersFiltered->sortBy('order_status');

        foreach($ordersFiltered as $order){
            $item_amount = OrderDetail::where('order_id', $order->order_id)->count();

            $order_detail   = OrderDetail::where('order_id', $order->order_id)->first();
            $product_image  = ProductImage::where('product_id', $order_detail->product->product_id)->first();

            $data[] = [
                'order_id'          => $order->order_id,
                'order_code'        => $order->order_code,
                'product_image'     => $product_image->image_path,
                'item_amount'       => $item_amount,
                'order_status'      => $order->order_status
            ];
        }

        return response()->json([
            'status' => 'success',
            'error' => null,
            'data' => $data
        ], 200);
    }

    public function activeOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:50',
            'customer_phone' => 'required|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $customer_name  = $request->customer_name;
        $customer_phone = $request->customer_phone;
        $data           = [];

        $orders = Order::where('customer_name', $customer_name)->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order data not found for user with name'.' '.$customer_name,
            ], 404);
        }

        $ordersFiltered = $orders->where('customer_phone', $customer_phone);

        if ($ordersFiltered->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order data not found for user with name '.$customer_name.' and phone number '.$customer_phone,
            ], 404);
        }

        $ordersFiltered = $ordersFiltered->whereIn('order_status', ['1', '2', '3']);

        if($ordersFiltered->isEmpty()){
            return response()->json([
                'status' => 'success',
                'message' => 'No active order data found',
                'data' => []
            ], 200);
        }

        $ordersFiltered = $ordersFiltered->sortByDesc('created_at');

        foreach($ordersFiltered as $order){
            $item_amount = OrderDetail::where('order_id', $order->order_id)->count();

            $order_detail   = OrderDetail::where('order_id', $order->order_id)->first();
            $product_image  = ProductImage::where('product_id', $order_detail->product->product_id)->first();

            $data[] = [
                'order_id'          => $order->order_id,
                'order_code'        => $order->order_code,
                'product_image'     => $product_image->image_path,
                'item_amount'       => $item_amount,
                'order_status'      => $order->order_status
            ];
        }

        return response()->json([
            'status' => 'success',
            'error' => null,
            'data' => $data
        ], 200);
    }

    public function orderDetail(Request $request){
        $order = Order::with('orderDetails')->find($request->order_id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order data not found',
            ], 404);
        }

        $order_id           = $order->order_id;
        $order_code         = $order->order_code;
        $order_date         = $order->order_date;
        $price_amount       = $order->price_amount;
        $order_status       = $order->order_status;
        $customer_name      = $order->customer_name;
        $customer_phone     = $order->customer_phone;
        $shipping_address   = $order->shipping_address;
        $subdistrict        = $order->subdistrict;
        $district           = $order->district;
        $city               = $order->city;
        $province           = $order->province;
        $postal_code        = $order->postal_code;
        $order_details      = $order->orderDetails->toArray();

        $data[] = [
            'order_id'          => $order_id,
            'order_code'        => $order_code,
            'order_date'        => $order_date,
            'price_amount'      => $price_amount,
            'order_status'      => $order_status,
            'customer_name'     => $customer_name,
            'customer_phone'    => $customer_phone,
            'shipping_address'  => $shipping_address,
            'subdistrict'       => $subdistrict,
            'district'          => $district,
            'city'              => $city,
            'province'          => $province,
            'postal_code'       => $postal_code,
            'order_details'     => []
        ];

        foreach($order_details as $order_detail){
            $orderDetailModel = new OrderDetail($order_detail);
            $product = $orderDetailModel->product;

            $product_image = ProductImage::where('product_id', $order_detail['product_id'])->first();

            if(!$product_image){
                return response()->json([
                    'status'        => 404,
                    'error'         => 'Product image not found',
                    'product_id'    => $product->product_id
                ]);
            }

            $data[0]['order_details'][] = [
                'product_id'            => $order_detail['product_id'],
                'product_name'          => $product->product_name,
                'product_image'         => $product_image->image_path,
                'order_amount'          => $order_detail['amount'],
                'product_price'         => $product->price,
                'subtotal'              => $order_detail['subtotal']
            ];
        }

        return response()->json([
            'status' => 'success',
            'error' => null,
            'data' => $data
        ], 200);
    }

    public function changeStatus(Request $request){
        $order_status   = $request->order_status;
        $order_id       = $request->order_id;

        $order = Order::find($order_id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order data not found',
            ], 404);
        }

        if($order_status == 1){
            $order->update([
                'order_status'  => 5,
            ]);
        }else if($order_status == 2){
            $order->update([
                'order_status'  => 4,
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status provided',
            ], 400);
        }

        $data =[
            'order_id'          => $order->order_id,
            'order_code'        => $order->order_code,
            'order_date'        => $order->order_date,
            'order_status'      => $order->order_status,
            'customer_name'     => $order->customer_name,
            'customer_phone'    => $order->customer_phone,
            'shipping_address'  => $order->shipping_address,
            'subdistrict'       => $order->subdistrict,
            'district'          => $order->district,
            'city'              => $order->city,
            'province'          => $order->province,
            'postal_code'       => $order->postal_code
        ];

        return response()->json([
            'status' => 200,
            'error' => null,
            'data' => $data,
        ], 200);
    }

    public function userOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:50',
            'customer_phone' => 'required|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $customer_name  = $request->customer_name;
        $customer_phone = $request->customer_phone;
        $data           = [];

        $orders = Order::where('customer_name', $customer_name)->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order data not found for user with name'.' '.$customer_name,
            ], 404);
        }

        $ordersFiltered = $orders->where('customer_phone', $customer_phone);

        if ($ordersFiltered->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order data not found for user with name '.$customer_name.' and phone number '.$customer_phone,
            ], 404);
        }

        $ordersFiltered = $ordersFiltered->sortBy('order_status');

        foreach($ordersFiltered as $order){
            $item_amount = OrderDetail::where('order_id', $order->order_id)->sum('amount');
            $price_amount = OrderDetail::where('order_id', $order->order_id)->sum('subtotal');
            $base_price = OrderDetail::where('order_id', $order->order_id)->sum('price');

            $order_detail   = OrderDetail::where('order_id', $order->order_id)->first();
            $product_image  = ProductImage::where('product_id', $order_detail->product->product_id)->first();

            $data[] = [
                'order_id'          => $order->order_id,
                'order_code'        => $order->order_code,
                'product_image'     => $product_image->image_path,
                'item_amount'       => $item_amount,
                'price_amount'      => $price_amount,
                'base_price'        => $base_price,
                'order_status'      => $order->order_status,
                'product_name'      => $order_detail->product->product_name,
                'product_unit'      => $order_detail->product->unit,
            ];
        }

        return response()->json([
            'status' => 'success',
            'error' => null,
            'data' => $data
        ], 200);
    }
}

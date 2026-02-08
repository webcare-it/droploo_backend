<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Dropshipper;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function confirmOrder (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'delivery_area' => 'required|numeric',
            'customer_address' => 'required|string|min:10',
            'price' => 'required|numeric|min:0',
            'product_quantity' => 'required|integer',
            'payment_type' => 'required|string',
            'order_type' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.size' => 'sometimes',
            'products.*.color' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            //Check Product Type...
            $data = $request->all();
            $firstProductId = $data['products'][0]['id'];
            $product = Product::find($firstProductId);

            // Create Order
            $order = new Order();
            if($product->b_product_id == null){
                $order->is_dropshipping = false;
            }
            if($product->b_product_id != null){
                $order->is_dropshipping = true;
            }
            $order->name = $request->customer_name;
            $order->phone = $request->customer_phone;
            $order->area = $request->delivery_area;
            $order->address = $request->customer_address;
            $order->orderId = $order->invoiceNumber();
            $order->price = $request->price;
            $order->qty = $request->product_quantity;
            $order->payment_type = $request->payment_type;
            $order->order_type = $request->order_type;

            $customerCheck = Order::where('phone', $request->customer_phone)->first();
            $order->customer_type = $customerCheck ? 'Old Customer' : 'New Customer';

            // Assign to employee
            $users = Admin::where('name', '!=', 'admin')->where('is_active', 1)
                ->whereDate('limit_updated_at', '!=', \Illuminate\Support\Carbon::today())->get();

            $session_user = Session::get('id');
            if ($session_user && session('name') != 'admin') {
                $order->employee_id = $session_user;
            } elseif ($users->isNotEmpty()) {
                $randomUser = $users->random();
                $order->employee_id = $randomUser->id;

                $assigned_employee_order = Order::where('employee_id', $randomUser->id)
                    ->whereDate('created_at', \Illuminate\Support\Carbon::today())
                    ->count();

                if ($assigned_employee_order >= $randomUser->order_limit) {
                    $randomUser->is_limit = true;
                    $randomUser->limit_updated_at = now();
                    $randomUser->save();
                }
            } else {
                $admin = Admin::first();
                $order->employee_id = $admin->id;
            }

            $order->save();

            // Create Order Details
            foreach ($request->products as $productData) {
                $productOrder = new OrderDetails();
                $productOrder->order_id = $order->id;
                $productOrder->product_id = $productData['id'];
                $productOrder->qty = $productData['qty'];
                $productOrder->price = $productData['price'];
                $productOrder->size = $productData['size'] ?? null;
                $productOrder->color = $productData['color'] ?? null;
                $productOrder->save();
            }

            //Delete Cart Products...
            $cartProducts = Cart::where('ip_address', $request->ip_address)->get();
            foreach($cartProducts as $product){
                $product->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm order. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            // Order creation temporarily disabled
            return response()->json([
                'status'  => 'error',
                'message' => 'আসসালামু আলাইকুম। প্রিয় ড্রপশিপার, বাংলাদেশের জাতীয় নির্বাচন উপলক্ষে আজ ৮ তারিখ থেকে ১৩ তারিখ পর্যন্ত ড্রপশিপিং-এর নতুন অর্ডার গ্রহণ সাময়িকভাবে বন্ধ থাকবে। তাই যারা এড রান করছেন, অনুগ্রহ করে এই সময়ের জন্য বন্ধ রাখবেন। বর্তমানে আমাদের হাতে ১৫০+ পেন্ডিং অর্ডার রয়েছে। ইনশাআল্লাহ আজ ও আগামীকালের মধ্যে সবগুলো অর্ডার ডেলিভারি সম্পন্ন করা হবে। পরবর্তী নোটিশ অনুযায়ী পুনরায় সকল কার্যক্রম শুরু করা হবে, ইনশাআল্লাহ।'
            ], 503);

            // Step 1: Validate dropshipper auth headers
            $dropshipper = Dropshipper::where('app_key', $request->header('App-Key'))
                ->where('app_secret', $request->header('App-Secret'))
                ->where('user_name', $request->header('Username'))
                ->where('is_approved', 1)
                ->first();

            if (!$dropshipper) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized dropshipper credentials.'
                ], 401);
            }

            // Step 2: Get dropshipper info
            $dropshipperInfoResponse = Http::withHeaders([
                'App-Secret' => $dropshipper->app_secret,
                'App-Key'    => $dropshipper->app_key,
                'Username'   => $dropshipper->user_name,
            ])->get('http://dropshipper.droploo.com/api/dropshipper/info');

            if (!$dropshipperInfoResponse->ok()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to fetch dropshipper info.',
                    'details' => $dropshipperInfoResponse->body()
                ], $dropshipperInfoResponse->status());
            }

            $dropshipperData = $dropshipperInfoResponse['dropshipper'] ?? null;

            if (!$dropshipperData) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dropshipper info is invalid.'
                ], 400);
            }

            // Step 3: Minimum delivery cost
            if ($request->delivery_cost < 60) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Delivery cost must be at least 60.'
                ], 400);
            }

            // Step 4: Calculate deduction before saving order
            $deductAmount = $request->delivery_cost; // base delivery charge
            
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                
                if ($product) {
                    $wholesalePrice = $product->wholesale_price;
                    $sellingPrice = $productData['price'];
                    $quantity = $productData['qty'];

                    // If product is variable, get wholesale price from productImages table
                    if ($product->is_variable == 1) {
                        $productImage = ProductImage::where('size', $productData['size'])
                            ->where('product_id', $productData['id'])
                            ->first();
                        
                        if ($productImage && isset($productImage->wholesale_price)) {
                            $wholesalePrice = $productImage->wholesale_price;
                        }
                    }

                    // Calculate difference: if selling price < wholesale price, add to deduction
                    $priceDifference = $wholesalePrice - $sellingPrice;
                    
                    if ($priceDifference > 0) {
                        $deductAmount += ($priceDifference * $quantity);
                    }
                }
            }

            // Step 4.1: Validate all products before creating order
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);

                if (!$product || $product->status == 0) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "Product with ID {$productData['id']} is not available."
                    ], 400);
                }
            }


            // Step 5: Check if dropshipper has enough balance
            if ((int)$dropshipperData['balance'] < $deductAmount) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Insufficient balance for delivery + wholesale adjustment.',
                    'required_amount' => $deductAmount,
                    'current_balance' => $dropshipperData['balance'],
                ], 400);
            }

            // Step 6: Save order only if balance check passed
            $order = Order::where('orderId', $request->invoice_number)->first();

            if (!$order) {
                $order = new Order();
                $order->orderId = $request->invoice_number;
            }

            $order->name                 = $request->customer_name;
            $order->phone                = $request->customer_phone;
            $order->area                 = $request->delivery_cost;
            $order->address              = $request->customer_address;
            $order->price                = $request->price;
            $order->discount             = $request->discount ?? 0;
            $order->advance              = $request->advance ?? 0;
            $order->qty                  = $request->product_quantity;
            $order->payment_type         = $request->payment_type;
            $order->delivery_charge_type = $request->delivery_charge_type;
            $order->order_type           = $request->order_type;
            $order->customer_type        = 'guest';
            $order->pathao_special_note  = $request->special_notes ?? null;
            $order->payment_gateway      = $request->payment_gateway ?? null;
            $order->transaction_id       = $request->transaction_id ?? null;
            $order->order_status         = 'pending';
            $order->dropshipper_id       = $dropshipper->dropshipper_id;
            $order->save();

            // Clear old details
            OrderDetails::where('order_id', $order->id)->delete();

            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                $details = new OrderDetails();
                $details->order_id   = $order->id;
                $details->product_id = $product ? $product->id : null;
                $qty = isset($productData['qty']) && $productData['qty'] > 0 ? $productData['qty'] : 1;
                $details->price      = $productData['price'];
                $details->color      = $productData['color'] ?? null;
                $details->size       = $productData['size'] ?? null;
                $details->qty        = $productData['qty'];
                $details->save();
            }

            // Step 7: Deduct final amount
            $balanceResponse = Http::withHeaders([
                'App-Secret' => $dropshipper->app_secret,
                'App-Key'    => $dropshipper->app_key,
                'Username'   => $dropshipper->user_name,
            ])->post('https://dropshipper.droploo.com/api/dropshipper/update-balance', [
                'amount'         => $deductAmount,
                'type'           => 'debit',
                'reason'         => 'Delivery + wholesale adjustment for invoice #' . $order->orderId,
                'invoice_number' => $order->orderId,
            ]);

            if (!$balanceResponse->ok()) {
                Log::warning('Failed to deduct balance.', [
                    'invoice' => $order->orderId,
                    'status'  => $balanceResponse->status(),
                    'body'    => $balanceResponse->body()
                ]);
            }

            return response()->json([
                'status'   => 'success',
                'message'  => $order->wasRecentlyCreated ? 'Order created successfully.' : 'Order updated successfully.',
                'order_id' => $order->id,
                'deducted' => $deductAmount,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }





    public function orderDetails ($orderId)
    {
        try {
            $order = Order::with('orderDetails')->where('orderId', $orderId)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => [
                    'order' => $order
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

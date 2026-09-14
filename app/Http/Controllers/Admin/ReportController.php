<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Dropshipper;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Notification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Codeboxr\PathaoCourier\Facade\PathaoCourier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Exports\OrdersExport;
use App\Exports\AllOrdersExport;
use App\Models\ProductImage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class ReportController extends Controller
{
    public function ordersReport(Request $request)
    {
        $sql = OrderDetails::with('product', 'order')->orderBy('created_at', 'desc');

        if (isset($request->from)) {
            $sql->whereDate('created_at', '>=', $request->from);
        }
        if (isset($request->to)) {
            $sql->whereDate('created_at', '<=', $request->to);
        }

        $ordersReports = $sql->paginate(10);
        return view('admin.customer.report', compact('ordersReports'));
    }

    public function exportOrdersForm()
    {
        return view('admin.customer.export-orders-form');
    }

    public function exportOrders(Request $request)
    {
        // Validate the request
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'order_status' => 'nullable|string',
            'columns' => 'nullable|array',
        ]);

        // Build the query
        $sql = Order::with('orderDetails', 'admin')->orderBy('created_at', 'desc');

        // Apply date range filter
        if ($request->from) {
            $sql->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $sql->whereDate('created_at', '<=', $request->to);
        }

        // Apply order status filter
        if ($request->order_status) {
            $sql->where('order_status', $request->order_status);
        }

        // Get the orders
        $orders = $sql->get();

        // Extract order IDs
        $orderIds = $orders->pluck('id')->toArray();

        // Get selected columns
        $selectedColumns = $request->columns ?? [
            'ItemType(*)',
            'StoreName(*)',
            'MerchantOrderId',
            'RecipientName(*)',
            'RecipientPhone(*)',
            'RecipientCity(*)',
            'RecipientZone(*)',
            'RecipientArea',
            'RecipientAddress(*)',
            'AmountToCollect(*)',
            'ItemQuantity(*)',
            'ItemWeight(*)',
            'ItemDesc',
            'SpecialInstruction',
        ];

        // Export the orders
        return Excel::download(new OrdersExport($orderIds, $selectedColumns), 'orders-export-' . now()->format('Y-m-d') . '.csv');
    }

    public function ordersCancel(Request $request){
         if(session('name') == 'admin'){
            $sql = Order::with('product', 'orderDetails')->where('order_status', 'cancel')->where('is_deleted', '!=', true)->orderBy('created_at', 'desc');
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                });
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }

        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'cancel')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                });
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        $cancel_orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-cancel', compact('cancel_orders', 'users'));
    }

    public function ordersHold(Request $request){
        $sql = Order::with('product', 'orderDetails')->where('order_status', 'hold')->where('is_deleted', '!=', true)->orderBy('created_at', 'desc');

        if (isset($request->search)) {
            $searchTerm = $request->search;
            $sql->where(function ($query) use ($searchTerm) {
            $query->orWhere('phone', $searchTerm)
            ->orWhere('orderId', $searchTerm);
            })->get();
            //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
        }

        $hold_orders = $sql->paginate(50);
        return view('admin.customer.order-hold', compact('hold_orders'));
    }

    public function ordersPending(Request $request){
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin', 'dropshipper')->orderBy('id', 'desc')->where('order_status', 'pending')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'pending')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-pending', compact('orders', 'users'));
    }

    public function ordersComplete(Request $request){
        $sql = Order::with('product', 'orderDetails')->where('status', 3)->orderBy('created_at', 'desc');

        if (isset($request->search)) {
            $sql->where('orderId', $request->search)->where('status', 3);
        }
        $complete_orders = $sql->paginate(50);
        return view('admin.customer.order-complete', compact('complete_orders'));
    }

    public function ordersDelivery(Request $request){
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'delivered')
            ->where('order_type', '!=', 'dropshipping')->where(function ($query) {
                $query->whereNull('pathao_order_status')
                ->orWhere('pathao_order_status', 'Delivered');
                })->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'delivered')
            ->where('is_deleted', '!=', true)
            ->where('order_type', '!=', 'dropshipping')
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $delivered_orders = $sql->paginate(50);
        // dd($delivered_orders);
        return view('admin.customer.delivery-order-list', compact('delivered_orders'));
    }

    public function dropshipperOrdersDelivery(Request $request){
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'delivered')
            ->where('order_type', 'dropshipping')->where(function ($query) {
                $query->whereNull('pathao_order_status')
                ->orWhere('pathao_order_status', 'Delivered');
                })->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'delivered')
            ->where('is_deleted', '!=', true)
            ->where('order_type', 'dropshipping')
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $delivered_orders = $sql->paginate(50);
        // dd($delivered_orders);
        return view('admin.customer.dropshipper-delivery-order-list', compact('delivered_orders'));
    }

    public function pendingPaymentOrder (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'pending payment')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'pending payment')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $pending_payment_orders = $sql->paginate(50);
        return view('admin.customer.pending-payment-order-list', compact('pending_payment_orders'));
    }

    //==================== Order status =========================//
    public function showCancelReasonForm ($orderId)
    {
        $order_status = 'cancel';
        return view('admin.customer.hold-cancel-form', compact('orderId', 'order_status'));
    }

    public function cancel(Request $request)
    {
        $cancelOrderStatus = Order::find($request->orderId);
        $cancelOrderStatus->order_status = 'cancel';
        $cancelOrderStatus->notes = $request->notes;
        $cancelOrderStatus->save();

         // Send SMS to dropshipper if order belongs to a dropshipper
        if ($cancelOrderStatus->dropshipper_id) {
            $dropshipper = $cancelOrderStatus->dropshipper;
            if ($dropshipper && $dropshipper->phone) {
                $smsService = new SmsService();
                $smsService->sendCancelNotification($dropshipper->phone, $cancelOrderStatus->orderId);
            }
        }

        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$cancelOrderStatus->orderId.' '. 'is made status cancel by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $cancelOrderStatus->notification()->save($notification);
        //Notification...


        return redirect('/order/cancel')->with('success', 'Order has been canceled');
    }

    public function showHoldReasonForm ($orderId)
    {
        $order_status = 'hold';
        return view('admin.customer.hold-cancel-form', compact('orderId', 'order_status'));
    }

    public function hold(Request $request)
    {
        $cancelOrderStatus = Order::find($request->orderId);
        $cancelOrderStatus->order_status = 'hold';
        $cancelOrderStatus->notes = $request->notes;
        $cancelOrderStatus->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$cancelOrderStatus->orderId.' '. 'is made status hold by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $cancelOrderStatus->notification()->save($notification);
        //Notification...
        return redirect('/order/hold')->with('success', 'Order has been holded');
    }

    public function pendingStatus($id)
    {
        $pendingOrderStatus = Order::find($id);
        $pendingOrderStatus->order_status = 'pending';
        $pendingOrderStatus->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$pendingOrderStatus->orderId.' '. 'is made status pending by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $pendingOrderStatus->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order has been pending');
    }


    public function statusUpdate(Request $request)
    {
        $id = $request->id;
        if($request->id == null){
            return redirect()->back()->withError('Please select minimum one.');
        }
        if ($request->action == 'pending'){
           $completeOrderStatusUpdate = Order::whereIn('id', $id)->get();
            foreach ($completeOrderStatusUpdate as $item) {
                $item->order_status = 'pending';
                $item->save();
                //Notification...
                $notification = new Notification();
                $notification->message = 'Order with invoice id'.' '.$item->orderId.' '. 'is made status pending by'.' '.Session::get('name');
                $notification->specific_user_id = Session::get('id');
                $notification->notification_for = "user";
                $item->notification()->save($notification);
                //Notification...
           }
            return redirect()->back()->with('success', 'Order has been pending');
        }elseif ($request->action == 'hold'){
            $holdOrderStatusUpdate = Order::whereIn('id', $id)->get();
            foreach ($holdOrderStatusUpdate as $item) {
                $item->order_status = 'hold';
                $item->save();
                //Notification...
                $notification = new Notification();
                $notification->message = 'Order with invoice id'.' '.$item->orderId.' '. 'is made status hold by'.' '.Session::get('name');
                $notification->specific_user_id = Session::get('id');
                $notification->notification_for = "user";
                $item->notification()->save($notification);
                //Notification...
            }
            return redirect()->back()->with('success', 'Order has been hold');
        }elseif ($request->action == 'cancel'){
            $cancelOrderStatusUpdate = Order::whereIn('id', $id)->get();
            foreach ($cancelOrderStatusUpdate as $item) {
                $item->order_status = 'cancel';
                $item->save();
                //Notification...
                $notification = new Notification();
                $notification->message = 'Order with invoice id'.' '.$item->orderId.' '. 'is made status cancel by'.' '.Session::get('name');
                $notification->specific_user_id = Session::get('id');
                $notification->notification_for = "user";
                $item->notification()->save($notification);
                //Notification...
            }
            return redirect()->back()->with('success', 'Order has been cancel');
        } else {
            $deleteOrderStatusUpdate = Order::whereIn('id', $id)->get();
            foreach ($deleteOrderStatusUpdate as $item) {
                // $item->delete();
                $item->is_deleted = true;
                $item->save();
                //Notification...
                $notification = new Notification();
                $notification->message = 'Order with invoice id'.' '.$item->orderId.' '. 'is deleted by'.' '.Session::get('name');
                $notification->specific_user_id = Session::get('id');
                $notification->notification_for = "user";
                $item->notification()->save($notification);
                //Notification...
            }
            return redirect()->back()->with('success', 'Order has been deleted');
        }
    }

    public function orderReturn($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'return';
        $orderReturn->pathao_order_status = 'Return';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status return by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }
    public function orderDamage($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'damage';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status damage by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function orderMissing($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'missing';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status missing by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }
    public function orderDelivered($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'delivered';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status delivered by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function orderCustomerConfirm ($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'customer confirm';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status Customer Confirm by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function orderRequestReturn ($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'request return';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status request to return by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function orderPaid ($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'paid';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status paid by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function pendingPayment ($id)
    {
        $orderReturn = Order::find($id);
        $orderReturn->order_status = 'pending payment';
        $orderReturn->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$orderReturn->orderId.' '. 'is made status pending payment by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderReturn->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function invoiceChecked ($id)
    {
        $invoiceChecked = Order::find($id);
        $invoiceChecked->order_status = 'invoice checked';
        $invoiceChecked->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$invoiceChecked->orderId.' '. 'is made status invoice Checked by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $invoiceChecked->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function invoiced ($id)
    {
        $invoiced = Order::find($id);
        $invoiced->order_status = 'invoiced';
        $invoiced->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$invoiced->orderId.' '. 'is made status invoiced by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $invoiced->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function stockOut ($id)
    {
        $stockOut = Order::find($id);
        $stockOut->order_status = 'stock out';
        $stockOut->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$stockOut->orderId.' '. 'is made status stock out by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $stockOut->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function scheduleDelivery ($id)
    {
        $scheduleDelivery = Order::find($id);
        $scheduleDelivery->order_status = 'schedule delivery';
        $scheduleDelivery->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$scheduleDelivery->orderId.' '. 'is made status schedule delivery by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $scheduleDelivery->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order status has been updated');
    }

    public function completeStatus($id)
    {
        $cancelOrderStatus = Order::find($id);
        $cancelOrderStatus->order_status = 'complete';
        $cancelOrderStatus->save();
        //Notification...
        $notification = new Notification();
        $notification->message = 'Order with invoice id'.' '.$cancelOrderStatus->orderId.' '. 'is made status complete by'.' '.Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $cancelOrderStatus->notification()->save($notification);
        //Notification...
        return redirect()->back()->with('success', 'Order has been complete');
    }
    public function deliveredStatus($id)
    {
        $orderStatus = Order::with('dropshipper', 'orderDetails.product')->find($id);
        $orderStatus->order_status = 'delivered';
        $orderStatus->save();

        $appKey    = $orderStatus->dropshipper->app_key;
        $appSecret = $orderStatus->dropshipper->app_secret;
        $userName  = $orderStatus->dropshipper->user_name;
        $invoice_number = $orderStatus->orderId;

        // Step 1: Calculate total wholesale cost
        $totalWholesaleCost = 0;

        foreach ($orderStatus->orderDetails as $detail) {
            $product = $detail->product;
            if ($product) {
                $wholesalePrice = $product->wholesale_price;

                if ($product->is_variable == 1) {
                    $productImage = ProductImage::where('size', $detail->size)
                        ->where('product_id', $detail->product_id)
                        ->first();
                    if ($productImage && isset($productImage->wholesale_price)) {
                        $wholesalePrice = $productImage->wholesale_price;
                    }
                }

                $totalWholesaleCost += ($wholesalePrice ?? 0) * $detail->qty;
            }
        }
        $orderTotal = (float)$orderStatus->price - (float)$orderStatus->area;

        // Step 2: Calculate profit
        $grandTotal = $orderTotal - (float)$totalWholesaleCost;
        $profit_amount = $grandTotal + (float)$orderStatus->area;


        // Step 3: Send profit to balance API
        $balanceResponse = Http::withHeaders([
            'App-Secret' => $appSecret,
            'App-Key'    => $appKey,
            'Username'   => $userName,
        ])->post('https://dropshipper.droploo.com/api/dropshipper/profit/update', [
            'amount'         => $profit_amount,
            'type'           => 'credit',
            'reason'         => 'Profit balance add for invoice #' . $invoice_number,
            'invoice_number' => $invoice_number,
        ]);

        if (!$balanceResponse->ok()) {
            Log::warning('Profit balance add failed for invoice #' . $invoice_number, [
                'status' => $balanceResponse->status(),
                'body'   => $balanceResponse->body(),
            ]);
        }

        // Send SMS to dropshipper
        if ($orderStatus->dropshipper && $orderStatus->dropshipper->phone) {
            $smsService = new SmsService();
            $smsService->sendDeliveredNotification($orderStatus->dropshipper->phone, $invoice_number);
        }

        // Notification
        $notification = new Notification();
        $notification->message = 'Order with invoice id ' . $orderStatus->orderId . ' is made status complete by ' . Session::get('name');
        $notification->specific_user_id = Session::get('id');
        $notification->notification_for = "user";
        $orderStatus->notification()->save($notification);


        return redirect()->back()->with('success', 'Order has been completed');
    }

    public function invoiceList (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->orderBy('id', 'desc')
            ->where(function ($query) {
                $query->where('order_status', 'complete')
                ->orWhere('order_status', 'delivered');
            })
            ->where('order_type', '!=', 'dropshipping')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
            //Searching...
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where(function ($query) {
                $query->where('order_status', 'complete')
                ->orWhere('order_status', 'delivered');
            })
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc')->where('order_type', '!=', 'dropshipping');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(100);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.invoice_orders', compact('orders', 'users'));
    }

    public function dropshipperInvoiceList (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin', 'dropshipper')->orderBy('id', 'desc')
            ->where(function ($query) {
                $query->where('order_status', 'complete')
                ->orWhere('order_status', 'delivered');
            })
            ->where('order_type', 'dropshipping')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
            //Searching...
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin', 'dropshipper')
            ->where('employee_id', $employee_id)
            ->where(function ($query) {
                $query->where('order_status', 'complete')
                ->orWhere('order_status', 'delivered');
            })
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc')->where('order_type', 'dropshipping');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(100);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.dropshipper_invoice_orders', compact('orders', 'users'));
    }

    public function allOrders(Request $request)
    {
        $sql = $this->buildAllOrdersQuery($request);
        $all_orders = $sql->paginate(100);

        $users = Admin::orderBy('id', 'desc')
            ->where('id', '!=', session()->get('id'))
            ->get();

        return view('admin.customer.order-list', compact('all_orders', 'users'));
    }

    private function buildAllOrdersQuery(Request $request)
    {
        if (session('name') == 'admin') {
            $sql = Order::with('orderDetails', 'admin')
                ->orderBy('id', 'desc')
                ->where('is_deleted', '!=', true);

            if (!empty($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                    $query->where('orderId', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                });
            }

            if (!empty($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }

            if (!empty($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }

            if (!empty($request->user_id)) {
                $sql->where('employee_id', (int)$request->user_id);
            }
        } else {
            $employee_id = Session::get('id');

            $sql = Order::with('orderDetails', 'admin')
                ->where('employee_id', $employee_id)
                ->orderBy('created_at', 'desc')
                ->where('is_deleted', '!=', true);

            if (!empty($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                    $query->where('phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('orderId', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                });
            }

            if (!empty($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }

            if (!empty($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
        }

        return $sql;
    }

    public function exportAllOrdersExcel(Request $request)
    {
        $sql = $this->buildAllOrdersQuery($request);

        return Excel::download(new AllOrdersExport($sql), 'all-orders-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportAllOrdersCsv(Request $request)
    {
        $sql = $this->buildAllOrdersQuery($request);

        return Excel::download(new AllOrdersExport($sql), 'all-orders-' . now()->format('Y-m-d') . '.csv');
    }

    public function exportAllOrdersChunked(Request $request)
    {
        $request->validate([
            'per_file' => 'required|integer|min:10|max:5000',
            'format' => 'required|in:csv,excel',
        ]);

        $perFile = (int) $request->per_file;
        $format = $request->format;
        $sql = $this->buildAllOrdersQuery($request);
        $totalCount = (clone $sql)->count();

        if ($totalCount === 0) {
            return redirect()->back()->with('error', 'No orders found to export.');
        }

        $totalFiles = ceil($totalCount / $perFile);
        $dateStr = now()->format('Y-m-d');
        $ext = $format === 'excel' ? 'xlsx' : 'csv';
        $zipFileName = "all-orders-{$dateStr}-{$totalCount}-orders.zip";
        $tempDir = storage_path('app/export_temp_' . uniqid());

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        for ($page = 0; $page < $totalFiles; $page++) {
            $offset = $page * $perFile;
            $orders = (clone $sql)->with('orderDetails', 'admin')->skip($offset)->take($perFile)->get();

            $chunkNum = $page + 1;
            $fileName = "orders_{$dateStr}_part_{$chunkNum}.{$ext}";
            $filePath = $tempDir . '/' . $fileName;

            if ($format === 'excel') {
                $this->writeXlsx($orders, $filePath);
            } else {
                $this->writeCsv($orders, $filePath);
            }
        }

        // Create ZIP
        $zip = new ZipArchive();
        $zipPath = storage_path('app/' . $zipFileName);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temp files
        $files = glob($tempDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    private function writeCsv($orders, $filePath)
    {
        $handle = fopen($filePath, 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header
        fputcsv($handle, [
            'order_code', 'customer_name', 'customer_email', 'customer_phone',
            'shipping_address', 'products', 'payment_type', 'delivery_status',
            'payment_status', 'notes', 'shipping_area',
        ]);

        foreach ($orders as $order) {
            $products = [];
            foreach ($order->orderDetails as $detail) {
                $attr = [];
                if ($detail->size && $detail->size !== 'No size') {
                    $attr['attribute'] = $detail->size;
                }
                if ($detail->color && $detail->color !== 'No color') {
                    $attr['attribute'] = ($attr['attribute'] ?? '') . ' ' . $detail->color;
                }
                $products[] = [
                    'product_id' => $detail->product_id,
                    'name' => $detail->product?->name ?? '',
                    'quantity' => $detail->qty,
                    'price' => $detail->price,
                    'attribute_value' => !empty($attr) ? $attr : null,
                ];
            }

            $paymentType = $this->mapPaymentType($order->payment_type);
            $paymentStatus = $this->mapPaymentStatus($order);

            fputcsv($handle, [
                $order->orderId ?? '',
                $order->name,
                $order->email ?? '',
                $order->phone,
                $order->address,
                json_encode($products, JSON_UNESCAPED_UNICODE),
                $paymentType,
                $order->order_status,
                $paymentStatus,
                $order->notes ?? '',
                $order->pathao_zone_name ?? $order->area ?? '',
            ]);
        }

        fclose($handle);
    }

    private function writeXlsx($orders, $filePath)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'order_code', 'customer_name', 'customer_email', 'customer_phone',
            'shipping_address', 'products', 'payment_type', 'delivery_status',
            'payment_status', 'notes', 'shipping_area',
        ];

        foreach ($headers as $col => $header) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, 1);
            $cell->setValue($header);
            $cell->getStyle()->getFont()->setBold(true);
        }

        $rowNum = 2;
        foreach ($orders as $order) {
            $products = [];
            foreach ($order->orderDetails as $detail) {
                $attr = [];
                if ($detail->size && $detail->size !== 'No size') {
                    $attr['attribute'] = $detail->size;
                }
                if ($detail->color && $detail->color !== 'No color') {
                    $attr['attribute'] = ($attr['attribute'] ?? '') . ' ' . $detail->color;
                }
                $products[] = [
                    'product_id' => $detail->product_id,
                    'name' => $detail->product?->name ?? '',
                    'quantity' => $detail->qty,
                    'price' => $detail->price,
                    'attribute_value' => !empty($attr) ? $attr : null,
                ];
            }

            $paymentType = $this->mapPaymentType($order->payment_type);
            $paymentStatus = $this->mapPaymentStatus($order);

            $data = [
                $order->orderId ?? '',
                $order->name,
                $order->email ?? '',
                $order->phone,
                $order->address,
                json_encode($products, JSON_UNESCAPED_UNICODE),
                $paymentType,
                $order->order_status,
                $paymentStatus,
                $order->notes ?? '',
                $order->pathao_zone_name ?? $order->area ?? '',
            ];

            foreach ($data as $col => $value) {
                $sheet->getCellByColumnAndRow($col + 1, $rowNum)->setValue($value);
            }
            $rowNum++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function mapPaymentType($type)
    {
        $map = [
            'cod' => 'cash_on_delivery',
            'cash_on_delivery' => 'cash_on_delivery',
            'wallet' => 'wallet',
            'online' => 'online',
            'bkash' => 'bkash',
            'nagad' => 'nagad',
            'rocket' => 'rocket',
        ];
        return $map[strtolower($type)] ?? $type;
    }

    private function mapPaymentStatus($order)
    {
        if ($order->order_status === 'delivered' || $order->order_status === 'complete' || $order->order_status === 'paid') {
            return 'paid';
        }
        if ($order->advance && (float)$order->advance > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    public function searchResult(Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')
                ->orderBy('id', 'desc')
                ->where('is_deleted', '!=', true);

            // Searching...
            if (!empty($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                    $query->where('orderId', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                });
            }

            if (!empty($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }

            if (!empty($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }

            if (!empty($request->user_id)) {
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
                ->where('employee_id', $employee_id)
                ->orderBy('created_at', 'desc')
                ->where('is_deleted', '!=', true);

            // Searching...
            if (!empty($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                    $query->where('phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('orderId', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                });
            }

            if (!empty($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }

            if (!empty($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
        }

        $all_orders = $sql->paginate(100);

        $users = Admin::orderBy('id', 'desc')
            ->where('id', '!=', session()->get('id'))
            ->get();

        return view('admin.customer.search-list', compact('all_orders', 'users'));
    }


    public function deletedOrder (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->where('is_deleted', true)->orderBy('id', 'desc');

            //Searching...
            if (isset($request->search)) {
                $sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }

        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('is_deleted', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm)
                    ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        $orders = $sql->paginate(100);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.deleted-orders', compact('orders', 'users'));
    }


    public function userOrderUpdate(Request $request, $id)
    {

        $product = Product::find($request->related_product_id);

        if($product){
            $orderProduct = Order::find($id);
            $orderProduct->name = $request->name;
            $orderProduct->phone = $request->phone;
            $orderProduct->email = $request->email;
            $orderProduct->area = $request->area;
            $orderProduct->qty = $orderProduct->qty + 1;
            if($product->discount_price == null){
                $orderProduct->price = $orderProduct->price + $product->regular_price;
            }
            if($product->discount_price != null){
                $orderProduct->price = $orderProduct->price + $product->discount_price;
            }
            $orderProduct->address = $request->address;
            $orderProduct->save();

            $productOrder = new OrderDetails();
            $productOrder->order_id = $orderProduct->id;
            $productOrder->product_id = $request->related_product_id;
            $productOrder->qty = 1;
            $productOrder->size = $request->size;
            $productOrder->color = $request->color;
            if($product->discount_price == null){
                $productOrder->price = $product->regular_price;
            }
            if($product->discount_price != null){
                $productOrder->price = $product->discount_price;
            }
            $productOrder->save();
        } else {
            $order = Order::find($id);
            $order->name = $request->name;
            $order->phone = $request->phone;
            $order->email = $request->email;
            $order->area = $request->area;
            $order->price = $request->total_price;
            if($request->filled('discount')){
                $order->discount = $request->discount;
            }
            if($request->filled('advance')){
                $order->advance = $request->advance;
            }
            $order->address = $request->address;
            $order->save();
        }

        //Create Pathao New Parcel...
        if($request->courier == 'Pathao'){
            $orderDetails = Order::find($id);
            $response = PathaoCourier::order()
                        ->create([
                            "store_id"            => "112586", // Find in store list,
                            "merchant_order_id"   => $orderDetails->orderId, // Unique order id
                            "recipient_name"      => $orderDetails->name, // Customer name
                            "recipient_phone"     => $orderDetails->phone, // Customer phone
                            "recipient_address"   => $orderDetails->address, // Customer address
                            "recipient_city"      => $request->city, // Find in city method
                            "recipient_zone"      => $request->zone, // Find in zone method
                            //"recipient_area"      => "5166", // Find in Area method
                            "delivery_type"       => "48", // 48 for normal delivery or 12 for on demand delivery
                            "item_type"           => "2", // 1 for document,2 for parcel
                            "special_instruction" => $request->pathao_special_note,
                            "item_quantity"       => "1", // item quantity
                            "item_weight"         => "0.5", // parcel weight
                            "amount_to_collect"   => (int) $orderDetails->price, // amount to collect
                            "item_description"    => "Not any" // product details
                        ]);
            $responseArray = json_decode(json_encode($response), true);
            $consignmentId = $responseArray['consignment_id'];
            $orderDetails->consignmentId = $consignmentId;
            $orderDetails->save();
        }

        if($request->courier == 'Steadfast'){
            $orderDetails = Order::find($id);

            if (empty($orderDetails->consignmentId))
            {
                // API endpoint (Packzy/Steadfast)
                $apiEndpoint = 'https://portal.packzy.com/api/v1/create_order';

                // API Authentication Parameters
                $apiKey = 'zqitddzywavwvr36vhsiddllfyka9otj';
                $secretKey = 'bug5srqntx0fd8gwy5fvpr37';

                // Required request parameters
                $invoice           = $orderDetails->orderId;
                $cod_amount        = (int) $orderDetails->price;
                $recipient_name    = $orderDetails->name;
                $recipient_phone   = $orderDetails->phone;
                $recipient_address = $orderDetails->address;
                $note              = $request->steadfast_notes;

                // API Headers
                $headers = [
                    'Api-Key'      => $apiKey,
                    'Secret-Key'   => $secretKey,
                    'Content-Type' => 'application/json',
                ];

                // Request payload
                $payload = [
                    'invoice'           => $invoice,
                    'cod_amount'        => $cod_amount,
                    'recipient_name'    => $recipient_name,
                    'recipient_phone'   => $recipient_phone,
                    'recipient_address' => $recipient_address,
                    'note'              => $note,
                ];

                try {
                    // Send the POST request
                    $response = Http::withHeaders($headers)->post($apiEndpoint, $payload);

                    // Check if request was successful
                    if ($response->successful()) {
                        $responseData = $response->json();


                        if (isset($responseData['consignment'])) {
                            $consignmentId = $responseData['consignment']['consignment_id'];
                            // $tracking_code = $responseData['consignment']['tracking_code'];
                            $tracking_code = $responseData['consignment']['tracking_link'];

                            // Save consignment ID to order
                            $orderDetails->consignmentId = $consignmentId;
                            $orderDetails->tracking_code = $tracking_code;
                            $orderDetails->save();


                            if ($orderDetails->order_type == 'Dropshipping')
                            {
                                $appKey    = $orderDetails->dropshipper->app_key;
                                $appSecret = $orderDetails->dropshipper->app_secret;
                                $userName  = $orderDetails->dropshipper->user_name;

                                Http::withHeaders([
                                    'App-Secret' => $appSecret,
                                    'App-Key'    => $appKey,
                                    'Username'   => $userName,
                                ])->post('https://dropshipper.droploo.com/api/dropshipper/order/tracking-code', [
                                    'tracking_code'         => $orderDetails->tracking_code,
                                    'invoice_number' => $orderDetails->orderId,
                                ]);

                                $invoice_number = $orderDetails->orderId;

                                // Step 1: Calculate total wholesale cost
                                $totalWholesaleCost = 0;

                                foreach ($orderDetails->orderDetails as $detail) {
                                    $product = $detail->product;
                                    if ($product) {
                                        $wholesalePrice = $product->wholesale_price;

                                        if ($product->is_variable == 1) {
                                            $productImage = ProductImage::where('size', $detail->size)
                                                ->where('product_id', $detail->product_id)
                                                ->first();
                                            if ($productImage && isset($productImage->wholesale_price)) {
                                                $wholesalePrice = $productImage->wholesale_price;
                                            }
                                        }

                                        $totalWholesaleCost += ($wholesalePrice ?? 0) * $detail->qty;
                                    }
                                }
                                $orderTotal = (float)$orderDetails->price - (float)$orderDetails->area;

                                // Step 2: Calculate profit
                                $grandTotal = $orderTotal - (float)$totalWholesaleCost;
                                $profit_amount = $grandTotal + (float)$orderDetails->area;

                                Http::withHeaders([
                                    'App-Secret' => $appSecret,
                                    'App-Key'    => $appKey,
                                    'Username'   => $userName,
                                ])->post('https://dropshipper.droploo.com/api/dropshipper/order/estimated/profit/add', [
                                    'profit_amount'         => $profit_amount,
                                    'invoice_number'        => $invoice_number,
                                ]);
                            }

                            // return response()->json([
                            //     'message' => 'Order sent to Steadfast successfully',
                            //     'consignment_id' => $consignmentId,
                            // ]);
                        } else {
                            return response()->json(['error' => 'Consignment ID not found in response'], 422);
                        }
                    } else {
                        return response()->json([
                            'error' => 'API call failed',
                            'status' => $response->status(),
                            'body' => $response->body()
                        ], 500);
                    }
                }
                catch (\Exception $e) {
                    Log::channel('steadfast')->error('Steadfast API Exception', [
                        'invoice' => $invoice,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]);
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
        }
        //Update Order with pathao details...
        $orderDetails = Order::find($id);
        $orderDetails->courier_name = $request->courier;
        $orderDetails->pathao_city_id = $request->city;
        $orderDetails->pathao_zone_id = $request->zone;
        $orderDetails->pathao_city_name = $request->city_name;
        $orderDetails->pathao_zone_name = $request->zone_name;
        $orderDetails->notes = $request->steadfast_notes;
        $orderDetails->pathao_special_note = $request->pathao_special_note;
        $orderDetails->otherCourierDetails = $request->otherCourierDetails;
        $orderDetails->delivery_charge_type = $request->delivery_charge_type;
        $orderDetails->order_status = 'complete';
        $orderDetails->save();

        $this->setSuccessMessage('Order has been updated');
        return redirect()->back();
    }


    public function orderDetailsDelete($id)
    {
        $orderDeleteFromAdminPanel = OrderDetails::find($id);
        $order = Order::where('id', $orderDeleteFromAdminPanel->order_id)->first();
        $orderDeleteFromAdminPanel->delete();

        $this->setSuccessMessage('Order has been deleted');
        return redirect()->back();
    }

    public function todayManual (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::whereDate('created_at', \Illuminate\Support\Carbon::today())->with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_type', 'Manual')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereDate('created_at', \Illuminate\Support\Carbon::today())
            ->where('employee_id', $employee_id)
            ->where('order_type', 'Manual')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-manual-list', compact('orders', 'users'));
    }

    public function todayOrders (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::whereDate('created_at', \Illuminate\Support\Carbon::today())->with('orderDetails', 'admin')->orderBy('id', 'desc')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereDate('created_at', \Illuminate\Support\Carbon::today())
            ->where('employee_id', $employee_id)
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-today-list', compact('orders', 'users'));
    }

    public function allManual (Request $request)
    {
        $currentMonth = Carbon::now()->format('m');
        if(session('name') == 'admin'){
            $sql = Order::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', $currentMonth)
            ->with('orderDetails', 'admin')->orderBy('id', 'desc')
            ->where('is_deleted', '!=', true)
            ->where('order_type', 'Manual');
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', $currentMonth)
            ->where('employee_id', $employee_id)
            ->where('order_type', 'Manual')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-manual-list', compact('orders', 'users'));
    }

    public function allWebsite (Request $request)
    {
        $currentMonth = Carbon::now()->format('m');
        if(session('name') == 'admin'){
            $sql = Order::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', $currentMonth)
            ->with('orderDetails', 'admin')->orderBy('id', 'desc')
            ->where('is_deleted', '!=', true)
            ->where('order_type', 'Website');
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', $currentMonth)
            ->where('employee_id', $employee_id)
            ->where('order_type', 'Website')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-website-list', compact('orders', 'users'));
    }

    public function todayCancel (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::whereDate('updated_at', \Illuminate\Support\Carbon::today())->with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'cancel')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereDate('updated_at', \Illuminate\Support\Carbon::today())
            ->where('employee_id', $employee_id)
            ->where('order_status', 'cancel')
            ->where('is_deleted', '!=', true)
            ->orderBy('updated_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-cancel-today-list', compact('orders', 'users'));
    }

    public function todayHold (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::whereDate('updated_at', \Illuminate\Support\Carbon::today())->with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'hold')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereDate('updated_at', \Illuminate\Support\Carbon::today())
            ->where('employee_id', $employee_id)
            ->where('order_status', 'hold')
            ->where('is_deleted', '!=', true)
            ->orderBy('updated_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-hold-today-list', compact('orders', 'users'));
    }

    public function todayPending (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::whereDate('updated_at', \Illuminate\Support\Carbon::today())->with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'pending')->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereDate('updated_at', \Illuminate\Support\Carbon::today())
            ->where('employee_id', $employee_id)
            ->where('order_status', 'pending')
            ->where('is_deleted', '!=', true)
            ->orderBy('updated_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
        }

        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-pending', compact('orders', 'users'));
    }

    public function todayDelivered (Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::whereDate('updated_at', \Illuminate\Support\Carbon::today())->with('orderDetails', 'admin')->orderBy('id', 'desc')->where('order_status', 'delivered')
            ->where('pathao_order_status', null)->where('is_deleted', '!=', true);
            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
                //$sql->orWhere('orderId', $request->search)->orWhere('phone', $request->search);
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->whereDate('updated_at', \Illuminate\Support\Carbon::today())
            ->where('employee_id', $employee_id)
            ->where('order_status', 'delivered')
            ->where('is_deleted', '!=', true)
            ->where('pathao_order_status', null)
            ->orderBy('updated_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('updated_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('updated_at', '<=', $request->to);
            }
            //Searching...
        }

        $delivered_orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.delivery-order-list', compact('delivered_orders', 'users'));
    }

    public function orderReturnList(Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->where('order_status', 'return')->orderBy('id', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }

        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'return')
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-return', compact('orders', 'users'));
    }

    public function orderDamageList(Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->where('order_status', 'damage')->orderBy('id', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }

        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'damage')
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-damage', compact('orders', 'users'));
    }

    public function orderMissingList(Request $request)
    {
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->where('order_status', 'missing')->orderBy('id', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }

        }
        else{
            $employee_id = Session::get('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)
            ->where('order_status', 'missing')
            ->orderBy('created_at', 'desc');

            //Searching...
            if (isset($request->search)) {
                $searchTerm = $request->search;
                $sql->where(function ($query) use ($searchTerm) {
                $query->where('phone', $searchTerm)
                ->orWhere('orderId', $searchTerm);
                })->get();
            }
            if (isset($request->from)) {
                $sql->whereDate('created_at', '>=', $request->from);
            }
            if (isset($request->to)) {
                $sql->whereDate('created_at', '<=', $request->to);
            }
            //Searching...
        }
        $orders = $sql->paginate(50);
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.customer.order-missing', compact('orders', 'users'));
    }

    //Pathao Webhook Implementation for Order Status....
    public function webHookForOrderStatus (Request $request)
    {
        // Parse the JSON payload from the webhook request
        $payload = json_decode($request->getContent(), true);

        // Extract relevant data from the payload
        $consignmentId = $payload['consignment_id'];
        $orderStatus = $payload['order_status'];
        $merchantOrderId = $payload['merchant_order_id'];

        // Find the order in your database by matching merchant_order_id
        $order = Order::where('orderId', $merchantOrderId)->first();

        if ($order) {
            // Update the order status based on the payload order_status
            $order->pathao_order_status = $orderStatus;
            if($orderStatus == 'Return'){
                $order->order_status = 'return';
            }
            elseif($orderStatus == 'Delivered'){
                $order->order_status = 'delivered';
            }
            $order->save();

            // Optionally, you can perform additional actions or logging here
        }

        // Respond with a success message to the webhook provider
        return response('Webhook received and processed.', 200);
    }
    //Pathao Webhook Implementation for Order Status....

    //Steadfast Webhook Implementation for Order Status....
    public function webHookForSteadfastOrderStatusAndDropshipperPayment (Request $request)
    {
        // Parse the JSON payload from the webhook request
        $payload = json_decode($request->getContent(), true);

        // Extract relevant data from the payload
        $consignmentId = $payload['consignment_id'];
        $invoiceId = $payload['invoice'];
        $orderStatus = $payload['status'];
        $codAmount = $payload['cod_amount'];
        $deliveryCharge = $payload['delivery_charge'];

        // Find the order in your database by matching merchant_order_id
        $order = Order::where('orderId', $invoiceId)->first();

        if ($order != null) {
            // Update the order status based on the payload order_status
            $order->steadfast_order_status = $orderStatus;
            if($orderStatus == 'cancelled'){
                $order->order_status = 'return';
                $order->pathao_order_status = "Return";
            }
            elseif($orderStatus == 'delivered'){
                $order->order_status = 'delivered';
                if($order->	dropshipperOrderId != null){
                    $this->dropshipperPaymentDeliveredOrder($order->id);
                }
            }
            elseif($orderStatus == 'partial_delivered'){
                $order->order_status = 'delivered';
            }
            $order->save();


            // Respond with a success message to the webhook provider
            return response('Webhook received and processed.', 200);

            // Optionally, you can perform additional actions or logging here
        }

        else{
            return response('Order not found.', 404);
        }
    }
    //Steadfast Webhook Implementation for Order Status....

    //Dropshipper payment from steadfast status....
    public function dropshipperPaymentDeliveredOrder ($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('id', $id)->with('orderDetails', 'dropshipper')->first();
            $totalWholeSalePrice = 0;

            foreach ($order->orderDetails as $detail) {
                if ($detail->product->is_variable == 1) {
                    DB::rollBack();
                    return response('Payment failed. Variable product detected.', 400);
                }
                $totalWholeSalePrice += $detail->product->wholesale_price * $detail->qty;
            }

            // dd($totalWholeSalePrice);

            $payableAmount = $order->price - $totalWholeSalePrice;

            // Update dropshipper's total credit within the transaction
            $dropshipper = Dropshipper::where('id', $order->dropshipper_id)->lockForUpdate()->first();
            $dropshipper->total_credit += $payableAmount;
            $dropshipper->save();

            // Mark order as paid within the transaction
            $order->is_dpaid = true;
            $order->timestamps = false;
            $order->save();

            DB::commit();

            return response('Payment is done successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Failed to process payment: ' . $e->getMessage(), 400);
        }
    }

    public function dropshipperPaymentPartialDeliveredOrder ($id, $cod_amount)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('id', $id)->with('orderDetails', 'dropshipper')->first();

            // Update dropshipper's total credit within the transaction
            $dropshipper = Dropshipper::where('id', $order->dropshipper_id)->lockForUpdate()->first();
            $dropshipper->total_credit += $cod_amount;
            $dropshipper->save();

            // Mark order as paid within the transaction
            $order->is_dpaid = true;
            $order->timestamps = false;
            $order->save();

            DB::commit();

            return response('Payment is done successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Failed to process payment: ' . $e->getMessage(), 200);
        }
    }
    //Dropshipper payment from steadfast status....

}

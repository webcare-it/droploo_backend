<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showAdminRegisterForm()
    {
        return view('auth.admin-register');
    }

    public function showAdminLoginForm()
    {
        return view('admin.home.auth.admin-login');
    }

    public function dashboard(Request $request)
    {
        //dd(\Illuminate\Support\Carbon::today());
        if(session('name') == 'admin'){
            $sql = Order::with('orderDetails', 'admin')->where('order_status', 'pending')
            ->where('is_deleted', '!=', true)
            ->orderBy('created_at', 'desc');


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
            if(isset($request->user_id)){
                $sql->where('employee_id', (int)$request->user_id);
            }
            //Searching...

            //Count Searching...
            if(isset($request->order_from) && isset($request->order_to)){
                $totalOrders = Order::whereDate('created_at', '>=', $request->order_from)
                ->whereDate('created_at', '<=', $request->order_to)
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $websiteOrder = Order::whereDate('created_at', '>=', $request->order_from)
                ->whereDate('created_at', '<=', $request->order_to)
                ->where('order_type', 'Website')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $manualOrder = Order::whereDate('created_at', '>=', $request->order_from)
                ->whereDate('created_at', '<=', $request->order_to)
                ->where('order_type', 'Manual')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $pendingOrder = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'pending')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $pendingPayment = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'pending payment')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $onHold = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'hold')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $scheduleDelivery = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'schedule delivery')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $cancelledOrder = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'cancel')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $completedOrder = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'complete')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $pathaoCompletedOrder = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('pathao_order_status', 'Delivered')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $pendingInvoice = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'pending invoice')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $delivered = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'delivered')
                ->where(function ($query) {
                    $query->whereNull('pathao_order_status')
                    ->orWhere('pathao_order_status', 'Delivered');
                    })
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $stockOut = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'stock out')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $customerConfirm = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'customer confirm')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $requestReturn = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'request return')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $paid = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'paid')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $return = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'return')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $damage = Order::whereDate('updated_at', '>=', $request->order_from)
                ->whereDate('updated_at', '<=', $request->order_to)
                ->where('order_status', 'damage')
                ->where('is_deleted', '!=', true)
                ->get()->count();

                $filterDate = $request->order_from. ' to '. $request->order_to;
            }
            else{
                //Show all data counts instead of monthly report...
                $websiteOrder = Order::where('order_type', 'Website')->where('is_deleted', '!=', true)->count();
                $manualOrder = Order::where('order_type', 'Manual')->where('is_deleted', '!=', true)->count();
                $pendingOrder = Order::where('order_status', 'pending')->where('is_deleted', '!=', true)->count();
                $pendingPayment = Order::where('order_status', 'pending payment')->where('is_deleted', '!=', true)->count();
                $onHold = Order::where('order_status', 'hold')->where('is_deleted', '!=', true)->count();
                $scheduleDelivery = Order::where('order_status', 'schedule delivery')->where('is_deleted', '!=', true)->count();
                $cancelledOrder = Order::where('order_status', 'cancel')->where('is_deleted', '!=', true)->count();
                $completedOrder = Order::where('order_status', 'complete')->where('is_deleted', '!=', true)->count();
                $pathaoCompletedOrder = Order::where('pathao_order_status', 'Delivered')->where('is_deleted', '!=', true)->count();
                $pendingInvoice = Order::where('order_status', 'pending invoice')->where('is_deleted', '!=', true)->count();
                $delivered = Order::where('order_status', 'delivered')
                ->where(function ($query) {
                    $query->whereNull('pathao_order_status')
                    ->orWhere('pathao_order_status', 'Delivered');
                    })->where('is_deleted', '!=', true)->count();
                $stockOut = Order::where('order_status', 'stock out')->where('is_deleted', '!=', true)->count();
                $customerConfirm = Order::where('order_status', 'customer confirm')->where('is_deleted', '!=', true)->count();
                $requestReturn = Order::where('order_status', 'request return')->where('is_deleted', '!=', true)->count();
                $paid = Order::where('order_status', 'paid')->where('is_deleted', '!=', true)->count();
                $return = Order::where('order_status', 'return')->where('is_deleted', '!=', true)->count();
                $damage = Order::where('order_status', 'damage')->where('is_deleted', '!=', true)->count();
                $totalOrders = Order::where('is_deleted', '!=', true)->count();
                $filterDate = 'All Time';
            }
            //Count Searching...

            //Product Reporting...
            if(isset($request->product_from) && isset($request->product_to)){
                $product_from = $request->product_from;
                $product_to = $request->product_to;
                $products = Product::whereHas('orderDetails', function ($query) use ($product_from, $product_to) {
                    $query->whereBetween('created_at', [$product_from, $product_to]);
                })->with(['orderDetails' => function ($query) use ($product_from, $product_to) {
                    $query->whereBetween('created_at', [$product_from, $product_to]);
                }])->get();
            }
            else{
                // Show all products instead of daily products
                $products = Product::whereHas('orderDetails')->with('orderDetails')->get();
            }
            //Product Reporting...
            $currentMonth = Carbon::now()->month;
            $notifications = Notification::orderBy('created_at', 'desc')->paginate(20);
            $specificEmployeeRank = 0;
            $monthlyPerformance = '0.000';
        }

        else{
            $employee_id = session('id');
            $sql = Order::with('orderDetails', 'admin')
            ->where('employee_id', $employee_id)->where('order_status', 'pending')
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

            //Generate Rank According to Completed Orders...
            // Calculate performance percentage for each employee based on all time data
            $performanceData = DB::table('orders')
            ->select('employee_id', DB::raw('SUM(CASE WHEN order_status = "delivered" THEN 1 ELSE 0 END) / COUNT(*) * 100 as performance_percentage'))
            ->where('is_deleted', '!=', true)
            ->groupBy('employee_id')
            ->get();

            // Rank employees based on performance_percentage
            $rankedEmployees = $performanceData->sortByDesc('performance_percentage')
            ->values()
            ->map(function ($item, $index) {
                $item->rank = $index + 1;
                return $item;
            });
            $specificEmployeeRank = $rankedEmployees->where('employee_id', $employee_id)->pluck('rank')->first();
            //Generate Rank According to Completed Orders...

            //Generate Monthly Performance According to Completed Orders...
            if($specificEmployeeRank != null){
                $allOrders = Order::where('employee_id', $employee_id)
                ->where('is_deleted', '!=', true)
                ->count();
                $completedOrders = Order::where('employee_id', $employee_id)->where('order_status', 'delivered')
                ->where('is_deleted', '!=', true)
                ->count();
                if($allOrders==0){
                    $monthlyPerformance = 0.000;
                }
                if($allOrders>0){
                    $monthlyPerformance = number_format((($completedOrders/$allOrders)*100),3);
                }
            }
            else{
                $monthlyPerformance = '0.000';
            }
            //Generate Monthly Performance According to Completed Orders...

            $websiteOrder = 0;
            $totalOrders = 0;
            $manualOrder = 0;
            $pendingOrder = 0;
            $pendingPayment = 0;
            $onHold = 0;
            $scheduleDelivery = 0;
            $cancelledOrder = 0;
            $completedOrder = 0;
            $pathaoCompletedOrder = 0;
            $pendingInvoice = 0;
            $delivered = 0;
            $stockOut = 0;
            $customerConfirm = 0;
            $requestReturn = 0;
            $paid = 0;
            $return = 0;
            $damage = 0;
            $filterDate = 'All Time';
            $products = [];
            $notifications = [];
        }

        $orders = $sql->paginate(50); // Increased from 10 to 50 for better UX
        $users = Admin::orderBy('id', 'desc')->where('id', '!=', session()->get('id'))->get();
        return view('admin.home.index', compact('orders', 'users',
         'specificEmployeeRank', 'monthlyPerformance', 'websiteOrder',
          'totalOrders', 'manualOrder', 'pendingOrder', 'pendingPayment',
          'onHold', 'scheduleDelivery', 'cancelledOrder', 'completedOrder', 'pathaoCompletedOrder',
          'pendingInvoice', 'delivered', 'stockOut', 'customerConfirm',
           'requestReturn', 'paid', 'return', 'damage', 'filterDate',
            'products', 'notifications'));
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        try{
            $admin = Admin::where('email', $request->email)->first();

            if(!$admin){
                return redirect()->back()->withError('Unauthorised user login');
            }
            if ($admin){
                if (password_verify($request->password, $admin->password)){
                    session(['id' => $admin->id]);
                    session(['name' => $admin->name]);
                    return redirect('/admin/dashboard');
                }else {
                    return redirect()->back()->withError('Password does not match');
                }
            }else {
                return redirect()->back()->withError('Email does not match');
            }
        }catch(Exception $exception){
            return redirect()->back()->withError($exception->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('admin.login.form')->with('success', 'You are successfully logged out');
    }
}

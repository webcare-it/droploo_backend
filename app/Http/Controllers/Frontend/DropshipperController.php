<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\DropshipperDepositRequest;
use App\Http\Requests\DropshipperRegRequest;
use App\Models\Dropshipper;
use App\Models\DropshipperBankingInfo;
use App\Models\DropshipperDeposit;
use App\Models\Order;
use App\Models\WithdrawHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DropshipperController extends Controller
{
    public function registrationForm ()
    {
        return view ('frontend.v-2.dropshipper.auth.registration');
    }

    public function registrationStore (DropshipperRegRequest $request)
    {
        try{
            $dropshipper = new Dropshipper();

            if($request->file('image')){
                $avatar = time().'dropshipper-image'.'.'. $request->image->extension();
                $request->image->move('frontend/dropshipper/images/', $avatar);;
                $dropshipper->image = $avatar;
            }
            $dropshipper->name = $request->name;
            $dropshipper->user_name = rand(100, 100000);
            $dropshipper->domain_name = $request->domain_name;
            $dropshipper->email = $request->email;
            $dropshipper->phone = $request->phone;
            $dropshipper->address = $request->address;
            $dropshipper->password = bcrypt($request->password);
            $dropshipper->save();
            // if($supplier->save()){
            //     Mail::to($supplier->email)->send(new VendorRegistrationEmail($supplier));
            // }

            return redirect()->back()->with('success', 'Your information has been submitted. Please wait for the approval!');
        }catch(Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    
    public function loginForm()
    {
        return view('frontend.v-2.dropshipper.auth.login');
    }

    public function login(Request $request)
{
    $this->validate($request, [
        'email'   => 'required|email',
        'password' => 'required'
    ]);

    try {
        if (Auth::guard('dropshipper')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $dropshipper = auth()->guard('dropshipper')->user();
            if($dropshipper->is_approved == 0){
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not approved yet.');
            }
            return redirect('/dropshipper/dashboard');
        } else {
            return redirect()->back()->with('error', 'Email or password does not match. Please try again.');
        }        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'An error occurred while logging in. Please try again.');
    }
}


    public function dashboard()
    {
        if(auth('dropshipper')->check()){
            $wallet = Dropshipper::where('id', Auth::guard('dropshipper')->id())->select('total_deposit')->first();
            $credit = Dropshipper::where('id', Auth::guard('dropshipper')->id())->select('total_credit')->first();
            $allOrder = Order::where('dropshipper_id', Auth::guard('dropshipper')->id())->count();
            $pendingOrder = Order::where('dropshipper_id', Auth::guard('dropshipper')->id())->where('order_status', 'pending')->count();
            $shipmentOrder = Order::where('dropshipper_id', Auth::guard('dropshipper')->id())->where('order_status', 'complete')->count();
            $deliveredOrder = Order::where('dropshipper_id', Auth::guard('dropshipper')->id())->where('order_status', 'delivered')->count();
            $returnOrder = Order::where('dropshipper_id', Auth::guard('dropshipper')->id())->where('order_status', 'return')->count();
            return view('frontend.dropshipper.dashboard', compact('wallet', 'credit', 'pendingOrder', 'shipmentOrder', 'deliveredOrder', 'returnOrder', 'allOrder'));
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }


    }

    //Deposit..
    public function depositHistory ()
    {
        if(auth('dropshipper')->check()){
            $deposits = DropshipperDeposit::where('dropshipper_id', Auth::guard('dropshipper')->id())->orderBy('id', 'desc')->get();
            return view('frontend.dropshipper.deposit.list', compact('deposits'));
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function depositCreate ()
    {
        if(auth('dropshipper')->check()){
            return view('frontend.dropshipper.deposit.create');
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function depositStore (DropshipperDepositRequest $request)
    {
        if(auth('dropshipper')->check()){
            $deposit = new DropshipperDeposit();
            $deposit->dropshipper_id = Auth::guard('dropshipper')->id();
            $deposit->amount = $request->amount;
            $deposit->payment_gateway = $request->payment_gateway;
            $deposit->transaction_id = $request->transaction_id;

            $deposit->save();
            return redirect()->back()->with('success', 'Request Sent! Please wait for the approval');
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    //Withdraw..
    public function withdrawHistory ()
    {
        if(auth('dropshipper')->check()){
            $withdraws = WithdrawHistory::where('dropshipper_id', Auth::guard('dropshipper')->id())->orderBy('id', 'desc')->get();
            return view('frontend.dropshipper.withdraw.list', compact('withdraws'));
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function withdrawCreate ()
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();

            if($dropshipper->total_credit >= 100){
                return view('frontend.dropshipper.withdraw.create');
            }

            else{
                return redirect()->back()->with('error', 'Insufficient Balance!');
            }
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function withdrawStore (Request $request)
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();
            $pendingRequest = WithdrawHistory::where('dropshipper_id', $dropshipper->id)->orderBy('id', 'desc')->where('status', 'Pending')->first();

            if($pendingRequest == null){
                if($request->amount>=100){
                    if($request->amount <= $dropshipper->total_credit){
                        $withdraw = new WithdrawHistory();
                        $withdraw->dropshipper_id = $dropshipper->id;
                        $withdraw->amount = $request->amount;
                        $withdraw->notes = $request->notes;
        
                        $withdraw->save();
                        return redirect('dropshipper/withdraw-history')->with('success', 'Request is sent successfully!');
                    }

                    else{
                        return redirect()->back()->with('error', 'Insufficient Balance!');
                    }
                }

                else{
                    return redirect()->back()->with('error', 'Minimum Withdraw amount is 100 BDT!');
                }
            }

            else{
                return redirect()->back()->with('error', 'The previous request is still pending!');
            }
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }


    //Profile...
    public function dropshipperProfile ()
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();
            $bankDetails = DropshipperBankingInfo::where('dropshipper_id', $dropshipper->id)->first();
            return view ('frontend.dropshipper.profile', compact('dropshipper', 'bankDetails'));
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function dropshipperProfileUpdate (Request $request)
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();

            if ($request->hasFile('image')) {
                if ($dropshipper->image && file_exists('frontend/dropshipper/images/'.$dropshipper->image)){
                    unlink('frontend/dropshipper/images/'.$dropshipper->image);
                }
                $image = rand() . '-imagedrop-.' . $request->image->extension();
                $request->image->move('frontend/dropshipper/images/', $image);
                $dropshipper->image = $image;
            }

            $dropshipper->name = $request->name;
            $dropshipper->phone = $request->phone;
            $dropshipper->address = $request->address;

            $dropshipper->save();
            return redirect()->back()->with('success', 'Updated Successfully!');
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function dropshipperBankInfoUpdate (Request $request)
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();

            $bankingInfo = DropshipperBankingInfo::where('dropshipper_id', $dropshipper->id)->first();
            if($bankingInfo == null){
                $newBankInfo = new DropshipperBankingInfo();

                $newBankInfo->banking_type = $request->banking_type;
                $newBankInfo->account_details = $request->account_details;
                $newBankInfo->dropshipper_id = $dropshipper->id;

                $newBankInfo->save();
                return redirect()->back()->with('success', 'Updated Successfully');
            }
            else{
                $bankingInfo->banking_type = $request->banking_type;
                $bankingInfo->account_details = $request->account_details;

                $bankingInfo->save();
                return redirect()->back()->with('success', 'Updated Successfully');
            }
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    public function dropshipperCredentialasUpdate (Request $request)
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();

            if (Hash::check($request->old_password, $dropshipper->password)) {
                $dropshipper->password = bcrypt($request->password);
                $dropshipper->save();
                return redirect()->back()->with('success', 'Password Changed Successfully!');
            }

            else{
                return redirect()->back()->with('error', 'Old Password Did not match!');
            }
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }

    //Orders...
    public function orders ($type)
    {
        if(auth('dropshipper')->check()){
            $dropshipper = Auth::guard('dropshipper')->user();
            if($type == 'all'){
                $orderType = 'All';
                $orders = Order::with('orderDetails')->orderBy('created_at', 'desc')->where('dropshipper_id', $dropshipper->id)->where('is_deleted', '!=', 1)->paginate(200);
                return view ('frontend.dropshipper.order.list', compact('orders', 'orderType'));
            }
            if($type == 'pending'){
                $orderType = 'Pending';
                $orders = Order::with('orderDetails')->orderBy('created_at', 'desc')->where('dropshipper_id', $dropshipper->id)->where('order_status', 'pending')->where('is_deleted', '!=', 1)->paginate(200);
                return view ('frontend.dropshipper.order.list', compact('orders', 'orderType'));
            }
            if($type == 'complete'){
                $orderType = 'Shipment';
                $orders = Order::with('orderDetails')->orderBy('created_at', 'desc')->where('dropshipper_id', $dropshipper->id)->where('order_status', 'complete')->where('is_deleted', '!=', 1)->paginate(200);
                return view ('frontend.dropshipper.order.list', compact('orders', 'orderType'));
            }
            if($type == 'delivered'){
                $orderType = 'Delivered';
                $orders = Order::with('orderDetails')->orderBy('created_at', 'desc')->where('dropshipper_id', $dropshipper->id)->where('order_status', 'delivered')->where('is_deleted', '!=', 1)->paginate(200);
                return view ('frontend.dropshipper.order.list', compact('orders', 'orderType'));
            }
            if($type == 'return'){
                $orderType = 'Returned';
                $orders = Order::with('orderDetails')->orderBy('created_at', 'desc')->where('dropshipper_id', $dropshipper->id)->where('order_status', 'return')->where('is_deleted', '!=', 1)->paginate(200);
                return view ('frontend.dropshipper.order.list', compact('orders', 'orderType'));
            }
        }

        else{
            return redirect()->intended('/dropshipper/login-form');
        }
    }
}

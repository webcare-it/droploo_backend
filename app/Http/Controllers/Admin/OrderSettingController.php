<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderSetting;
use Illuminate\Http\Request;

class OrderSettingController extends Controller
{
    public function index()
    {
        $settings = OrderSetting::first();
        if (!$settings) {
            $settings = OrderSetting::create([
                'order_status' => 1,
                'order_disable_message' => '',
            ]);
        }
        return view('admin.order-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'order_status' => 'required|in:0,1',
            'order_disable_message' => 'nullable|string',
        ]);

        $settings = OrderSetting::first();
        if (!$settings) {
            $settings = OrderSetting::create([
                'order_status' => $request->order_status,
                'order_disable_message' => $request->order_disable_message,
            ]);
        } else {
            $settings->update([
                'order_status' => $request->order_status,
                'order_disable_message' => $request->order_disable_message,
            ]);
        }

        $notification = array(
            'message' => 'Order settings updated successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('admin.order.settings')->with($notification);
    }
}

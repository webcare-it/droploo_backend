<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderSetting;
use Illuminate\Http\Request;

class OrderSettingController extends Controller
{
    /**
     * Display order settings
     */
    public function index()
    {
        $setting = OrderSetting::getSetting();
        return view('admin.settings.order-setting', compact('setting'));
    }

    /**
     * Update order settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'order_status' => 'required|boolean',
            'order_off_message' => 'nullable|string|max:1000',
        ]);

        $setting = OrderSetting::getSetting();
        $setting->update([
            'order_status' => $request->order_status,
            'order_off_message' => $request->order_off_message,
        ]);

        return redirect()->back()->with('success', 'Order settings updated successfully!');
    }
}

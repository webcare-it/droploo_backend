<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Dropshipper;

class DropshipperController extends Controller
{
    // Show list of dropshippers
    public function dropshipperList()
    {
        $dropshippers = Dropshipper::all();
        return view('admin.dropshipper.list', compact('dropshippers'));
    }

    // Show dropshipper details
    public function dropshipperDetails($id)
    {
        $dropshipper = Dropshipper::findOrFail($id);
        $ordersCount = $dropshipper->orders()->count(); // Assuming you have a relation defined
        return view('admin.dropshipper.details', compact('dropshipper', 'ordersCount'));
    }

    // Edit dropshipper
    public function dropshipperEdit($id)
    {
        $dropshipper = Dropshipper::findOrFail($id);
        return view('admin.dropshipper.edit', compact('dropshipper'));
    }

    // Update dropshipper
    public function dropshipperUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'nullable|string',
            'domain_name' => 'nullable|string',
        ]);

        $dropshipper = Dropshipper::findOrFail($id);
        $dropshipper->update($request->all());

        return redirect()->back()->with('success', 'Dropshipper updated successfully!');
    }

    // Show orders for a specific dropshipper
    public function dropshipperOrders($dropshipper_id)
    {
        $orders = Order::where('dropshipper_id', $dropshipper_id)->latest()->get();
        $dropshipper = Dropshipper::findOrFail($dropshipper_id);

        return view('admin.dropshipper.orders', compact('orders', 'dropshipper'));
    }

    // Delete dropshipper
    public function dropshipperDelete($id)
    {
        $dropshipper = Dropshipper::find($id);
        
        if (!$dropshipper) {
            return redirect()->back()->with('error', 'Dropshipper not found.');
        }
        
        // Check if the dropshipper has any related orders
        if ($dropshipper->orders()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete dropshipper because they have associated orders.');
        }
        
        // Delete related records first
        $dropshipper->bankInfo()->delete();
        $dropshipper->withdraw()->delete();
        $dropshipper->deposits()->delete();
        $dropshipper->credits()->delete();
        
        // Delete the dropshipper
        $dropshipper->delete();
        
        return redirect()->back()->with('success', 'Dropshipper deleted successfully!');
    }
}

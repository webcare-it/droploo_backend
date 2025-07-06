<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'vendor_id', 'product_id', 'qty', 'price', 'orderId', 'phone'];

    //===================================== Relationship ======================================//

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->with('shipping', 'billing', 'payment', 'order');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class)->with('product');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_district_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'employee_id', 'id');
    }

    public function dropshipper()
    {
        return $this->belongsTo(Dropshipper::class, 'dropshipper_id', 'dropshipper_id');
    }

    public function credits()
    {
        return $this->hasMany(DropshipperCredit::class, 'order_id', 'id');
    }

    public function invoiceNumber()
    {
        $orderLastId = Order::orderBy('id', 'desc')->first();
        if (! $orderLastId) {
            return'BM0001';
        } else {
            $string = preg_replace("/[^0-9\.]/", '', $orderLastId->id);
            return 'BM' . sprintf('%04d', $string+1);
        }
    }

    public function notification()
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }
}

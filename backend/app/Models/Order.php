<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'vendor_id', 'product_id', 'qty', 'price', 'orderId', 'phone'];

    protected $hidden = [
        'id',
        'user_id',
        'employee_id',
        'orderId',
        'dropshipperOrderId',
        'consignmentId',
        'tracking_code',
        'name',
        'phone',
        'email',
        'area',
        'delivery_charge_type',
        'district_id',
        'sub_district_id',
        'price',
        'discount',
        'advance',
        'qty',
        'payment_type',
        'address',
        'status',
        'order_status',
        'pathao_order_status',
        'steadfast_order_status',
        'is_deleted',
        'order_type',
        'customer_type',
        'created_at',
        'updated_at',
        'courier_name',
        'pathao_special_note',
        'pathao_city_id',
        'pathao_city_name',
        'pathao_zone_id',
        'pathao_zone_name',
        'notes',
        'otherCourierDetails',
        'payment_gateway',
        'transaction_id',
        'dropshipper_id',
        'charge_is_paid',
        'is_dpaid',
        'is_printed',
        'profit',
        'tracking_link',
        'is_dropshipping'
    ];

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
            return 'DL0001';
        } else {
            $string = preg_replace("/[^0-9\.]/", '', $orderLastId->id);
            return 'DL' . sprintf('%04d', $string + 1);
        }
    }

    public function notification()
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }
}

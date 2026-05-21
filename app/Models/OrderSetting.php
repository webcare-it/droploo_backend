<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_status',
        'order_disable_message',
    ];

    protected $casts = [
        'order_status' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropshipperCredit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dropshipper()
    {
        return $this->belongsTo(Dropshipper::class, 'dropshipper_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}

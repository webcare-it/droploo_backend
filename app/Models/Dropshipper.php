<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dropshipper extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class, 'dropshipper_id', 'id');
    }

    public function bankInfo ()
    {
        return $this->hasOne(DropshipperBankingInfo::class, 'dropshipper_id', 'id');
    }

    public function withdraw ()
    {
        return $this->hasMany(WithdrawHistory::class, 'dropshipper_id', 'id');
    }
}

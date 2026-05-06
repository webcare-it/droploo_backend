<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dropshipper ()
    {
        return $this->belongsTo(Dropshipper::class, 'dropshipper_id', 'id');
    }
}

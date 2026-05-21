<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSetting extends Model
{
    use HasFactory;

    protected $table = 'order_settings';

    protected $fillable = [
        'order_status',
        'order_off_message',
    ];

    protected $casts = [
        'order_status' => 'boolean',
    ];

    /**
     * Get the order setting or create default
     */
    public static function getSetting()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'order_status' => true,
                'order_off_message' => 'Order creation is currently disabled. Please try again later.',
            ]);
        }
        return $setting;
    }
}

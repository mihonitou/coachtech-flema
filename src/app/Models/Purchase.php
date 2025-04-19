<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'address',
        'payment_method',
    ];

    /**
     * リレーション定義
     */

    // 購入者（N:1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 購入された商品（N:1）
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 配送先住所（1:1）
    public function shipmentAddress()
    {
        return $this->hasOne(ShipmentAddress::class);
    }
}

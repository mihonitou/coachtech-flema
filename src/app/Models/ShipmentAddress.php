<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'purchase_id',
        'name',
        'postal_code',
        'address',
        'building',
    ];

    /**
     * リレーション定義
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 紐づく購入（1:1）
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}

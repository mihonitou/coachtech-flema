<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_id',
        'name',
        'postal_code',
        'address',
        'building',
    ];

    /**
     * リレーション定義
     */

    // 紐づく購入（1:1）
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}

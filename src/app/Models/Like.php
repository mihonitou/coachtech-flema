<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
    ];

    /**
     * リレーション定義
     */

    // 「いいね」をしたユーザー（N:1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 「いいね」が付けられた商品（N:1）
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

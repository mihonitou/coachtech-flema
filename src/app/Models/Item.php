<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'status',
        'brand_name',
        'image_path',
        'sold',
    ];

    /**
     * リレーション定義
     */

    // 出品者（1:N）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // この商品についた「いいね」
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // この商品へのコメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // この商品に対する購入（1:0or1）
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    // カテゴリとの中間テーブル（N:N）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }
}

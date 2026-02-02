<?php

namespace App\Models;

use App\Models\Comment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'price',
        'condition',
        'description',
        'image_path',
    ];

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ
    public function categories()
    {
        return $this->belongsToMany(    Category::class,
        'item_category'
        );
    }

    // 　　いいね
    public function likes()
    {
        return $this->hasMany(Like::class)
        ->where('delete_flag', 0);
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入情報（1商品1購入想定）
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    
}
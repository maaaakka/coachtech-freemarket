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
        return $this->hasMany(Like::class);
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
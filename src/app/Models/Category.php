<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // リレーション（あとで使う）
    public function items()
    {
        return $this->belongsToMany(   Item::class,
        'item_category'
        );
    }
}

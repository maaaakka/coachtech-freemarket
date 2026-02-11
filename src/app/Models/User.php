<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Comment;
use App\Models\Address;
use App\Models\Profile;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 　　出品した商品
    public function items()
    {
    return $this->hasMany(Item::class);
    }

    // いいね
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes')
            ->wherePivot('delete_flag', 0)
            ->withTimestamps();
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入履歴
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // プロフィール（アイコン・住所など）
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    
}

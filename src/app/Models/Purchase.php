<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * 一括代入許可カラム
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_method',
        'payment_status',
    ];

    /**
     * ===========================
     * 支払い方法 定数
     * ===========================
     */
    public const PAYMENT_CARD = 1;          // クレジットカード
    public const PAYMENT_CONVENIENCE = 2;   // コンビニ払い

    /**
     * ===========================
     * 支払いステータス 定数
     * ===========================
     */
    public const STATUS_PENDING = 0;   // 未決済
    public const STATUS_PAID = 1;      // 支払い完了
    public const STATUS_SHIPPED = 2;   // 発送済み
    public const STATUS_DONE = 3;      // 取引完了
    public const STATUS_CANCEL = 9;    // キャンセル

    /**
     * ユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 配送先住所
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * 支払い方法（日本語表示用）
     */
    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            self::PAYMENT_CARD => 'クレジットカード',
            self::PAYMENT_CONVENIENCE => 'コンビニ払い',
            default => '不明',
        };
    }

    /**
     * ステータス表示用
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            self::STATUS_PENDING => '未払い',
            self::STATUS_PAID => '支払い完了',
            self::STATUS_SHIPPED => '発送済み',
            self::STATUS_DONE => '取引完了',
            self::STATUS_CANCEL => 'キャンセル',
            default => '不明',
        };
    }
}
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
     * 支払いステータス 定数（←ここ重要）
     * ===========================
     */
    public const STATUS_PENDING = 1;     // 支払い待ち
    public const STATUS_PAID = 2;        // 支払い完了
    public const STATUS_EXPIRED = 3;     // 期限切れ
    public const STATUS_CANCEL = 4;      // キャンセル

    /**
     * ===========================
     * リレーション
     * ===========================
     */

    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 配送先住所
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * ===========================
     * 表示用アクセサ
     * ===========================
     */

    // 支払い方法ラベル
    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            self::PAYMENT_CARD => 'クレジットカード',
            self::PAYMENT_CONVENIENCE => 'コンビニ払い',
            default => '不明',
        };
    }

    // 支払いステータスラベル
    public function getStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            self::STATUS_PENDING => '支払い待ち',
            self::STATUS_PAID => '支払い完了',
            self::STATUS_EXPIRED => '期限切れ',
            self::STATUS_CANCEL => 'キャンセル',
            default => '不明',
        };
    }

    /**
     * ===========================
     * よく使う判定メソッド（超便利）
     * ===========================
     */

    // 支払い完了しているか
    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    // 支払い待ちか
    public function isPending(): bool
    {
        return $this->payment_status === self::STATUS_PENDING;
    }
}
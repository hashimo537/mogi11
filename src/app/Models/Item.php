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
        'description',
        'price',
        'condition',
        'image',
    ];


    const CONDITION_GOOD = 1;
    const CONDITION_CLEAN = 2;
    const CONDITION_NORMAL = 3;
    const CONDITION_BAD = 4;

    public static function conditionLabels()
    {
        return [
            self::CONDITION_GOOD => '良好',
            self::CONDITION_CLEAN => '目立った傷や汚れなし',
            self::CONDITION_NORMAL => 'やや傷や汚れあり',
            self::CONDITION_BAD => '状態が悪い',
        ];
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

// カテゴリ（多対多）
public function categories()
{
    return $this->belongsToMany(Category::class, 'item_categories');
}

public function getConditionLabelAttribute(): string
{
    return self::conditionLabels()[$this->condition] ?? '未設定';
}


// いいね
public function likes()
{
    return $this->hasMany(Like::class);
}

// いいねしたユーザー（多対多）
public function likedUsers()
{
    return $this->belongsToMany(User::class, 'likes');
}

// コメント
public function comments()
{
    return $this->hasMany(Comment::class);
}

// 購入（1対1）
public function purchase()
{
    return $this->hasOne(Purchase::class);
}
}

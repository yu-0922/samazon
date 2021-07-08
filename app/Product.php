<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFavorite\Traits\Favoriteable;

class Product extends Model
{
    // お気に入り機能の追加（商品が対象）
    use Favoriteable;
    // カテゴリーと紐付け
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    // レビューと紐付け
    public function reviews()
    {
        return $this->hasMany('App\Review');
    }
}

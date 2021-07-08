<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFavorite\Traits\Favoriteable;
// ソート機能が実装されているソースコードを読み込む
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    // お気に入り機能,ソート機能の追加（商品が対象）
    use Favoriteable, Sortable;

    // ソートする対象を指定
    public $sortable = [
        'price',
        'update_at'
    ];

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

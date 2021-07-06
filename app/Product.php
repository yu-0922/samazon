<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // 商品と紐付け
    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    // ユーザーと紐付け
    public function user()
    {
        return $this->belongsTo('App\user');
    }
}

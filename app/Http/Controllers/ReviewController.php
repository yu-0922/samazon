<?php

namespace App\Http\Controllers;

use App\Review;
// productモデルが使用できるようにする
use App\Product;
// 現在レビューしているユーザーの情報をReviewController.phpで取り扱えるようにする
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product, Request $request)
    {
        // 新しいレビューのデータを作成
        $review = new Review();
        // $request内のデータを各カラムに保存
        $review->content = $request->input('content');
        $review->product_id = $product->id;
        // レビューを作成したユーザーのIDをレビューに保存
        $review->user_id = Auth::user()->id;
        // フォームから送信された評価をデータベースに保存
        $review->score = $request->input('score');
        $review->save();

        return redirect()->route('products.show', $product);
    }
}
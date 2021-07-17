<?php

namespace App\Http\Controllers;

use App\Product;
// カテゴリーを扱うCategoryモデルをこのファイル内で使用できる
use App\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // $request内に$category->idの値が保存されている
    public function index(Request $request) 
    {
        $sort_query = [];
        $sorted = "";

        if ($request->sort !== null) {
            $slices = explode(' ', $request->sort);
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->sort;
        }

        if ($request->category !== null) {
            // 受け取った絞り込みたいカテゴリーIDを持つ商品データを取得
            $products = Product::Where('category_id', $request->category)->sortable($sort_query)->paginate(15);
            $category = Category::find($request->category);
        } else {
            // Productモデルを使って全ての商品データをデータベースから取得し、$productsに代入
            $products = Product::sortable($sort_query)->paginate(15);
            $category = null;
        }

        $sort = [
            '並び替え' => '', 
            '価格の安い順' => 'price asc',
            '価格の高い順' => 'price desc', 
            '出品の古い順' => 'updated_at asc', 
            '出品の新しい順' => 'updated_at desc'
        ];

        $categories = Category::all();
        // 全カテゴリーからmajor_category_nameのカラムのみ取得。その上でunique()を使って重複している部分を削除
        $major_category_names = Category::pluck('major_category_name')->unique();
        
        // 呼び出すビューを指定し、$productsをビューに渡す
        return view('products.index', compact('products', 'category', 'categories', 'major_category_names', 'sort', 'sorted'));
    }

    public function favorite(Product $product)
    {
        // 現在のユーザー情報を$userに代入
        $user = Auth::user();

        // ユーザーがその商品をお気に入り済みかチェック
        if ($user->hasFavorited($product)) {
            // お気に入り済みの場合は解除
            $user->unfavorite($product);
        } else {
            // 登録していない場合はお気に入りとして登録
            $user->favorite($product);
        }

        return redirect()->route('products.show', ['id' => $product->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        // 商品についての全てのレビューを取得して$reviewsに保存
        $reviews = $product->reviews()->get();
        // resources\views\productsディレクトリ内のshow.blade.phpをビューとして使用
        // compact('product', 'reviews')で商品のデータが保存されている変数を、ビューへと渡す
        return view('products.show', compact('product', 'reviews'));
    }
}

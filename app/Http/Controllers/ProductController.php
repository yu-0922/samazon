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
        if ($request->category !== null) {
            // 受け取った絞り込みたいカテゴリーIDを持つ商品データを取得
            $products = Product::Where('category_id', $request->category)->paginate(15);
            $category = Category::find($request->category);
        } else {
            // Productモデルを使って全ての商品データをデータベースから取得し、$productsに代入
            $products = Product::paginate(15);
            $category = null;
        }

        $categories = Category::all();
        // 全カテゴリーからmajor_category_nameのカラムのみ取得。その上でunique()を使って重複している部分を削除
        $major_category_names = Category::pluck('major_category_name')->unique();
        
        // 呼び出すビューを指定し、$productsをビューに渡す
        return view('products.index', compact('products', 'category', 'categories', 'major_category_names'));
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $categoriesに全てのカテゴリーを保存
        $categories = Category::all();
        
        //呼び出すビューを指定し、$categoriesをビューに渡す
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Productモデルの変数を作成
        $product = new Product();
        // フォームから送信されたデータが格納されている$requestから各項目のデータをそれぞれのカラムに保存
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        // データベースに保存
        $product->save();
        // route関数にリダイレクトするコントローラーとアクションを指定
        // データが保存された後showアクションへとリダイレクト
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        // view関数を使って使用するビューを指定,compact以下で複数の変数をビューに渡す
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //$request内に格納されている更新後のデータをそれぞれのカラムに渡して上書き
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        // 更新
        $product->update();
        // リダイレクト
        return redirect()->route('products.show', ['id' => $product->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // データベースから指定の商品のデータを削除
        $product->delete();
        // /productsというURLへリダイレクト
        return redirect()->route('products.index');
    }
}

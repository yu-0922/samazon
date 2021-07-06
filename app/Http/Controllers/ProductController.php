<?php

namespace App\Http\Controllers;

use App\Product;
// カテゴリーを扱うCategoryモデルをこのファイル内で使用できる
use App\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Productモデルを使って全ての商品データをデータベースから取得し、$productsに代入
        $products = Product::all(); 
        
        //呼び出すビューを指定し、$productsをビューに渡す
        return view('products.index', compact('products'));
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

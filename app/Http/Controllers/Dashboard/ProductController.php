<?php

namespace App\Http\Controllers\Dashboard;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_query = [];
        $sorted = "";

        if ($request->sort !== null) {
            $slices = explode(' ', $request->sort);
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->sort;
        }

        if ($request->keyword !== null) {
            $keyword = rtrim($request->keyword);
            $total_count = Product::where('name', 'like', "%{$keyword}%")->orwhere('id', "{$keyword}")->count();
            $products = Product::where('name', 'like', "%{$keyword}%")->orwhere('id', "{$keyword}")->sortable($sort_query)->paginate(15);
        } else {
            $keyword = "";
            $total_count = Product::count();
            $products = Product::paginate(15);
        }

        $sort = [
                '価格の安い順' => 'price asc',
            '価格の高い順' => 'price desc',
                '出品の古い順' => 'updated_at asc',
                '出品の新しい順' => 'updated_at desc'
            ];

        return view('dashboard.products.index', compact('products', 'sort', 'sorted', 'total_count', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('dashboard.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        // おすすめ商品かどうかを判定するフラグを受け取って保存
        if ($request->input('recommend') == 'on') {
            $product->recommend_flag = true;
        } else {
            $product->recommend_flag = false;
        }

        if ($request->file('image') !== null) {
            $product->image = Storage::disk('s3')->putFile('public/products', $request->file('image'), 'public');
        } else {
            $product->image = '';
        }

        // 送料にチェックが入っているか判定
        if ($request->input('carriage') == 'on') {
            $product->carriage_flag = true;
        } else {
            $product->carriage_flag = false;
        }
        $product->save();

        return redirect()->route('dashboard.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        if ($request->input('recommend') == 'on') {
            $product->recommend_flag = true;
        } else {
            $product->recommend_flag = false;
        }

        if ($request->hasFile('image')) {
            $product->image = Storage::disk('s3')->putFile('public/products', $request->file('image'), 'public');
        } else {
            $product->image = '';
        }

        if ($request->input('carriage') == 'on') {
            $product->carriage_flag = true;
        } else {
            $product->carriage_flag = false;
        }
        $product->update();

        return redirect()->route('dashboard.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('dashboard.products.index');
    }

    public function import(Product $product)
    {
        return view('dashboard.products.import');
    }

    public function import_csv(Request $request)
    {
        if ($request->hasFile('csv')) {
            Excel::import(new ProductsImport, $request->file('csv'));
            return redirect()->route('dashboard.products.import_csv')->with('flash_message', 'CSVでの一括登録が成功しました!');
        }
        return redirect()->route('dashboard.products.import_csv')->with('flash_message', 'CSVが追加されていません。CSVを追加してください。');
    }
}

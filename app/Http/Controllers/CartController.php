<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Cart::instance()を使えるようにする
use Gloudemans\Shoppingcart\Facades\Cart;
// Auth::user()を使えるようにする
use Illuminate\Support\Facades\Auth;
// モデルなどを介さず直接データベースからデータを取得できるようにするためのファイルを読み込む
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // 現在カートに入っている商品一覧とこれまで購入した商品履歴（カートの履歴）を表示
    public function index()
    {
        // ユーザーのIDを元にこれまで追加したカートの中身を$cart変数に保存
        $cart = Cart::instance(Auth::user()->id)->content();

        $total = 0;

        foreach ($cart as $c) {
            $total += $c->qty * $c->price;
        }

        return view('carts.index', compact('cart', 'total'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // カート商品を追加
    public function store(Request $request)
    {
        // ユーザーのIDを元にカートのデータを作成し、add()関数を使って送信されたデータを元に商品を追加
        Cart::instance(Auth::user()->id)->add(
            [
                'id' => $request->_token, 
                'name' => $request->name, 
                'qty' => $request->qty, 
                'price' => $request->price, 
                'weight' => $request->weight, 
            ] 
        );
        // 商品追加後、そのまま商品の個別ページにリダイレクト
        return redirect()->route('products.show', $request->get('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // ユーザーのIDと注文履歴(カートの履歴)のIDを元に、過去の履歴を表示
    public function show($id)
    {
        // データベース内のshoppingcartテーブルに保存されているデータを、ユーザーとカートのIDを使用して取得
        $cart = DB::table('shoppingcart')->where('instance', Auth::user()->id)->where('identifier', $count)->get();

        // 取得したデータをビューに渡す
        return view('carts.show', compact('cart'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // カートの中身を更新
    public function update(Request $request)
    {
        // trueの場合、指定した商品をカートから削除
        if ($request->input('delete')) {
            // Cart::remove()に削除したいカート内の商品IDを渡すことで、カートから削除
            Cart::instance(Auth::user()->id)->remove($request->input('id'));
        } else {
            // trueでない場合は、商品の個数を$request->input('qty')の値へ変更
            Cart::instance(Auth::user()->id)->update($request->input('id'), $request->input('qty'));
        }

        return redirect()->route('carts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // カートの商品を購入する処理を実装
    public function destroy(Request $request)
    {
        $user_shoppingcarts = DB::table('shoppingcart')->where('instance', Auth::user()->id)->get();

        // 現在までのユーザーが注文したカートの数を取得
        $count = $user_shoppingcarts->count();

        // 新しくデータベースに登録するカートのデータ用にカートのIDを１つ増やす
        $count += 1;
        //ユーザーのIDを使ってカート内の商品情報などをデータベースに保存
        Cart::instance(Auth::user()->id)->store;

        // データベース内のshoppingcartテーブルへのアクセス。where()を使ってユーザーのIDとカート数$countを使い、先ほど作成したカートのデータを更新
        DB::table('shoppingcart')->where('instance', Auth::user()->id)->where('number', null)->update(['number' => $count, 'buy_flag' => true]);

        Cart::instance(Auth::user()->id)->destroy();

        return redirect()->route('carts.index');
    }
}

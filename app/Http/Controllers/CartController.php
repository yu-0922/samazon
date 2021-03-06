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
            if($c->options->carriage) {
                $total += ($c->qty * ($c->price + env('CARRIAGE')));
            } else {
                $total += $c->qty * $c->price;
            }
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
                'options' => [
                    'carriage' => $request->carriage
                ]
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
        $user_shoppingcarts = DB::table('shoppingcart')->get();
        $number = DB::table('shoppingcart')->where('instance', Auth::user()->id)->count();
        // 現在までのユーザーが注文したカートの数を取得
        $count = $user_shoppingcarts->count();

        // 新しくデータベースに登録するカートのデータ用にカートのIDを１つ増やす
        $count += 1;
        $number += 1;
        $cart = Cart::instance(Auth::user()->id)->content();

        $price_total = 0;
        $qty_total = 0;

        foreach ($cart as $c) {
            if ($c->options->carriage) {
                $price_total += ($c->qty * ($c->price + 800));
            } else {
                $price_total += $c->qty * $c->price;
            }
            $qty_total += $c->qty;
        }

        //ユーザーのIDを使ってカート内の商品情報などをデータベースに保存
        Cart::instance(Auth::user()->id)->store($count);

        // データベース内のshoppingcartテーブルへのアクセス。where()を使ってユーザーのIDとカート数$countなどを使い、先ほど作成したカートのデータを更新
        DB::table('shoppingcart')->where('instance', Auth::user()->id)
                                ->where('number', null)
                                ->update(
                                    [
                                        'code' => substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10),
                                        'number' => $number,
                                        'price_total' => $price_total,
                                        'qty' => $qty_total,
                                        'buy_flag' => true,
                                        'updated_at' => date("Y/m/d H:i:s")
                                    ]
                                );

        $pay_jp_secret = env('PAYJP_SECRET_KEY');
        \Payjp\Payjp::setApiKey($pay_jp_secret);

        $user = Auth::user();

        $res = \Payjp\Charge::create(
            [
                "customer" => $user->token,
                "amount" => $price_total,
                "currency" => 'jpy'
            ]
        );

        Cart::instance(Auth::user()->id)->destroy();

        return redirect()->route('carts.index');
    }
}

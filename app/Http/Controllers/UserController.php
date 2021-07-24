<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
use App\ShoppingCart;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function mypage()
    {
        // ユーザー自身の情報を$userに保存
        $user = Auth::user();

        // ビューに渡してビュー側で表示
        return view('users.mypage', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user = Auth::user();

        $user->name = $request->input('name') ? $request->input('name') : $user->name;
        $user->email = $request->input('email') ? $request->input('email') : $user->email;
        $user->postal_code = $request->input('postal_code') ? $request->input('postal_code') : $user->postal_code;
        $user->address = $request->input('address') ? $request->input('address') : $user->address;
        $user->phone = $request->input('phone') ? $request->input('phone') : $user->phone;
        $user->update();

        return redirect()->route('mypage');
    }

    public function edit_address()
    {
        $user = Auth::user();

        return view('users.edit_address', compact('user'));
    }

    public function edit_password()
    {
        return view('users.edit_password');
    }

    public function update_password(Request $request)
    {
        $user = Auth::user();

        // 送信されたリクエスト内のpasswordとconfirm_passwordが同一か確認
        if($request->input('password') == $request->input('confirm_password')) {
            // 同一ならパスワードを暗号化しデータベースへ保存
            $user->password = bcrypt($request->input('password'));
            $user->update();
        } else {
            // 異なっていたらパスワード変更画面へとリダイレクト
            return redirect()->route('mypage.edit_password');
        }

        return redirect()->route('mypage');
    }

    public function favorite()
    {
        $user = Auth::user();

        $favorites = $user->favorites(Product::class)->get();

        return view('users.favorite', compact('favorites'));
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user->deleted_flag) {
            $user->deleted_flag = false;
        } else {
            $user->deleted_flag = true;
        }

        $user->update();

        Auth::logout();

        return redirect('/');
    }

    public function cart_history_index(Request $request)
    {
        $page = $request->page != null ? $request->page : 1;
        $user_id = Auth::user()->id;
        $billings = ShoppingCart::getCurrentUserOrders($user_id);
        $total = count($billings);
        $paginator = new LengthAwarePaginator(array_slice($billings, ($page - 1), 15), $total, 15, $page, ['path' => 'dashboard']);

        return view('users.cart_history_index', compact('billings', 'total', 'paginator'));
    }

    public function cart_history_show(Request $request)
    {
        $num = $request->num;
        $user_id = Auth::user()->id;

        $cart_info = DB::table('shoppingcart')->where('instance', $user_id)->where('number', $num)->get()->first();

        Cart::instance($user_id)->restore($num);

        $cart_contents = Cart::content();

        Cart::instance($user_id)->store($num);

        Cart::destroy();

        DB::table('shoppingcart')->where('instance', $user_id)
                                ->where('number', null)
                                ->update(
                                    [
                                        'code' => $cart_info->code,
                                        'number' => $num,
                                        'price_total' => $cart_info->price_total,
                                        'qty' => $cart_info->qty,
                                        'buy_flag' => $cart_info->buy_flag,
                                        'updated_at' => $cart_info->updated_at
                                    ]
                                );

        return view('users.cart_history_show', compact('cart_contents', 'cart_info'));
    }
}

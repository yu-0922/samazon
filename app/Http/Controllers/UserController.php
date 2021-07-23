<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
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

}

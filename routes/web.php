<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// カートの中身を確認するページへのURLを設定
Route::get('users/carts', 'CartController@index')->name('carts.index');
// カートへ追加する処理のルーティングを設定
Route::post('users/carts', 'CartController@store')->name('carts.store');
Route::delete('users/carts', 'CartController@destroy')->name('carts.destroy');
// ユーザー情報関連の各ルーティングを設定
Route::get('users/mypage', 'UserController@mypage')->name('mypage');
Route::get('users/mypage/edit', 'UserController@edit')->name('mypage.edit');
Route::get('users/mypage/address/edit', 'UserController@edit_address')->name('mypage.edit_address');
Route::put('users/mypage', 'UserController@update')->name('mypage.update');
Route::get('users/mypage/favorite', 'UserController@favorite')->name('mypage.favorite');
// パスワード変更画面のURLとパスワードを更新するルーティングを追加
Route::get('users/mypage/password/edit', 'UserController@edit_password')->name('mypage.edit_password');
Route::put('users/mypage/password', 'UserController@update_password')->name('mypage.update_password');

// Route:postでPOSTで使用するルーティングだと分かるようにする
// products/{product}/reviewsとして商品のデータを自動的に取得
// 使用するコントローラーとそのアクションを、ReviewController@storeと指定
Route::post('products/{product}/reviews', 'ReviewController@store');

Route::get('products/{product}/favorite', 'ProductController@favorite')->name('products.favorite');

Route::resource('products', 'ProductController');
// メールでの認証が済んでいない場合はメール送信画面へと遷移
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
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

// Route:postでPOSTで使用するルーティングだと分かるようにする
// products/{product}/reviewsとして商品のデータを自動的に取得
// 使用するコントローラーとそのアクションを、ReviewController@storeと指定
Route::post('products/{product}/reviews', 'ReviewController@store');

Route::resource('products', 'ProductController');
// メールでの認証が済んでいない場合はメール送信画面へと遷移
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

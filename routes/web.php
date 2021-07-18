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

Route::get('/', 'WebController@index');

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

Route::get('products', 'ProductController@index')->name('products.index');


// メールでの認証が済んでいない場合はメール送信画面へと遷移
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/dashboard', 'DashboardController@index')->middleware('auth:admins');

Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('login', 'Dashboard\Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Dashboard\Auth\LoginController@login')->name('login');
    Route::resource('major_categories', 'Dashboard\MajorCategoryController')->middleware('auth:admins');
    Route::resource('categories', 'Dashboard\CategoryController')->middleware('auth:admins');
    Route::resource('products', 'Dashboard\ProductController')->middleware('auth:admins');
    Route::resource('users', 'Dashboard\UserController')->middleware('auth:admins');
});

// APP_EVNの値を読み取り、その値がproductionと同じかどうかで処理を切り分ける
if(env('APP_EVN') === 'production') {
    // httpsでアセットを読み込む
    URL::forceScheme('https');
}
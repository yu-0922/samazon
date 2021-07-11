<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyFlagToShoppingcart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoppingcart', function (Blueprint $table) {
            // falseを設定してdestoryアクションにてユーザーが注文した内容を参照し、購入する商品情報をtrueに変更した後に購入処理を実行
            $table->boolean('buy_flag')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoppingcart', function (Blueprint $table) {
            //
        });
    }
}

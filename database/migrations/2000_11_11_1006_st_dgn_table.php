<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StDgnTable extends Migration
{
    /**
     * Run the migrations.
     * 伝言(st_dgn)テーブル
     * @return void
     */
    public function up()
    {
        Schema::create('st_dgn', function (Blueprint $table) {
            $table->increments('dgn_id')->comment('伝言ID');
            $table->unsignedInteger('dgb_id')->comment('伝言板ID');
            $table->string('title', 100)->comment('伝言タイトル');
            $table->text('message')->comment('伝言内容');
            $table->unsignedInteger('create_user_id')->comment('伝言作成ユーザーID');
//            $table->boolean('del_flg')->comment('削除フラグ')->default(false);
            $table->softDeletes();  // ソフトデリートカラムを追加
            $table->timestamps();

            $table->foreign('dgb_id')->references('dgb_id')->on('st_dgb');
            $table->foreign('create_user_id')->references('id')->on('st_act');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_dgn');
    }
}

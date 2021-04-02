<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StDgbTable extends Migration
{
    /**
     * Run the migrations.
     * 伝言板(st_dgb)テーブル
     * @return void
     */
    public function up()
    {
        Schema::create('st_dgb', function (Blueprint $table) {
            $table->increments('dgb_id')->comment('伝言板ID');
            $table->string('title', 100)->comment('板タイトル');
            $table->text('comment')->comment('板内容')->nullable();
            $table->unsignedInteger('create_user_id')->comment('板作成ユーザーID');
            $table->timestamp('last_update_date')->comment('最終更新日時');
            $table->softDeletes();  // ソフトデリートカラムを追加
            $table->timestamps();

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
        Schema::dropIfExists('st_dgb');
    }
}

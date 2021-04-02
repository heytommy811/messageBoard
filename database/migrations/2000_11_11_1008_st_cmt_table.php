<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StCmtTable extends Migration
{
    /**
     * Run the migrations.
     * コメント(st_cmt)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('st_cmt', function (Blueprint $table) {
            $table->increments('comment_id')->comment('コメントID');
            $table->unsignedInteger('dgn_id')->comment('伝言ID');
            $table->text('comment')->comment('コメント内容');
            $table->unsignedInteger('post_user_id')->comment('投稿ユーザーID');
            //$table->boolean('del_flg')->comment('削除フラグ')->default(false);
            $table->softDeletes();  // ソフトデリートカラムを追加
            $table->timestamps();

            $table->foreign('dgn_id')->references('dgn_id')->on('st_dgn');
            $table->foreign('post_user_id')->references('id')->on('st_act');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('st_cmt');
    }
}

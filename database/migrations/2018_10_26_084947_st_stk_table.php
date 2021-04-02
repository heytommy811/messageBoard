<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StStkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_stk', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_id')->comment('コメントID')->nullable();
            $table->unsignedInteger('reply_user_id')->comment('返答ユーザーID');
            $table->unsignedInteger('user_id')->comment('ユーザーID');
            $table->unsignedInteger('dgn_id')->comment('伝言ID');
            $table->smallInteger('stk_type')->comment('新着種別');
            $table->softDeletes();  // ソフトデリートカラムを追加
            $table->timestamps();

            $table->foreign('dgn_id')->references('dgn_id')->on('st_dgn');
            $table->foreign('comment_id')->references('comment_id')->on('st_cmt');
            $table->foreign('reply_user_id')->references('id')->on('st_act');
            $table->foreign('user_id')->references('id')->on('st_act');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_stk');
    }
}

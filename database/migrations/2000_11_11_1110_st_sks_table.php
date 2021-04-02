<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StSksTable extends Migration
{
    /**
     * Run the migrations.
     * 参加申請(st_sks)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('st_sks', function (Blueprint $table) {
            $table->increments('join_request_id')->comment('参加申請ID');
            $table->unsignedInteger('dgb_id')->comment('伝言板ID');
            $table->unsignedInteger('user_id')->comment('申請ユーザーID');
            $table->smallInteger('request_result')->comment('申請結果')->default(0);
            $table->timestamps();

            $table->foreign('dgb_id')->references('dgb_id')->on('st_dgb');
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
        //
        Schema::dropIfExists('st_sks');
    }
}

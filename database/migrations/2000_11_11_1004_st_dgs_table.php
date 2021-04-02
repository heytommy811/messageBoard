<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StDgsTable extends Migration
{
    /**
     * Run the migrations.
     * 伝言板招待(st_dgs)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('st_dgs', function (Blueprint $table) {
            $table->increments('dgb_invite_id')->comment('伝言板招待ID');
            $table->unsignedInteger('dgb_id')->comment('招待先伝言板ID');
            $table->string('invite_id', 13)->comment('招待ID');
            $table->unsignedInteger('create_user_id')->comment('招待者ユーザーID');
            $table->timestamp('invite_yuko_kigen')->comment('有効期限');
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
        //
        Schema::dropIfExists('st_dgs');
    }
}

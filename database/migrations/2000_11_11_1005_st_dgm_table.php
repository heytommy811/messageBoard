<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StDgmTable extends Migration
{
    /**
     * Run the migrations.
     * 伝言板メンバー(st_dbm)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('st_dgm', function (Blueprint $table) {
            $table->unsignedInteger('dgb_id')->comment('伝言板ID');
            $table->unsignedInteger('user_id')->comment('ユーザーID');
            $table->string('name', 100)->comment('メンバー名')->nullable();
            $table->smallInteger('auth_id')->comment('権限ID');
            $table->timestamps();

            $table->primary(['dgb_id', 'user_id']);
            $table->foreign('dgb_id')->references('dgb_id')->on('st_dgb');
            $table->foreign('user_id')->references('id')->on('st_act');
            $table->foreign('auth_id')->references('auth_id')->on('sm_kgn');
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
        Schema::dropIfExists('st_dgm');
    }
}

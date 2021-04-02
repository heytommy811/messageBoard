<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StDgkTable extends Migration
{
    /**
     * Run the migrations.
     * 伝言板管理(st_dgk)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('st_dgk', function (Blueprint $table) {
            $table->unsignedInteger('dgb_id')->comment('伝言板ID');
            $table->smallInteger('default_auth_id')->comment('デフォルト権限ID');
            $table->smallInteger('join_type')->comment('参加方式');
            $table->string('join_password', 100)->comment('参加パスワード')->nullable();
            $table->boolean('search_type')->comment('検索可否')->default(false);
            //$table->boolean('del_flg')->comment('削除フラグ')->default(false);
            $table->softDeletes();  // ソフトデリートカラムを追加
            $table->timestamps();

            $table->primary('dgb_id');
            $table->foreign('dgb_id')->references('dgb_id')->on('st_dgb');
            $table->foreign('default_auth_id')->references('auth_id')->on('sm_kgn');
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
        Schema::dropIfExists('st_dgk');
    }
}

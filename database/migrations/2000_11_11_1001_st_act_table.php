<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StActTable extends Migration
{
    /**
     * Run the migrations.
     * アカウント（st_act）テーブル作成
     * @return void
     */
    public function up()
    {
        Schema::create('st_act', function (Blueprint $table) {
            $table->increments('id')->comment('ユーザーID');
            $table->string('mail', 191)->comment('メールアドレス')->unique();
            $table->string('password', 255)->comment('パスワード');
            $table->string('account_name', 100)->comment('アカウント名');
            $table->timestamp('last_login_dt')->comment('最終ログイン日時')->nullable();
            $table->smallInteger('login_fail_cnt')->comment('ログイン失敗回数')->default(0);
            $table->timestamp('login_fail_dt')->comment('最終ログイン失敗日時')->nullable();
            $table->smallInteger('login_lock_cnt')->comment('ログインロック回数')->default(0);
            $table->timestamp('login_lock_dt')->comment('最終ログインロック日時')->nullable();
            $table->boolean('account_lock')->comment('アカウントロック')->default(false);
            $table->boolean('admin')->comment('管理者')->default(false);
            //$table->boolean('del_flg')->comment('削除フラグ')->default(false);
            $table->softDeletes();  // ソフトデリートカラムを追加
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_act');
    }
}

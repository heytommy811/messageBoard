<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StAcmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_acm', function (Blueprint $table) {
            $table->increments('id')->comment('ユーザーID');
            $table->string('mail', 191)->comment('メールアドレス');
            $table->string('password', 255)->comment('パスワード');
            $table->string('account_name', 100)->comment('アカウント名');
            $table->string('ip')->comment('クライアントIP');
            $table->timestamp('url_yuko_kigen')->comment('URL有効期限');
            $table->string('url', 255)->comment('URL');
            $table->string('cert_id', 255)->comment('認証ID');
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
        Schema::dropIfExists('st_acm');
    }
}

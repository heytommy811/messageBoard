<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStAprTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_apr', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('ユーザーID');
            $table->string('cert_id', 255)->comment('認証ID');
            $table->timestamp('url_yuko_kigen')->comment('URL有効期限');
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
        Schema::dropIfExists('st_apr');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SysAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip')->comment('クライアントIP');
            $table->string('agent')->comment('user-agent');
            $table->boolean('is_pc')->comment('PC接続かどうか');
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
        Schema::dropIfExists('sys_access');
    }
}

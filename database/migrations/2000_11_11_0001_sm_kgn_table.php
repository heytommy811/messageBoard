<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmKgnTable extends Migration
{
    /**
     * Run the migrations.
     * 権限マスタ(sm_kgn)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sm_kgn', function (Blueprint $table) {
            $table->smallInteger('auth_id')->comment('権限ID');
            $table->string('description')->comment('説明');

            $table->primary('auth_id');
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
        Schema::dropIfExists('sm_kgn');
    }
}

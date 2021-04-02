<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StIinTable extends Migration
{
    /**
     * Run the migrations.
     * いいね(st_iin)テーブル
     * @return void
     */
    public function up()
    {
        //
        Schema::create('st_iin', function (Blueprint $table) {
            $table->unsignedInteger('dgn_id')->comment('伝言ID');
            $table->unsignedInteger('user_id')->comment('ユーザーID');            
            $table->timestamps();

            $table->primary(['dgn_id', 'user_id']);
            $table->foreign('dgn_id')->references('dgn_id')->on('st_dgn');
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
        Schema::dropIfExists('st_iin');
    }
}

<?php

use Illuminate\Database\Seeder;

class st_dgn_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('st_dgn')->insert([
            [
                'dgb_id' => 1,
                'title' => '伝言タイトル1',
                'message' => '伝言の内容が入ります。abc',
                'create_user_id' => 2,
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'dgb_id' => 2,
                'title' => '伝言タイトル2',
                'message' => '伝言の内容が入ります。あいうえお',
                'create_user_id' => 2,
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ]
        ]);
    }
}

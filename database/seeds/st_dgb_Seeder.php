<?php

use Illuminate\Database\Seeder;

class st_dgb_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('st_dgb')->insert([
            [
                'title' => '伝言板タイトル１',
                'comment' => '板内容',
                'create_user_id' => 2,
                'last_update_date' => new DateTime(),  // timestamp type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'title' => '伝言板タイトル２',
                'comment' => '板内容',
                'create_user_id' => 2,
                'last_update_date' => new DateTime(),  // timestamp type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ]
        ]);
    }
}

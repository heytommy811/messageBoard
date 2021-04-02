<?php

use Illuminate\Database\Seeder;

class st_dgm_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('st_dgm')->insert([
            [
                'dgb_id' => 1,
                'user_id' => 2,
                'name' => '板作成者メンバ名',
                'auth_id' => 5,
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'dgb_id' => 1,
                'user_id' => 3,
                'name' => '伝言板メンバ名',
                'auth_id' => 4,
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'dgb_id' => 2,
                'user_id' => 2,
                'name' => '板作成者メンバ名',
                'auth_id' => 5,
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ]
        ]);
    }
}

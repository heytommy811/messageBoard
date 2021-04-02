<?php

use Illuminate\Database\Seeder;

class sm_kgn_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sm_kgn')->insert([
            [
                'auth_id' => 1,
                'description' => '閲覧権限',
            ],
            [
                'auth_id' => 2,
                'description' => '閲覧、投稿権限',
            ],
            [
                'auth_id' => 3,
                'description' => '閲覧、共有権限',
            ],
            [
                'auth_id' => 4,
                'description' => '閲覧、投稿、共有権限',
            ],
            [
                'auth_id' => 5,
                'description' => '管理権限',
            ]
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class st_act_table_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * アカウント（st_act）テーブルのデータ登録
     * @return void
     */
    public function run()
    {
        DB::table('st_act')->insert([
            [
                'mail' => str_random(10) . '@mail.com',
                'password' => Hash::make('aaa'),
                'account_name' => 'アカウント名太郎',
                'last_login_dt' => new DateTime(),  // timestamp type
                'del_flg' => false,  // boolen type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'mail' => str_random(10) . '@mail.com',
                'password' => Hash::make('aaa'),
                'account_name' => 'アカウント名次郎',
                'last_login_dt' => new DateTime(),  // timestamp type
                'del_flg' => false,  // boolen type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ]
        ]);
    }
}

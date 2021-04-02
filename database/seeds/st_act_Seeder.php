<?php

use Illuminate\Database\Seeder;

class st_act_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('st_act')->insert([
            [
                'mail' => 'admin',
                'password' => Hash::make('admin'),
                'account_name' => '管理者',
                'last_login_dt' => new DateTime(),  // timestamp type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
                'admin' => true,
            ],
            [
                'mail' => 'test@mail.com',
                'password' => Hash::make('aaa'),
                'account_name' => 'アカウント名太郎',
                'last_login_dt' => new DateTime(),  // timestamp type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
                'admin' => false,
            ],
            [
                'mail' => 'test2@mail.com',
                'password' => Hash::make('aaa'),
                'account_name' => 'アカウント名次郎',
                'last_login_dt' => new DateTime(),  // timestamp type
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
                'admin' => false,
            ]
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class st_dgk_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('st_dgk')->insert([
            [
                'dgb_id' => 1,
                'default_auth_id' => 4,
                'join_type' => 1,   // invite
                'join_password' => 'abc',
                'search_type' => true,  // can search
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'dgb_id' => 2,
                'default_auth_id' => 3,
                'join_type' => 2,   // request
                'join_password' => 'abc',
                'search_type' => false, // can't search
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ]
        ]);
    }
}

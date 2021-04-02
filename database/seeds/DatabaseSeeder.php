<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //st_act_table_Seeder::class,
            sm_kgn_Seeder::class,
            sm_sys_Seeder::class,
            st_acm_Seeder::class,
            st_act_Seeder::class,
            st_apr_Seeder::class,
            st_dgb_Seeder::class,
            st_dgk_Seeder::class,
            st_dgs_Seeder::class,
            st_dgm_Seeder::class,
            st_dgn_Seeder::class,
            st_cmt_Seeder::class,
            st_iin_Seeder::class,
            st_sks_Seeder::class,
        ]);
    }
}

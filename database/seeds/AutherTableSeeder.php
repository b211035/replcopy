<?php

use Illuminate\Database\Seeder;

class AutherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('authers')->insert(
            [
                'login_id' => 'admin',
                'name' => 'admin',
                'password' => '$2y$10$iahMjMH4sbOpm0.Jotp54OsnI80rAj67hjKtCApZqNx1H73nIOTSC'
            ]
        );
    }
}

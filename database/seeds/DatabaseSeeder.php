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
	    DB::table('users')->insert([
		    'login'=>'admin',
		    'email' => 'admin@ukr.net',
		    'password' => bcrypt('admin'),
		    'created_at' => date('Y-m-d H:i:s'),
		    'updated_at' => date('Y-m-d H:i:s')
	    ]);
    }
}

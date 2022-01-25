<?php
//php artisan db:seed --class=UsersTableSeeder
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('admin_system_profiles')->insert([
            'name' => 'admin',
            'permissions' => '{}',
            'active' => 1,
            'active_by' => 1,
            'active_date' => date('Y-m-d H:i:s'),
            'add_by' => 1,
            'add_date' => date('Y-m-d H:i:s'),
        ]);
        DB::table('admin_system_users')->insert([
            'profile_id' => 1,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin'),
            'add_by' => 1,
            'add_date' => date('Y-m-d H:i:s'),
        ]);
    }
}

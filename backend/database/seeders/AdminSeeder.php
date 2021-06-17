<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return User::create([
            'login' => 'admin',
            'name' => 'vlad',
            'email' => 'veklenko1402@gmail.com',
            'password' => bcrypt('12345'),
            'role' => 'admin'
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
           'name' => 'admin',
           'email' => 'admin@gmail.com',
           'password' => 'admin',
           'type' => 'admin',
        ]);
        $user->email_verified_at = now();
        $user->update();
    }
}

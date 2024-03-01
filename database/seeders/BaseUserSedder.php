<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BaseUserSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
           'email' => 'jawad.taki.alden2002@gmail.com',
           'name' => 'jawad taki alden',
           'image' => File::get(public_path('user_seeder_images/profile.jpg')),
           'language' => 'en',
           'birthdate' => '2003-02-26',
           'gender' => 'male',
           'password' => 'jawad2003',
           'phone' => '0948966979'
        ]);
    }
}

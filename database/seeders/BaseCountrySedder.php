<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaseCountrySedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::create([
           'name' => 'syria',
           'title' => [
               'en' => 'Syria',
               'ar' => 'سوريا'
           ] ,
           'flag' => 'SY',
           'state_key' => 'SY',
           'is_active' => true,
           'is_default' => true
        ]);
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Section;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Section::create([
           'title' => [
               'en' => 'most viewed',
               'ar' => 'الاكثر مشاهدة'
           ],
           'is_active' => true,
           'is_default' => true
        ]);

        Section::create([
            'title' => [
                'en' => 'newest',
                'ar' => 'الاحدث'
            ],
            'is_active' => true,
            'is_default' => false
        ]);
    }
}

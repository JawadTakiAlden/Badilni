<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaseSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Section::create([
           'title' => [
               'en' => "most viewed",
               'ar' => "الاكثر مشاهدة"
           ],
            'is_active' => true,
            'is_default' => false
        ]);

        Section::create([
            'title' => [
                'en' => "newest",
                'ar' => "الاحدث"
            ],
            'is_active' => true,
            'is_default' => true
        ]);
    }
}

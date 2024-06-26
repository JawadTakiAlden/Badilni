<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Section::create([
           'title' => json_encode([
               'en' => 'most viewed',
               'ar' => 'الاكثر مشاهدة'
           ]),
           'is_active' => true,
           'is_default' => true
        ]);

        Section::create([
            'title' => json_encode([
                'en' => 'newest',
                'ar' => 'الاحدث'
            ]),
            'is_active' => true,
            'is_default' => false
        ]);

        $category = Category::create([
            'title' => json_encode([
                'en' => "Laptop",
                'ar' => 'لابتوبات'
            ]),
            'description' => json_encode([
                'en' => 'contain the most popular brand in laptops world',
                'ar' => 'يحتوي هذا القسم على اشهر الماركات في عالم اللابتوبات'
            ]),
            'is_active' => true,
            'sort' => 0,
        ]);


        $country1 = Country::create([
            'title' => json_encode(['en' => 'United States', 'ar' => 'الولايات المتحدة']),
            'country_code' => 'US',
            'is_active' => true,
        ]);

        $country2 = Country::create([
            'title' => json_encode(['en' => 'United Kingdom', 'ar' => 'المملكة المتحدة']),
            'country_code' => 'UK',
            'is_active' => true,
        ]);

        $city1 = City::create([
            'title' => json_encode(['en' => 'New York City' , 'ar' => 'مدينة نيويورك']),
            'country_id' => $country1->id,
            'is_active' => true
        ]);

        $city2 = City::create([
            'title' => json_encode(['en' => 'London' , 'ar' => 'لندن']),
            'country_id' => $country2->id,
            'is_active' => true
        ]);

        Area::create([
            'title' => json_encode(['en' => 'Times Square' , 'ar' => 'ساحة تايمز']),
            'city_id' => $city1->id,
            'is_active' => true
        ]);

         Area::create([
            'title' => json_encode(['en' => 'Covent Garden' , 'ar' => 'كوفنت غاردن']),
            'city_id' => $city2->id,
            'is_active' => true
        ]);
    }
}

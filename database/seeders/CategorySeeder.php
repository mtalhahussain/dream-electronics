<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobile Phones',
                'slug' => 'mobile-phones',
                'description' => 'Smartphones and feature phones',
                'icon' => 'bi-phone',
                'color' => '#007bff',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'description' => 'Laptops and notebooks',
                'icon' => 'bi-laptop',
                'color' => '#6f42c1',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Television',
                'slug' => 'television',
                'description' => 'Smart TVs and LED displays',
                'icon' => 'bi-tv',
                'color' => '#dc3545',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Refrigerator',
                'slug' => 'refrigerator',
                'description' => 'Refrigerators and freezers',
                'icon' => 'bi-snow2',
                'color' => '#20c997',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Washing Machine',
                'slug' => 'washing-machine',
                'description' => 'Washing machines and dryers',
                'icon' => 'bi-circle',
                'color' => '#fd7e14',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Air Conditioner',
                'slug' => 'air-conditioner',
                'description' => 'Air conditioners and cooling systems',
                'icon' => 'bi-wind',
                'color' => '#0dcaf0',
                'sort_order' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Home Appliances',
                'slug' => 'home-appliances',
                'description' => 'Other home appliances and electronics',
                'icon' => 'bi-house-gear',
                'color' => '#6c757d',
                'sort_order' => 7,
                'is_active' => true
            ],
            [
                'name' => 'Audio & Video',
                'slug' => 'audio-video',
                'description' => 'Sound systems, speakers, and entertainment devices',
                'icon' => 'bi-speaker',
                'color' => '#e83e8c',
                'sort_order' => 8,
                'is_active' => true
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}

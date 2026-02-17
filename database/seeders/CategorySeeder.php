<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Escort',
            'Massage',
            'Adult Entertainment',
            'Companionship',
            'Dating',
            'Adult Services',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'is_active' => true,
                'listing_count' => 0,
            ]);
        }

        $this->command->info('Created ' . count($categories) . ' categories');
    }
}


<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        $subCategories = [11,12];
        $subCatRandKey = array_rand($subCategories);

        $brands = [4,5,6,7,8,12];
        $brandRandKey = array_rand($brands);
        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => 42,
            'sub_category_id' => $subCategories[$subCatRandKey],
            'brand_id' => $brands[$brandRandKey],
            'price' => rand(100, 1000),
            'sku' => rand(50, 500),
            'track_qty' => 'Yes',
            'qty' => 14,
            'is_featured' => 'Yes',
            'status' => 1,


        ];
    }
}

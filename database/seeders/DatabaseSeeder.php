<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $categories = Category::factory(5)->create();
        $suppliers  = Supplier::factory(8)->create();

        $categories->each(function (Category $category) use ($suppliers) {
            $products = Product::factory(6)->create(['category_id' => $category->id]);

            $products->each(function (Product $product) use ($suppliers) {
                $subset = $suppliers->random(rand(1, 3));
                foreach ($subset as $supplier) {
                    $product->suppliers()->attach($supplier->id, [
                        'cost_price' => fake()->randomFloat(2, 1, 500),
                    ]);
                }
            });
        });

        Product::factory(3)->inactive()->create(['category_id' => $categories->first()->id]);
        Product::factory(4)->lowStock()->create(['category_id' => $categories->last()->id]);
    }
}

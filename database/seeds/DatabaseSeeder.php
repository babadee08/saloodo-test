<?php

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $product_type = ProductType::find(1);

        $products = factory(Product::class, 10)->create(['product_type_id' => $product_type->id]);

        $products->map(function ($product) {
            $product->price()->create([
                'price' => 1000,
                'discount' => 200
            ]);
        });

        factory(\App\Models\User::class, 5)->create();
    }
}

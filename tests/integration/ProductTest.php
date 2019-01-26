<?php

use App\Models\Product;
use App\Models\ProductType;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function a_product_has_a_type()
    {
        $product_type = ProductType::find(1);

        $product = factory(Product::class)->create(['product_type_id' => $product_type->id]);

        $this->assertEquals($product->productType->id, $product_type->id);
    }

    /**
     * @test
     */
    public function a_product_has_a_price()
    {
        $product = factory(Product::class)->create();

        $this->assertFalse($product->hasPrice());

        $product->price()->create([
            'price' => 1000,
            'discount' => 200
        ]);

        $product->refresh();

        $this->assertTrue($product->hasPrice());
    }

}

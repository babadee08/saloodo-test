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

}

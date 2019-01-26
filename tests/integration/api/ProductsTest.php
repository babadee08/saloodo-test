<?php


use App\Models\Product;
use App\Models\ProductType;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function anyone_can_get_a_list_of_all_products()
    {
        $this->createTestProducts();

        $this->get('/api/products')->seeJson([
            'status' => 'success',
            'message' => 'Successfully fetched all products'
        ]);
    }

    /**
     * @test
     */
    public function anyone_can_see_a_single_product()
    {
        $products = $this->createTestProducts();
        $first_product = $products[0];

        $this->get('/api/products/' . $first_product->id)->seeJsonContains([
            'status' => 'success',
            'message' => 'Successfully fetched a single product',
            'name' => $first_product->name
        ]);
    }


    public function createTestProducts()
    {
        $product_type = ProductType::find(1);

        $products = factory(Product::class, 10)->create(['product_type_id' => $product_type->id]);

        $products->map(function ($product) {
            $product->price()->create([
                'price' => 1000,
                'discount' => 200
            ]);
        });

        return $products;
    }
}

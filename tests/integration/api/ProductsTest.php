<?php


use App\Components\TokenManager;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductsTest extends TestCase
{
    use DatabaseTransactions;

    private $faker;

    public function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

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


    /**
     * @return mixed
     */
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

    /**
     * @test
     */
    public function a_single_product_can_be_created_by_admin()
    {
        //given that we have a admin with access token
        $user = factory(User::class)->create(['is_admin' => true]);

        // create access token for user
        $apiToken = TokenManager::generateApiToken();

        $user->api_token = $apiToken;
        $user->save();

        // given that we have a product create data
        $product_data = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'sku' => $this->faker->ean8,
            'qty' => 5,
            'product_type_id' => 1,
            'price' => 5000, // remember this is in cents
        ];

        $header = [
            'Authorization' => $apiToken
        ];

        $this->post('/api/products', $product_data, $header)
            ->seeJsonContains([
                'status' => 'success',
                'message' => 'product successfully created',
                'name' => $product_data['name']
            ]);
    }

}

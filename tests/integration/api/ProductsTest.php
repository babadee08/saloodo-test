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
     * @param int $total
     * @return mixed
     */
    public function createTestProducts(int $total = 10)
    {
        $product_type = ProductType::find(1);

        $products = factory(Product::class, $total)->create(['product_type_id' => $product_type->id]);

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
        $apiToken = $this->generateValidToken();

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

    /**
     * @test
     */
    public function a_product_bundle_can_be_created()
    {
        $products = $this->createTestProducts(3);

        $valid_product_ids = $products->pluck('id')->all();

        // given that we have a product create data
        $product_data = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'sku' => $this->faker->ean8,
            'qty' => 5,
            'product_type_id' => 2, // product type for bundle
            'price' => 5000, // remember this is in cents
            'products' => $valid_product_ids
        ];

        $header = [
            'Authorization' => $this->generateValidToken()
        ];

        $this->post('/api/products', $product_data, $header)
            ->seeJsonContains([
                'status' => 'success',
                'message' => 'product successfully created',
                'name' => $product_data['name']
            ]);
    }

    /**
     * @return string
     */
    public function generateValidToken(): string
    {
        //given that we have a admin with access token
        $user = factory(User::class)->create(['is_admin' => true]);

        // create access token for user
        $apiToken = TokenManager::generateApiToken();

        $user->api_token = $apiToken;
        $user->save();

        return $apiToken;
    }

}

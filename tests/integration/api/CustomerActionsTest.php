<?php

use App\Components\TokenManager;
use App\Models\User;
use Faker\Factory;

class CustomerActionsTest extends TestCase
{
    private $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    /**
     * @return string
     */
    public function generateValidUserToken(): string
    {
        //given that we have a admin with access token
        $user = factory(User::class)->create();

        // create access token for user
        $apiToken = TokenManager::generateApiToken();

        $user->api_token = $apiToken;
        $user->save();

        return $apiToken;
    }

    /**
     * @test
     */
    public function a_user_can_add_item_to_cart()
    {
        $products = $this->createTestProducts(3);

        $valid_product_ids = $products->pluck('id')->all();

        $header = [
            'Authorization' => $this->generateValidUserToken()
        ];

        foreach ($valid_product_ids as $product_id) {
            $post_data = [
                'product_id' => $product_id,
                'qty' => 2,
            ];
            $this->post('/api/cart', $post_data, $header)
                ->seeJsonContains([
                    'status' => 'success',
                    'message' => 'item added to cart',
                    'product_id' => $valid_product_ids[0]
                ]);
        }
    }

    /**
     * @test
     */
    public function a_user_can_retrieve_cart_items()
    {
        $header = [
            'Authorization' => $this->generateValidUserToken()
        ];

        $this->get('/api/cart', $header)
            ->seeJsonContains([
                'status' => 'success',
                'message' => 'cart items',
                'price' => "10.00"
            ]);
    }

    /**
     * @test
     */
    public function a_user_can_checkout_their_cart()
    {
        $body = [
            'address' => $this->faker->address,
            'payment_method' => $this->faker->creditCardType
        ];

        $header = [
            'Authorization' => $this->generateValidUserToken()
        ];

        $this->post('/api/cart/checkout', $body, $header)
            ->seeJsonContains([
                'status' => 'success',
                'message' => 'you order has been received',
                'id' => 1,
                'total_price' => '60.00'
            ]);
    }

}

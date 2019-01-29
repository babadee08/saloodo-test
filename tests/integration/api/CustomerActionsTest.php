<?php

use App\Components\TokenManager;
use App\Models\User;

class CustomerActionsTest extends TestCase
{

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
                'unit_price' => "10.00"
            ]);
    }

}

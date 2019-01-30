<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OrderTest extends TestCase
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
    public function a_user_can_create_an_order()
    {
        $user = factory(User::class)->create();

        $user->createOrder([
            'address' => $this->faker->address,
            'payment_method' => $this->faker->creditCardType,
            'total_price' => 1000
        ]);

        $this->assertEquals($user->orders->count(), 1);
    }

    /**
     * @test
     */
    public function an_item_can_be_added_to_an_order()
    {
        $order = factory(Order::class)->create();

        $products = factory(Product::class, 3)->create();

        $products->map(function ($product) use ($order) {
            $order->orderItems()
                ->create([
                    'product_id' => $product->id,
                    'qty' => 5,
                    'price' => 5000
                ]);
        });

        $this->assertEquals($order->orderItems->count(), 3);
    }
}

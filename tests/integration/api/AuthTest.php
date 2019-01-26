<?php

use App\Models\User;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
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
    public function a_user_can_register()
    {
        $user_data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password'
        ];

        $this->post('/api/user/register', $user_data)->seeJsonContains([
            'status' => 'success',
            'message' => 'successfully created a new user',
            'email' => $user_data['email']
        ]);

    }

    /**
     * @test
     */
    public function registered_user_can_login()
    {
        $user = factory(User::class)->create();

        $login_details = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $this->post('/api/user/login', $login_details)->seeJsonContains([
            'status' => 'success',
            'message' => 'access token issued'
        ]);
    }

}

<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    // products endpoints
    $router->get('/products', 'ProductsController@index');
    $router->post('/products', 'ProductsController@create');
    $router->get('/products/{id}', 'ProductsController@getProductDetail');
    $router->put('/products/{id}', 'ProductsController@updateProduct');

    // auth endpoints
    $router->post('/user/register', 'AuthController@createUser');
    $router->post('/user/login', 'AuthController@login');

    // user endpoints
    $router->get('/user/order', 'UserController@getAllOrder');
    $router->get('/user/order/{id}', 'UserController@getOrder');

    // user actions
    $router->get('/cart/add', 'CartController@addItemToCart');
    $router->get('/cart', 'CartController@getCart');
    $router->post('/cart/checkout', 'CartController@checkout');

});

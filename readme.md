## Saloodo Backend Developer Test

[![Build Status](https://travis-ci.org/babadee08/saloodo-test.svg?branch=master)](https://travis-ci.org/babadee08/saloodo-test)

This is a simplified prototype app that provide a set of rest interfaces to perform product management actions by admins and can also enable customers place orders on the products.

Even though the suggested framework was [Symfony](https://symfony.com/), The Project is based off [Laravel-Lumen](https://lumen.laravel.com) instead. The reason for this is that Lumen is based off Symfony components and they share the same architectural design, concepts and patterns. 

Beside Laravel-lumen, this project uses other tools like:
- [Redis](https://redis.io/)
- [Lumen Route List Display](https://github.com/appzcoder/lumen-route-list)

## Installation

Development environment requirements :
- [Docker](https://www.docker.com) >= 17.06 CE
- [Docker Compose](https://docs.docker.com/compose/install/)

Setting up your development environment on your local machine :
```bash
$ git clone git@github.com:babadee08/saloodo-test.git
$ git clone https://github.com/babadee08/saloodo-test.git
$ cd saloodo-test
$ cp .env.example .env
$ docker-compose run --rm --no-deps webapp composer install
$ docker-compose up -d
```

Now you can access the application via [http://localhost:8000](http://localhost:8000).

**There is no need to run ```php artisan serve```. PHP is already running in a dedicated container.**

## Before starting
You need to run the migrations with the seeds :
```bash
$ docker-compose run --rm webapp php artisan migrate --seed
```

This will create two new user (Customer and Admin) that you can use to interact with the api :
```yml
// Admin Access
email: admin@saloodo.test
password: administrator

// Customer Access
email: user@saloodo.test
password: password
```
## Useful commands

Running tests :
```bash
$ docker-compose run --rm --no-deps webapp ./vendor/bin/phpunit --cache-result --order-by=defects --stop-on-defect --debug --coverage-text
```

Seeding the database :
```bash
$ docker-compose run --rm webapp php artisan db:seed
```

List all available endpoints
```bash
$ docker-compose run --rm --no-deps webapp php artisan route:list
```

## Accessing the API

Clients can access to the REST API. API requests require authentication via token. You can create a new token in your user profile.

Then, you can use this token in Authorization header :

```bash

# Authorization Header
curl --header "Authorization: your_private_token_here" http://localhost:8000/api/posts
```
All API endpoints are prefixed by ```api```.

Here is also a link to the [Postman Collection](https://www.getpostman.com/collections/036dd8b36ae0c47def37) to access a lot of the endpoints

Of course, you need to call the login endpoint with the appropriate user to get a valid `access token` that can be used

The table below shows the available endpoints and the level of permission attached to each one of them


```
+------+--------------------+-----------------+----------------------+
| Verb | Path               | Permissions     | Action               |
+------+--------------------+-----------------+----------------------+
| GET  | /                  | Unauthenticated | Framework info       |
| GET  | /api/products      | Customer        | Fetch all Products   |
| POST | /api/products      | Admin           | Create Product       |
| GET  | /api/products/{id} | Unauthenticated | Get product Details  |
| PUT  | /api/products/{id} | Admin           | Update Product       |
| POST | /api/register      | Unauthenticated | Register             |
| POST | /api/login         | Unauthenticated | Login                |
| GET  | /api/orders        | Customer        | Get all Orders       |
| GET  | /api/orders/{id}   | Customer        | Get Order details    |
| POST | /api/cart          | Customer        | Add products to cart |
| GET  | /api/cart          | Customer        | Get cart content     |
| POST | /api/cart/checkout | Customer        | Checkout cart        |
+------+--------------------+-----------------+----------------------+
```

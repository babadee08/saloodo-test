<?php

namespace App\Services;

use App\Components\CustomException;
use App\Components\ErrorMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CartService
{
    const CACHE_PREFIX = 'CART';
    const DEFAULT_TIMEOUT = 60 * 60; // in minutes

    private $userID;

    public function __construct(Request $request)
    {
        $this->userID = $request->user()->id;
    }

    /**
     * @param array $data
     * @return array
     * @throws CustomException
     */
    public function addToCart(array $data) : array
    {
        $product = Product::with('price')->find($data['product_id']);

        if (empty($product)) {
            throw new CustomException('Invalid Product id', ErrorMessage::RECORD_EXISTING);
        }

        if ($product->qty < $data['qty']) {
            throw new CustomException('Product out of stock', ErrorMessage::OUT_OF_STOCK);
        }

        $cache_key = $this->getCacheKey();

        if ($cart = Cache::get($cache_key)) {

            if (array_key_exists($data['product_id'], $cart)) {
                $old_qty = $cart[$data['product_id']]['qty'];

                $data['qty'] = $old_qty + $data['qty'];
            }

            $this->updateCart($data, $product, $cart);

        } else {

            $this->updateCart($data, $product, $cart);

        }

        return Cache::get($cache_key);
    }

    /**
     * @return array
     */
    public function getCart() : array
    {
        $cache_key = $this->getCacheKey();

        if ($value = Cache::get($cache_key)) {

            return $value;
        }

        return [];
    }

    /**
     * @return array
     */
    public function clearCart() : array
    {
        $cache_key = $this->getCacheKey();

        return Cache::pull($cache_key);
    }

    /**
     * @return string
     */
    private function getCacheKey() : string
    {
        return self::CACHE_PREFIX . ':' . $this->userID;
    }

    /**
     * @param array $data
     * @param $product
     * @param $cart
     */
    public function updateCart(array $data, $product, $cart): void
    {
        $cache_key = $this->getCacheKey();

        $data['name'] = $product->name;
        $data['unit_price'] = $product->price->final_price;
        $data['total'] = number_format(($data['unit_price'] * $data['qty']), 2);

        $cart[$data['product_id']] = $data;

        Cache::put($cache_key, $cart, self::DEFAULT_TIMEOUT);
    }
}
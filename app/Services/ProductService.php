<?php

namespace App\Services;


use App\Components\CustomException;
use App\Components\ErrorMessage;
use App\Models\Product;

class ProductService
{
    public function __construct()
    {
    }

    /**
     * @param int $id
     * @return Product|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws CustomException
     */
    public function getSingleProduct(int $id)
    {
        $product = Product::with('price')->find($id);

        if (is_null($product)) {
            throw new CustomException('Invalid Product id', ErrorMessage::RECORD_EXISTING);
        }

        return $product;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createProduct(array $data)
    {
        $data = collect($data);

        $product_attributes = ['name', 'description', 'sku', 'qty', 'product_type_id'];

        $product_data = $data->only($product_attributes);

        $product = Product::create($product_data->all());

        if ($data->contains('products', array()) && $product->isBundle()) {
            // create the sub products
            $this->addBundleProducts($data->all(), $product);

        }

        $this->createProductPrice($data->all(), $product);

        return $product;
    }

    /**
     * @param array $data
     * @param Product $product
     */
    public function createProductPrice(array $data, Product $product): void
    {
        $price_attributes = ['price', 'discount', 'discount_percentage'];

        $price_data = [];

        foreach ($price_attributes as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $price_data[$attribute] = $data[$attribute];
            }
        }

        //Create product price entry
        $product->price()->create($price_data);
    }

    /**
     * @param array $data
     * @param Product $product
     */
    public function addBundleProducts(array $data, Product $product): void
    {
        $sub_products = $data['products'];

        foreach ($sub_products as $product_id) {
            $product->bundle()->create(['product_id' => $product_id]);
        }
    }
}

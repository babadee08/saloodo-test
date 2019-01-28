<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    const SIMPLE_PRODUCT_ID = 1;
    const BUNDLE_PRODUCT_ID = 2;

    protected $table = 'product_type';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function productType()
    {
        return $this->hasMany(Product::class);
    }
}

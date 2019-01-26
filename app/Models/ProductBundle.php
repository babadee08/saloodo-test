<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    protected $table = 'products_bundle';

    protected $fillable = ['product_id'];

    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'bundle_id', 'id');
    }

}

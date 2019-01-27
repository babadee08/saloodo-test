<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property integer product_type_id
 * @property integer price
 */
class Product extends Model
{
    protected $table = 'products';

    protected $fillable = ['name', 'description', 'sku', 'qty', 'product_type_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function price()
    {
        return $this->hasOne(Price::class);
    }

    /**
     * @return bool
     */
    public function hasPrice() : bool
    {
        if ($this->price !== null) {
            return true;
        }
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bundle()
    {
        if ($this->product_type_id == ProductType::BUNDLE_PRODUCT_ID) {
            return $this->hasMany(ProductBundle::class, 'bundle_id');
        }
    }

}

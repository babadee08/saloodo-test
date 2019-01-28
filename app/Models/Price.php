<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed price
 * @property mixed discount
 * @property bool discount_active
 */
class Price extends Model
{
    protected $table = 'prices';

    protected $fillable = ['price', 'discount', 'discount_percentage', 'discount_active'];

    protected $hidden = ['id', 'product_id', 'created_at', 'updated_at'];

    protected $appends = ['final_price'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param $value
     * @return float|int
     * Accessors to help format price
     */
    public function getPriceAttribute($value)
    {
        return number_format( $value / 100, 2);
    }

    /**
     * @param $value
     * @return string
     * Accessors to help format discount price
     */
    public function getDiscountAttribute($value)
    {
        return number_format( $value / 100, 2);
    }

    /**
     * @return string
     * Accessors to help compute the final price
     */
    public function getFinalPriceAttribute()
    {
        if (!$this->discount_active) {
            return number_format( $this->price, 2);
        }

        if (!is_null($this->discount)) {
            $final_price =  $this->price - $this->discount;
            return number_format( $final_price, 2);
        }
    }

    /**
     * @param $value
     * Mutator to set the value of price to cents
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = ($value * 100);
    }

    /**
     * @param $value
     * Mutator to set the value of price to cents
     */
    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = ($value * 100);
    }

}

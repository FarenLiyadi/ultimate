<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QtyPricingRule extends Model
{
    protected $fillable = [
        'variation_id','unit_id','price_group_id',
        'min_qty','discount_type','discount_value',
        'location_id','valid_from','valid_to'
    ];

    public function variation(){ return $this->belongsTo(\App\Variation::class); }
    public function unit(){ return $this->belongsTo(\App\Unit::class); }
    public function priceGroup(){ return $this->belongsTo(\App\SellingPriceGroup::class, 'price_group_id'); }

    public function scopeActive($q){
        $today = now()->toDateString();
        return $q->where(function($qq) use($today){
            $qq->whereNull('valid_from')->orWhere('valid_from','<=',$today);
        })->where(function($qq) use($today){
            $qq->whereNull('valid_to')->orWhere('valid_to','>=',$today);
        });
    }
}

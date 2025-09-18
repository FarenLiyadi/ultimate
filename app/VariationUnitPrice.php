<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationUnitPrice extends Model
{
    use HasFactory;
    protected $fillable = ['variation_id','unit_id','price_group_id','price_inc_tax'];

    public function variation(){ return $this->belongsTo(\App\Variation::class); }
    public function unit(){ return $this->belongsTo(\App\Unit::class); }
    public function priceGroup(){ return $this->belongsTo(\App\SellingPriceGroup::class, 'price_group_id'); }
}

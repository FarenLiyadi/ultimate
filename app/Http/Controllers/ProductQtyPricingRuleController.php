<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\QtyPricingRule;
use Illuminate\Support\Facades\DB;

class ProductQtyPricingRuleController extends Controller
{
    public function store(Product $product, Request $request)
    {
        // Struktur:
        // qty_rules[variation_id][unit_id][] = {
        //   price_group_id|default, min_qty, discount_type, discount_value, location_id, valid_from, valid_to
        // }
        $data = $request->validate([
            'qty_rules' => 'array',
        ]);

        DB::transaction(function () use ($product, $data) {
            $variationIds = $product->variations()->pluck('id');
            QtyPricingRule::whereIn('variation_id', $variationIds)->delete();

            foreach (($data['qty_rules'] ?? []) as $variation_id => $units) {
                foreach ($units as $unit_id => $tiers) {
                    foreach ($tiers as $row) {
                        if (empty($row['min_qty'])) continue; // lewati baris kosong

                        QtyPricingRule::create([
                            'variation_id'  => (int)$variation_id,
                            'unit_id'       => (int)$unit_id,
                            'price_group_id'=> (isset($row['price_group_id']) && $row['price_group_id'] !== 'default')
                                                ? (int)$row['price_group_id'] : null,
                            'min_qty'       => (int)$row['min_qty'],
                            'discount_type' => in_array(($row['discount_type'] ?? 'fixed'), ['fixed','percent']) ? $row['discount_type'] : 'fixed',
                            'discount_value'=> (float)($row['discount_value'] ?? 0),
                            'location_id'   => !empty($row['location_id']) ? (int)$row['location_id'] : null,
                            'valid_from'    => $row['valid_from'] ?? null,
                            'valid_to'      => $row['valid_to'] ?? null,
                        ]);
                    }
                }
            }
        });

        return back()->with('status','Diskon qty per satuan tersimpan.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Variation;
use App\VariationUnitPrice;
use App\QtyPricingRule;
use Illuminate\Support\Facades\DB;

class ProductPricingController extends Controller
{
    public function store(Product $product, Request $request)
{
    // dd($request->all());
    $data = $request->validate([
        'unit_prices' => 'array',
        'qty_rules'   => 'array',
    ]);

    DB::transaction(function () use ($product, $data) {

        $variationIds = $product->variations()->pluck('id')->all();

        /* ============================================================
         * 1) SIMPAN HARGA MULTI-SATUAN PER LOCATION
         * ============================================================ */

        foreach (($data['unit_prices'] ?? []) as $locId => $variations) {

            // Hapus harga lama untuk lokasi ini (termasuk yg NULL legacy)
            VariationUnitPrice::whereIn('variation_id', $variationIds)
                ->where(function ($q) use ($locId) {
                    $q->where('location_id', $locId)
                      ->orWhereNull('location_id');
                })
                ->delete();

            // Insert / Update harga baru
            foreach ($variations as $variation_id => $units) {
                foreach ($units as $unit_id => $groups) {
                    foreach ($groups as $pgid => $price_inc_tax) {

                        if ($price_inc_tax === null || $price_inc_tax === '') continue;

                        
                        VariationUnitPrice::updateOrCreate(
                            [
                                'location_id'    => (int)$locId,
                                'variation_id'   => (int)$variation_id,
                                'unit_id'        => (int)$unit_id,
                                'price_group_id' => $pgid === 'default' ? null : (int)$pgid,
                            ],
                            [
                                'price_inc_tax'  => (float)$price_inc_tax,
                            ]
                        );

                    }
                }
            }
        }

        /* ============================================================
         * 2) SINKRON DEFAULT SELL PRICE
         * ============================================================ */

        $baseUnitId = (int) $product->unit_id;
        $taxPct = optional($product->tax)->amount ? (float)$product->tax->amount : 0.0;
        $div = $taxPct > 0 ? (1 + $taxPct/100) : 1;

        foreach (($data['unit_prices'] ?? []) as $locId => $variations) {
            foreach ($variations as $vid => $units) {

                $incDefault = $units[$baseUnitId]['default'] ?? null;

                if ($incDefault !== null && $incDefault !== '') {
                    $inc = (float)$incDefault;
                    $ex  = $div > 0 ? round($inc / $div, 4) : $inc;

                    Variation::where('id', (int)$vid)->update([
                        'sell_price_inc_tax' => $inc,
                        'default_sell_price' => $ex,
                    ]);
                }

            }
        }

        /* ============================================================
         * 3) SIMPAN QTY PRICING RULES
         * ============================================================ */

        QtyPricingRule::whereIn('variation_id', $variationIds)->delete();

        foreach (($data['qty_rules'] ?? []) as $variation_id => $units) {
            foreach ($units as $unit_id => $tiers) {
                foreach ($tiers as $row) {

                    if (!isset($row['min_qty']) || $row['min_qty'] === '') continue;

                    $pg = $row['price_group_id'] ?? null;
                    $pgId = ($pg === '' || $pg === null || $pg === 'default')
                        ? null
                        : ((int)$pg ?: null);

                    QtyPricingRule::create([
                        'variation_id'   => (int)$variation_id,
                        'unit_id'        => (int)$unit_id,
                        'price_group_id' => $pgId,
                        'min_qty'        => (int)$row['min_qty'],
                        'discount_type'  => in_array(($row['discount_type'] ?? 'fixed'), ['fixed','percent'])
                                                ? $row['discount_type'] : 'fixed',
                        'discount_value' => (float)($row['discount_value'] ?? 0),
                        'location_id'    => !empty($row['location_id']) ? (int)$row['location_id'] : null,
                        'valid_from'     => $row['valid_from'] ?? null,
                        'valid_to'       => $row['valid_to'] ?? null,
                    ]);
                }
            }
        }

    });

    return redirect('products')->with('status', [
        'success' => 1,
        'msg' => __('product.product_updated_success'),
    ]);
}
}

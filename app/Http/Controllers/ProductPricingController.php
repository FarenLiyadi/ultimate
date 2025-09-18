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
        // keduanya opsional (boleh kosong)
        $data = $request->validate([
            'unit_prices' => 'array',
            'qty_rules'   => 'array',
        ]);

        DB::transaction(function () use ($product, $data) {
            $variationIds = $product->variations()->pluck('id')->all();

            /* ====== 1) Simpan harga per-satuan ====== */
            VariationUnitPrice::whereIn('variation_id', $variationIds)->delete();

            foreach (($data['unit_prices'] ?? []) as $variation_id => $units) {
                foreach ($units as $unit_id => $groups) {
                    foreach ($groups as $pgid => $price_inc_tax) {
                        if ($price_inc_tax === null || $price_inc_tax === '') continue;
                        VariationUnitPrice::create([
                            'variation_id'   => (int)$variation_id,
                            'unit_id'        => (int)$unit_id,
                            'price_group_id' => $pgid !== 'default' ? (int)$pgid : null,
                            'price_inc_tax'  => (float)$price_inc_tax,
                        ]);
                    }
                }
            }

            /* ====== 2) Sinkron ke variations (default base-unit) ====== */
            $baseUnitId = (int) $product->unit_id;
            $taxPct = optional($product->tax)->amount ? (float)$product->tax->amount : 0.0;
            $div = $taxPct > 0 ? (1 + $taxPct/100) : 1;

            foreach (($data['unit_prices'] ?? []) as $vid => $units) {
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

            /* ====== 3) Simpan aturan diskon qty ====== */
            QtyPricingRule::whereIn('variation_id', $variationIds)->delete();

           foreach (($data['qty_rules'] ?? []) as $variation_id => $units) {
    foreach ($units as $unit_id => $tiers) {
        foreach ($tiers as $row) {
            if (!isset($row['min_qty']) || $row['min_qty'] === '' ) continue;

            // --- NORMALISASI price_group_id ---
            $pg = $row['price_group_id'] ?? null;
            // treat "", null, "default" as NULL
            if ($pg === '' || $pg === null || $pg === 'default') {
                $pgId = null;
            } else {
                // if something weird like JSON mistakenly sent, force to int and drop 0
                $pgId = (int) $pg;
                if ($pgId <= 0) { $pgId = null; }
            }

            QtyPricingRule::create([
                'variation_id'   => (int)$variation_id,
                'unit_id'        => (int)$unit_id,
                'price_group_id' => $pgId,           // <- hasil normalisasi
                'min_qty'        => (int)$row['min_qty'],
                'discount_type'  => in_array(($row['discount_type'] ?? 'fixed'), ['fixed','percent']) ? $row['discount_type'] : 'fixed',
                'discount_value' => (float)($row['discount_value'] ?? 0),
                'location_id'    => !empty($row['location_id']) ? (int)$row['location_id'] : null,
                'valid_from'     => $row['valid_from'] ?? null,
                'valid_to'       => $row['valid_to'] ?? null,
            ]);
        }
    }
}
        });
$output = ['success' => 1,
                'msg' => __('product.product_updated_success'),
            ];
  
            return redirect('products')->with('status', $output);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Variation;
use App\VariationUnitPrice;
use Illuminate\Support\Facades\DB;
class ProductVariationUnitPriceController extends Controller
{
     public function store(Product $product, Request $request)
    {
        // Struktur: unit_prices[variation_id][unit_id][price_group_id|null] = price_inc_tax
        $data = $request->validate([
            'unit_prices' => 'array',
        ]);

        DB::transaction(function () use ($product, $data) {
        $variationIds = $product->variations()->pluck('id')->all();

        // ambil base unit produk (PCS atau yang jadi satuan dasar)
        $baseUnitId = (int) $product->unit_id;

        // ambil tax % dari product (kalau ada)
        $taxPct = optional($product->tax)->amount ? (float)$product->tax->amount : 0.0;
        $divisor = $taxPct > 0 ? (1 + $taxPct/100) : 1;

        // data nested yang dikirim form
        $unitPricesInput = $data['unit_prices'] ?? [];

        // loop tiap variation → jika ada nilai untuk baseUnit & default, sinkronkan
        foreach ($variationIds as $vid) {
            $incDefault = $unitPricesInput[$vid][$baseUnitId]['default'] ?? null;
            if ($incDefault !== null && $incDefault !== '') {
                $inc = (float) $incDefault;
                $ex  = $divisor > 0 ? round($inc / $divisor, 4) : $inc;

                Variation::where('id', $vid)->update([
                    'sell_price_inc_tax'   => $inc,   // inc-tax konsisten
                    'default_sell_price'   => $ex,    // ex-tax konsisten
                ]);
            }
        }
        });

        return back()->with('status','Harga per satuan tersimpan.');
    }
}

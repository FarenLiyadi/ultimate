@php
    // mapping existing prices
    $existing_prices = [];
    foreach ($unitPricesForLocation as $p) {
        $gkey = $p->price_group_id ?? 'default';
        $existing_prices[$p->variation_id][$p->unit_id][$gkey] = $p->price_inc_tax;
    }

    $fmt2 = fn($val) => $val === null ? '' : number_format((float)$val, 2, '.', '');
@endphp

<style>
    .unit-block {
        margin-bottom: 12px;
        border: 1px solid #e3e3e3;
        border-radius: 6px;
        padding: 10px 14px;
        background: #fdfdfd;
    }
    .unit-table thead th {
        background: #f5f7fa;
        text-align: center;
        border-bottom: 2px solid #dcdcdc !important;
        font-weight: 600;
    }
    .unit-table td {
        vertical-align: middle !important;
        padding: 6px 8px;
    }
    .unit-table td:first-child {
        font-weight: 600;
        white-space: nowrap;
    }
    .unit-table input {
        text-align: right;
        font-weight: 600;
    }
</style>

@foreach ($product->variations as $v)

    <div class="unit-block">

        <div class="mb-2" style="font-size:15px; font-weight:600;">
            {{ $product->type == 'variable'
                ? $v->product_variation->name . ' – ' . $v->name
                : $product->name
            }}
            <span class="text-muted">({{ $v->sub_sku }})</span>
        </div>

        <table class="table table-bordered table-sm unit-table">
            <thead>
                <tr>
                    <th style="width:130px;">Unit</th>
                    <th style="width:110px;">Default</th>
                    @foreach ($price_groups_dropdown as $pgid => $pgname)
                        <th style="width:110px;">{{ $pgname }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>

                @foreach ($units_for_product as $uid => $uname)

                    @php
                        $default_val = $existing_prices[$v->id][$uid]['default'] ?? null;

                        if ($default_val === null && $uid == $product->unit_id) {
                            $default_val = $v->sell_price_inc_tax;
                        }
                    @endphp

                    <tr>
                        {{-- Unit --}}
                        <td>{{ $uname }}</td>

                        {{-- Default price --}}
                        <td>
                            <input  type="number" step="0.01" min="0"
                                    class="form-control form-control-sm"
                                    name="unit_prices[{{ $location_id }}][{{ $v->id }}][{{ $uid }}][default]"
                                    value="{{ $fmt2($default_val) }}">
                        </td>

                        {{-- Price group --}}
                        @foreach ($price_groups_dropdown as $pgid => $pgname)
                            @php
                                $gval = $existing_prices[$v->id][$uid][$pgid] ?? null;
                            @endphp

                            <td>
                                <input  type="number" step="0.01" min="0"
                                        class="form-control form-control-sm"
                                        name="unit_prices[{{ $location_id }}][{{ $v->id }}][{{ $uid }}][{{ $pgid }}]"
                                        value="{{ $fmt2($gval) }}">
                            </td>
                        @endforeach

                    </tr>

                @endforeach

            </tbody>
        </table>
    </div>

@endforeach

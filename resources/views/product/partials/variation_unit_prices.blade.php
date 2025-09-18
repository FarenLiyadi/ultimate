<h5 class="mb-8 tw-text-xl">Harga per Satuan 
  <small class="text-muted d-block">Harga dasar per unit & per Price Group. Dipakai sebagai acuan saat menghitung harga tier Qty.</small>
</h5>

@php
  // susun map harga existing: [variation_id][unit_id][group_key] = price
  $existing_prices = [];
  foreach ($unitPrices as $p) {
    $gkey = $p->price_group_id ?? 'default';
    $existing_prices[$p->variation_id][$p->unit_id][$gkey] = $p->price_inc_tax;
  }
  // helper kecil untuk ambil label unit aman
  $unitLabel = function($u, $fallback) {
    if (is_array($u))   return $u['short_name'] ?? $u['name'] ?? $fallback;
    if (is_object($u))  return $u->short_name ?? $u->name ?? $fallback;
    return (string) $u;
  };
  // formatter 2 desimal untuk value input type="number"
  $fmt2 = function($val) {
    if ($val === null || $val === '') return '';
    return number_format((float)$val, 2, '.', ''); // 2 desimal, tanpa pemisah ribuan
  };
  $thousand = session('currency.thousand_separator') ?? ',';
  $decimal  = session('currency.decimal_separator') ?? '.';
  $digits   = 2; // 2 desimal
@endphp

@foreach($product->variations as $v)
  <div class="card mb-3">
    <div class="card-body p-2">
      <table class="table table-sm table-bordered align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 160px;">Unit</th>
            <th style="width: 200px;">Default (Inc. Tax)</th>
            @foreach($price_groups_dropdown as $pgid => $pgname)
              <th style="width: 200px;">{{ $pgname }} (Inc. Tax)</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($units_for_product as $uid => $uname)
            <tr>
              <td><strong>{{ $unitLabel($uname, $uid) }}</strong></td>

             {{-- Default --}}
<td>
  @php
    // Ambil value dari variation_unit_prices kalau ada
    $val = $existing_prices[$v->id][$uid]['default'] ?? null;

    // Kalau belum ada & ini adalah BASE UNIT produk → fallback ke harga jual default (Edit Produk)
    if ($val === null && (int)$uid === (int)$product->unit_id) {
        $val = $v->sell_price_inc_tax; // sama persis dengan yang di Edit Produk
    }
  @endphp

  <input
    type="number" step="0.01" min="0"
    name="unit_prices[{{ $v->id }}][{{ $uid }}][default]"
    class="form-control"
    value="{{ $fmt2($val) }}">  {{-- $fmt2 sudah kamu definisikan: 2 desimal tanpa ribuan --}}
</td>

              {{-- Per group --}}
              @foreach($price_groups_dropdown as $pgid => $pgname)
                @php $gval = $existing_prices[$v->id][$uid][$pgid] ?? null; @endphp
                <td>
                  <input
                    type="number" step="0.01" min="0"
                    name="unit_prices[{{ $v->id }}][{{ $uid }}][{{ $pgid }}]"
                    class="form-control"
                    value="{{ $fmt2($gval) }}">
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endforeach

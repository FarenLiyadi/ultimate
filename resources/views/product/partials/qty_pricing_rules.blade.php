<h5 class="mb-2 tw-text-xl">Harga Khusus Qty per Satuan </h5>

@php
  // kelompokkan rules: [variation_id][unit_id] = list tier
  $rules_map = [];
  foreach ($rules as $r) {
    $rules_map[$r->variation_id][$r->unit_id][] = $r;
  }
  // ambil separator dari setting
  $thousand = session('currency.thousand_separator') ?? '.';
  $decimal  = session('currency.decimal_separator') ?? ',';
@endphp

@foreach($product->variations as $v)
  <div class="card mb-3">
    <div class="card-body p-2">
      @foreach($units_for_product as $uid => $uname)
        <h6 class="mt-2 mb-1 tw-text-lg">Unit: {{ $uname }}</h6>
        <table class="table table-sm table-bordered align-middle">
          <thead>
            <tr>
              <th style="width:140px;">Price Group</th>
              <th style="width:120px;">Min Qty (≥)</th>
              <th style="width:160px;">Harga/Unit (Inc. Tax)</th>
              <th style="width:160px;">Lokasi</th>
              <th style="width:260px;">Periode</th>
              <th style="width:60px;"></th>
            </tr>
          </thead>
          <tbody data-unit="{{ $uid }}" data-var="{{ $v->id }}">
            @php $tier_list = $rules_map[$v->id][$uid] ?? []; @endphp

            @foreach($tier_list as $i => $r)
              <tr>
                {{-- 1) Price Group --}}
                <td>
                  <select name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][price_group_id]" class="form-control">
                    <option value="" @selected(!$r->price_group_id)>Default</option>
                    @foreach($price_groups as $pgid => $pgname)
                      <option value="{{ $pgid }}" @selected($r->price_group_id==$pgid)>{{ $pgname }}</option>
                    @endforeach
                  </select>
                </td>

                {{-- 2) Min Qty --}}
                <td>
                  <input type="number" min="1"
                    name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][min_qty]"
                    value="{{ $r->min_qty }}"
                    class="form-control" required>
                </td>

                {{-- 3) Harga/Unit (Inc. Tax) + hidden discount --}}
                <td>
                  <div class="input-group">
                    <span class="input-group-addon">IDR</span>
                    <input
                      type="text" inputmode="decimal"
                      class="form-control input_number js-final-price"
                      name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][final_price_inc_tax]"
                      value="" placeholder="cth: 13.500"
                      data-var="{{ $v->id }}"
                      data-unit="{{ $uid }}"
                      data-pg="{{ $r->price_group_id ?? '' }}">
                  </div>
                  <input type="hidden" class="js-discount-type"
                        name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][discount_type]"
                        value="{{ $r->discount_type ?? 'fixed' }}">
                  <input type="hidden" class="js-discount-value"
                        name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][discount_value]"
                        value="{{ $r->discount_value ?? 0 }}">
                </td>

                {{-- 4) Lokasi --}}
                <td>
                  <select name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][location_id]" class="form-control">
                    <option value="">Semua</option>
                    @foreach($business_locations as $locId => $locName)
                      <option value="{{ $locId }}" @selected($r->location_id==$locId)>{{ $locName }}</option>
                    @endforeach
                  </select>
                </td>

                {{-- 5) Periode --}}
                <td class="d-flex gap-1">
                  <input type="date" name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][valid_from]" value="{{ $r->valid_from }}" class="form-control">
                  <input type="date" name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][valid_to]"   value="{{ $r->valid_to }}"   class="form-control">
                </td>

                {{-- 6) Aksi --}}
                <td>
                  <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()">Hapus</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <button type="button" class="btn btn-outline-secondary btn-sm add-tier"
                data-var="{{ $v->id }}" data-unit="{{ $uid }}">+ Tambah Tier</button>
        <hr class="my-2">
      @endforeach
    </div>
  </div>
@endforeach

<script>
document.addEventListener('click', function(e){
  if(!e.target.classList.contains('add-tier')) return;

  const varId  = e.target.dataset.var;
  const unitId = e.target.dataset.unit;
  const tbody  = e.target.closest('.card-body')
                   .querySelector(`tbody[data-var="${varId}"][data-unit="${unitId}"]`);
  const nextIdx = tbody.querySelectorAll('tr').length;

  const rowHtml = `
<tr>
  <td>
    <select name="qty_rules[${varId}][${unitId}][${nextIdx}][price_group_id]" class="form-control">
      <option value="">Default</option>
      @foreach($price_groups as $pgid => $pgname)
        <option value="{{ $pgid }}">{{ $pgname }}</option>
      @endforeach
    </select>
  </td>
  <td><input type="number" min="1" name="qty_rules[${varId}][${unitId}][${nextIdx}][min_qty]" value="3" class="form-control" required></td>

  <td>
    <div class="input-group">
      <span class="input-group-addon">IDR</span>
      <input
        type="text" inputmode="decimal"
        class="form-control input_number js-final-price"
        name="qty_rules[${varId}][${unitId}][${nextIdx}][final_price_inc_tax]"
        placeholder="cth: 13.500"
        data-var="${varId}"
        data-unit="${unitId}"
        data-pg="">
    </div>
    <input type="hidden" class="js-discount-type"
           name="qty_rules[${varId}][${unitId}][${nextIdx}][discount_type]" value="fixed">
    <input type="hidden" class="js-discount-value"
           name="qty_rules[${varId}][${unitId}][${nextIdx}][discount_value]" value="0">
  </td>

  <td>
    <select name="qty_rules[${varId}][${unitId}][${nextIdx}][location_id]" class="form-control">
      <option value="">Semua</option>
      @foreach($business_locations as $locId => $locName)
        <option value="{{ $locId }}">{{ $locName }}</option>
      @endforeach
    </select>
  </td>
  <td class="d-flex gap-1">
    <input type="date" name="qty_rules[${varId}][${unitId}][${nextIdx}][valid_from]" class="form-control">
    <input type="date" name="qty_rules[${varId}][${unitId}][${nextIdx}][valid_to]" class="form-control">
  </td>
  <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()">Hapus</button></td>
</tr>`;

  tbody.insertAdjacentHTML('beforeend', rowHtml);

  // apply mask ke field baru
  if (typeof $.fn.inputmask === 'function') {
    $(tbody).find('tr:last .input_number').inputmask(window.__qtyMaskOpts || {});
  }
});
</script>

<script>
window.QTY_DIGITS = 2;

// Opsi Inputmask global (ambil separator dari Blade)
window.__qtyMaskOpts = {
  alias: 'numeric',
  groupSeparator: @json($thousand),
  radixPoint: @json($decimal),
  autoGroup: true,
  digits: window.QTY_DIGITS,
  digitsOptional: false,
  rightAlign: false,
  removeMaskOnSubmit: true,
  allowMinus: false,
  placeholder: '0'
};

// init mask saat load + inisialisasi nilai final dari diskon tersimpan
document.addEventListener('DOMContentLoaded', function(){
  if (typeof $.fn.inputmask === 'function') {
    $('.input_number').inputmask(window.__qtyMaskOpts);
  }
  document.querySelectorAll('tbody[data-var][data-unit] tr').forEach(function(tr){
    recalcRow(tr, /*roundNow=*/true);  // format & isi awal
  });
});

// util: escape karakter ke regex
function escRe(s){ return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

// Ambil angka unmasked (aman utk locale)
function getNumeric(el) {
  if (!el) return NaN;
  if (el.inputmask && typeof el.inputmask.unmaskedvalue === 'function') {
    const raw = el.inputmask.unmaskedvalue(); // '.' sebagai radix oleh inputmask
    return raw === '' ? NaN : parseFloat(raw);
  }
  // fallback tanpa plugin → gunakan separator dari mask opts
  const gs = (window.__qtyMaskOpts?.groupSeparator ?? ',');
  const rp = (window.__qtyMaskOpts?.radixPoint ?? '.');
  let v = (el.value || '').trim();
  if (!v) return NaN;
  v = v.replace(new RegExp(escRe(gs), 'g'), ''); // hapus ribuan
  v = v.replace(new RegExp(escRe(rp), 'g'), '.'); // jadikan '.' sebagai desimal
  return parseFloat(v);
}

// Set angka ke input (format 2 desimal & biar termask)
function setNumeric(el, num) {
  const str = (isFinite(num) ? Number(num).toFixed(window.QTY_DIGITS) : '');
  if (el && el.inputmask && typeof el.inputmask.setValue === 'function') {
    el.inputmask.setValue(str);
  } else if (el) {
    el.value = str;
  }
}

// base price dari "Harga per Satuan" (juga termask)
function findBasePrice(varId, unitId, pg) {
  const gkey = (!pg || pg === 'default') ? 'default' : pg;
  let el = document.querySelector(`input[name="unit_prices[${varId}][${unitId}][${gkey}]"]`);
  if (!el && gkey !== 'default') {
    el = document.querySelector(`input[name="unit_prices[${varId}][${unitId}][default]"]`);
  }
  return getNumeric(el);
}

/**
 * Recalc 1 baris.
 * @param {HTMLElement} row - <tr>
 * @param {boolean} roundNow - kalau true: format/set nilai final ke 2 desimal (akan memicu input); kalau false: hanya hitung & isi hidden.
 */
function recalcRow(row, roundNow=false){
  const fp = row.querySelector('.js-final-price');
  if(!fp) return;

  const varId = fp.dataset.var;
  const unitId = fp.dataset.unit;
  const pg     = fp.dataset.pg;

  const base = findBasePrice(varId, unitId, pg);
  const typeEl = row.querySelector('.js-discount-type');
  const valEl  = row.querySelector('.js-discount-value');

  if (!isFinite(base) || base <= 0) return;

  // final dari input; bila kosong → hitung dari diskon tersimpan
  let finalRaw = getNumeric(fp);
  const dType = (typeEl?.value || 'fixed').toLowerCase();
  const dVal  = parseFloat(valEl?.value || '0');

  if (!isFinite(finalRaw)) {
    // kosong → isi dari diskon tersimpan
    let computed = dType === 'percent'
      ? Math.max(0, base * (1 - (dVal/100)))
      : Math.max(0, base - dVal);
    if (roundNow) setNumeric(fp, computed); // hanya format saat init/blur/submit
    finalRaw = computed;
  } else {
    // user sedang mengetik → jangan format saat input (hindari loop); format saat blur/submit
    finalRaw = Math.max(0, finalRaw);
    if (roundNow) setNumeric(fp, finalRaw);
  }

  // simpan sebagai DISKON FIXED = base - final
  const disc = Math.max(0, base - finalRaw);
  if (typeEl) typeEl.value = 'fixed';
  if (valEl)  valEl.value  = disc.toFixed(window.QTY_DIGITS);
}

/* ===== Listeners ===== */

// Saat user KETIK: hitung saja, tanpa format (roundNow=false)
document.addEventListener('input', function(e){
  if (!e.target.classList.contains('js-final-price')) return;
  recalcRow(e.target.closest('tr'), /*roundNow=*/false);
});

// Saat user BLUR: baru format ke 2 desimal (roundNow=true)
document.addEventListener('blur', function(e){
  if (!e.target.classList.contains('js-final-price')) return;
  recalcRow(e.target.closest('tr'), /*roundNow=*/true);
}, true);

// Ganti Price Group → kosongkan final dan hitung ulang (formatkan)
document.addEventListener('change', function(e){
  if (e.target.name && e.target.name.includes('[price_group_id]')){
    const tr = e.target.closest('tr');
    const fp = tr.querySelector('.js-final-price');
    if (fp){
      fp.dataset.pg = (e.target.value || '');
      setNumeric(fp, NaN);      // kosongkan
      recalcRow(tr, true);      // hitung & format
    }
  }
});

// Sinkron sebelum submit (pastikan hidden discount sudah terisi & final terformat)
const form = document.querySelector('form[action*="products/pricing/save"]');
if (form) {
  form.addEventListener('submit', function(){
    document.querySelectorAll('tbody[data-var][data-unit] tr').forEach(function(tr){
      recalcRow(tr, true);
    });
  });
}
</script>


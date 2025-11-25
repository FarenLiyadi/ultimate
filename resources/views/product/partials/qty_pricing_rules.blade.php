<h5 class="mb-3 tw-text-xl font-bold">Harga Khusus Qty per Satuan</h5>

<style>
    .qty-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
        padding: 16px;
        margin-bottom: 22px;
    }
    .price-warning {
        border: 2px solid #ff4d4d !important;
        background: #ffecec !important;
    }
    .is-invalid {
        border: 2px solid red !important;
        background: #fff0f0 !important;
    }
    .qty-table thead th {
        background: #f3f4f6;
        text-align: center;
        font-weight: 600;
        border-bottom: 2px solid #ddd;
        font-size: 13px;
        white-space: nowrap;
    }
    .qty-unit-title {
        font-weight: 700;
        margin: 10px 0 6px 0;
        font-size: 15px;
        color: #374151;
    }
    .js-base-label {
        font-size: 11px;
        color: #6b7280;
        display: block;
        margin-bottom: 3px;
    }
</style>

@php
    // kelompokkan rules
    $rules_map = [];
    foreach ($rules as $r) {
        $rules_map[$r->variation_id][$r->unit_id][] = $r;
    }

    $thousand = session('currency.thousand_separator') ?? '.';
    $decimal  = session('currency.decimal_separator') ?? ',';
@endphp

@foreach($product->variations as $v)
<div class="qty-card">
    <h5 class="tw-text-lg font-bold mb-2">
        {{ $product->type=='variable' ? $v->product_variation->name . ' – ' . $v->name : $product->name }}
        <small class="text-muted">({{ $v->sub_sku }})</small>
    </h5>

    @foreach($units_for_product as $uid => $uname)
        <div class="qty-unit-title">Unit: {{ $uname }}</div>

        @php $tier_list = $rules_map[$v->id][$uid] ?? []; @endphp

        <table class="table table-sm table-bordered qty-table">
            <thead>
                <tr>
                    <th style="width:150px;">Price Group</th>
                    <th style="width:100px;">Min Qty</th>
                    <th style="width:170px;">Harga Final</th>
                    <th style="width:150px;">Lokasi *</th>
                    <th style="width:250px;">Periode</th>
                    <th style="width:55px;"></th>
                </tr>
            </thead>

            <tbody data-var="{{ $v->id }}" data-unit="{{ $uid }}" data-next="{{ count($tier_list) }}">

            @foreach($tier_list as $i => $r)
                <tr>
                    {{-- Price Group --}}
                    <td>
                        <select name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][price_group_id]"
                                class="form-control js-pg">
                            <option value="">Default</option>
                            @foreach($price_groups as $pgid => $pgname)
                                <option value="{{ $pgid }}" @selected($r->price_group_id==$pgid)>
                                    {{ $pgname }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- Min qty --}}
                    <td>
                        <input type="number" min="1" class="form-control js-minqty"
                        name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][min_qty]"
                        value="{{ $r->min_qty }}" required>
                    </td>

                    {{-- Harga Final --}}
                    <td>
                        <span class="js-base-label"></span>

                        <div class="input-group">
                            <span class="input-group-addon">IDR</span>
                            <input type="text" inputmode="decimal"
                                   class="form-control input_number js-final-price"
                                   name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][final_price_inc_tax]"
                                   data-var="{{ $v->id }}" data-unit="{{ $uid }}"
                                   data-pg="{{ $r->price_group_id ?? '' }}">
                        </div>

                        <input type="hidden" class="js-discount-type"
                               name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][discount_type]"
                               value="{{ $r->discount_type ?? 'fixed' }}">
                        <input type="hidden" class="js-discount-value"
                               name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][discount_value]"
                               value="{{ $r->discount_value ?? 0 }}">
                    </td>

                    {{-- Lokasi --}}
                    <td>
                        <select name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][location_id]"
                                class="form-control js-loc" required>
                            <option value="" disabled selected>Pilih Lokasi</option>
                            @foreach($business_locations as $lid => $lname)
                                <option value="{{ $lid }}" @selected($r->location_id==$lid)>
                                    {{ $lname }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- Periode --}}
                    <td>
                        <div style="display:flex; gap:6px;">
                            <input type="date" class="form-control"
                                   name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][valid_from]"
                                   value="{{ $r->valid_from }}">
                            <input type="date" class="form-control"
                                   name="qty_rules[{{ $v->id }}][{{ $uid }}][{{ $i }}][valid_to]"
                                   value="{{ $r->valid_to }}">
                        </div>
                    </td>

                    {{-- Tools --}}
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                            Hapus
                        </button>

                        <button type="button" class="btn btn-outline-secondary btn-sm duplicate-tier">
                            ×
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <button type="button"
                class="btn btn-sm btn-outline-secondary btn-add-tier"
                data-var="{{ $v->id }}" data-unit="{{ $uid }}">
            + Tambah Tier
        </button>

        <hr>
    @endforeach
</div>
@endforeach

<script>
/* ===============================
   CONFIG
================================*/
window.QTY_DIGITS = 2;

window.__qtyMaskOpts = {
    alias: "numeric",
    groupSeparator: "{{ $thousand }}",
    radixPoint: "{{ $decimal }}",
    autoGroup: true,
    digits: window.QTY_DIGITS,
    digitsOptional: false,
    rightAlign: false,
    removeMaskOnSubmit: true,
    allowMinus: false,
    placeholder: "0"
};

/* ===============================
   HELPERS
================================*/
function escRe(s){
    return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
}

function getNumeric(el){
    if (!el) return NaN;

    // inputmask support
    if (el.inputmask){
        const raw = el.inputmask.unmaskedvalue();
        return raw === "" ? NaN : parseFloat(raw);
    }

    let v = (el.value || "").trim();
    if (!v) return NaN;

    v = v.replace(new RegExp(escRe("{{ $thousand }}"), "g"), "");
    v = v.replace(new RegExp(escRe("{{ $decimal }}"), "g"), ".");

    return parseFloat(v);
}

function setNumeric(el, num){
    const str = isFinite(num) ? Number(num).toFixed(window.QTY_DIGITS) : "";
    if (el.inputmask) el.inputmask.setValue(str);
    else el.value = str;
}

/* ===============================
   FIND BASE PRICE
================================*/
function findBasePrice(varId, unitId, pg){
    let key = pg || "default";

    let selector =
        `input[name^='unit_prices[${varId}][${unitId}]['][name$='[${key}]']`;

    let el = document.querySelector(selector);

    return getNumeric(el);
}

/* ===============================
   DEBUG LOGGER (optional)
================================*/
window.DEBUG_QTY = false;
function dbg(...a){
    if (window.DEBUG_QTY){
        console.log("[QTY DEBUG]", ...a);
    }
}

/* ===============================
   RECALC DISCOUNT
================================*/
function recalc(row, roundNow=false){
    const fp = row.querySelector(".js-final-price");
    const dVal = row.querySelector(".js-discount-value");

    const pg     = row.querySelector(".js-pg").value || "default";
    const varId  = fp.dataset.var;
    const unitId = fp.dataset.unit;

    dbg("=== RECALC START ===");
    dbg("var =", varId, "unit =", unitId, "pg =", pg);

    // Base price
    let base = findBasePrice(varId, unitId, pg);
    if (!isFinite(base)) base = 0;
    dbg("Base =", base);

    // Final price
    let finalPrice = getNumeric(fp);
    if (!isFinite(finalPrice)){
        finalPrice = parseFloat(fp.dataset.lastValid || base);
    } else {
        fp.dataset.lastValid = finalPrice;
    }
    dbg("Final =", finalPrice);

    if (roundNow){
        setNumeric(fp, finalPrice);
    }

    // Discount
    let discount = base - finalPrice;
    if (!isFinite(discount) || discount < 0) discount = 0;

    dVal.value = discount.toFixed(window.QTY_DIGITS);

    dbg("Discount =", dVal.value);
    dbg("=== RECALC END ===");
}

/* ===============================
   INIT
================================*/
document.addEventListener("DOMContentLoaded", () => {

    // Activate InputMask
    if ($.fn.inputmask){
        $(".input_number").inputmask(window.__qtyMaskOpts);
    }

    // Initial calculation
    document.querySelectorAll("tbody[data-var][data-unit] tr")
        .forEach(row => recalc(row, true));
});

/* ===============================
   EVENTS
================================*/
document.addEventListener("input", e => {
    if (e.target.classList.contains("js-final-price")){
        recalc(e.target.closest("tr"), false);
    }
});

document.addEventListener("change", e => {
    if (e.target.classList.contains("js-pg") ||
        e.target.classList.contains("js-loc"))
    {
        const row = e.target.closest("tr");

        // Update data-pg attr
        row.querySelector(".js-final-price").dataset.pg =
            row.querySelector(".js-pg").value || "default";

        recalc(row, true);
    }
});

/* ===============================
   BEFORE SUBMIT
================================*/
document.addEventListener("submit", () => {
    document.querySelectorAll("tbody[data-var][data-unit] tr")
        .forEach(row => recalc(row, true));
}, true);
document.addEventListener("click", e => {
    if (!e.target.classList.contains("btn-add-tier")) return;

    const varId  = e.target.dataset.var;
    const unitId = e.target.dataset.unit;

    const tbody = e.target.closest(".qty-card")
        .querySelector(`tbody[data-var="${varId}"][data-unit="${unitId}"]`);

    // Ambil index dari data-next
    let nextIndex = parseInt(tbody.dataset.next || "0");

    let html = `
<tr>
    <td>
        <select class="form-control js-pg"
                name="qty_rules[${varId}][${unitId}][${nextIndex}][price_group_id]">
            <option value="">Default</option>
            @foreach($price_groups as $pgid => $pgname)
                <option value="{{ $pgid }}">{{ $pgname }}</option>
            @endforeach
        </select>
    </td>

    <td>
        <input type="number" min="1" class="form-control js-minqty"
               name="qty_rules[${varId}][${unitId}][${nextIndex}][min_qty]"
               value="1" required>
    </td>

    <td>
        <span class="js-base-label"></span>
        <div class="input-group">
            <span class="input-group-addon">IDR</span>
            <input type="text" inputmode="decimal"
                   class="form-control input_number js-final-price"
                   name="qty_rules[${varId}][${unitId}][${nextIndex}][final_price_inc_tax]"
                   data-var="${varId}" data-unit="${unitId}" data-pg="">
        </div>
        <input type="hidden" class="js-discount-type"
               name="qty_rules[${varId}][${unitId}][${nextIndex}][discount_type]"
               value="fixed">
        <input type="hidden" class="js-discount-value"
               name="qty_rules[${varId}][${unitId}][${nextIndex}][discount_value]"
               value="0">
    </td>

    <td>
        <select class="form-control js-loc" required
                name="qty_rules[${varId}][${unitId}][${nextIndex}][location_id]">
            <option disabled selected value="">Pilih Lokasi</option>
            @foreach($business_locations as $lid => $lname)
                <option value="{{ $lid }}">{{ $lname }}</option>
            @endforeach
        </select>
    </td>

    <td>
        <div style="display:flex; gap:6px;">
            <input type="date" class="form-control"
                   name="qty_rules[${varId}][${unitId}][${nextIndex}][valid_from]">
            <input type="date" class="form-control"
                   name="qty_rules[${varId}][${unitId}][${nextIndex}][valid_to]">
        </div>
    </td>

    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm"
                onclick="this.closest('tr').remove()">Hapus</button>
        <button type="button" class="btn btn-outline-secondary btn-sm duplicate-tier">×</button>
    </td>
</tr>`;

    tbody.insertAdjacentHTML("beforeend", html);

    // ACTIVATION INPUT MASK ONLY for last row
    if ($.fn.inputmask) {
        $(tbody).find("tr:last .input_number").inputmask(window.__qtyMaskOpts);
    }

    // increment counter
    tbody.dataset.next = nextIndex + 1;
});
</script>




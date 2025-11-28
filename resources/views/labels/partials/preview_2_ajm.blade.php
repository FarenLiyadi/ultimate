<style>
body {
    padding: 0;
    margin: 0;
    font-family: "Arial", Arial, Helvetica, sans-serif;
    font-weight: 500;
}

table {
    width: 100%;
    border-collapse: collapse;
}

/* Area label */
td.label {
    width: 50mm;
    height: 30mm;
    padding: 0;
    margin: 0;
    vertical-align: middle; /* penting */
    text-align: center;
    padding: 4px; /* jarak */
    box-sizing: border-box;
}

/* TABEL INTERNAL AGAR BENAR-BENAR TENGAH */
.inner-table {
    width: 100%;
    height: 100%;
    border-collapse: collapse;
}

.inner-table td {
    vertical-align: middle;
    text-align: center;
}

/* text style */
.product-name {
    font-size: {{ $print['name_size'] + 1 }}px;
    font-weight: 600;
    text-transform: uppercase;
    margin: 0;
    padding: 8;
}

.price-text {
    font-size: {{ $print['price_size'] + 1 }}px;
    font-weight: 600;
    margin: 0;
    padding: 1mm 0 0 0;
}

/* Barcode */
.barcode-img {
    width: 85%;
    height: 8mm;
    display: block;
    margin: 1mm auto 0 auto;
}

.sku-text {
    font-size: 12px;
    font-weight: bold;
    margin-top: 1mm;
}
</style>


<table align="center">
@foreach($page_products as $page_product)

<tr>
    <td class="label">
        <table class="inner-table">
            <tr>
                <td>

                    {{-- Name --}}
                    @if(!empty($print['name']))
                        <div class="product-name">
                            {{ strtoupper(Str::limit($page_product->product_actual_name, 55, '...')) }}
                        </div>
                    @endif

                    {{-- Price --}}
                    @if(!empty($print['price']))
                        <div class="price-text">
                            {{ session('currency')['symbol'] ?? '' }}

                            @if($print['price_type'] == 'inclusive')
                                {{ @num_format($page_product->sell_price_inc_tax) }}
                            @else
                                {{ @num_format($page_product->default_sell_price) }}
                            @endif
                        </div>
                    @endif

                    {{-- Barcode --}}
                    <img class="barcode-img"
                        src="data:image/png;base64,{{ DNS1D::getBarcodePNG($page_product->sub_sku, $page_product->barcode_type, 1,40) }}">

                    {{-- SKU --}}
                    <div class="sku-text">
                        {{ strtoupper(Str::limit($page_product->sub_sku, 24, '...')) }}
                    </div>

                </td>
            </tr>
        </table>
    </td>
</tr>

@endforeach
</table>


<style>
@media print {
    @page {
        size: 54mm 31mm;
        margin: 2mm 2mm;
    }
}
</style>

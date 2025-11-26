<style>
body {
    padding: 0;
    margin: 0;
    font-family: "Arial Black", Arial, Helvetica, sans-serif;
    font-weight: bold;
}

table {
    width: 71mm;
    border-collapse: collapse;
}

td.label {
    width: 33mm;
    height: 15mm;
    padding: 0;
    margin: 0;
    vertical-align: top;
}

td.gap {
    width: 2mm;
}

.label-box {
    width: 33mm;
    height: 15mm;
    padding: 1mm;
    padding-left: 3mm;
    padding-right: 2mm;
    overflow: hidden;
    font-size: 8px;
    line-height: 1.05;
}

.product-name {
    font-size: {{ $print['name_size'] + 1 }}px;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 0.5mm;
}

.price-text {
    font-size: {{ $print['price_size'] + 1 }}px;
    font-weight: bold;
    margin-top: 1mm;
}

/* Barcode + SKU wrapper */
.barcode-wrap {
    width: 100%;
    text-align: center; /* center seluruh isi */
    margin-top: 1mm;
}

.barcode-img {
    width: 80%;
    height: 5mm;
    display: block;
    margin: 0 auto; /* center WITHOUT left offset */
	margin-top: 2px;
}

.sku-text {
    text-align: center;
    font-size: 8px;
    margin-top: 1px;
    font-weight: bold;
}
</style>



<table align="center">
@foreach($page_products as $page_product)

    @if($loop->index % 2 == 0)
        <tr>
    @endif

    <td class="label">
        <div class="label-box">

            {{-- Nama Produk --}}
            @if(!empty($print['name']))
                <div class="product-name">
                    {{ strtoupper(Str::limit($page_product->product_actual_name, 18, '...')) }}
                </div>
            @endif

            {{-- Harga --}}
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
                   {{ strtoupper(Str::limit($page_product->sub_sku, 18, '...')) }}
            </div>

        </div>
    </td>

    @if($loop->index % 2 == 0)
        <td class="gap"></td>
    @endif

    @if($loop->iteration % 2 == 0)
        </tr>
    @endif

@endforeach
</table>


<style>
@media print {
    @page {
        size: {{ $paper_width }}in {{ $paper_height }}in;
        margin: {{ $margin_top }}in {{ $margin_left }}in;
    }
}
</style>

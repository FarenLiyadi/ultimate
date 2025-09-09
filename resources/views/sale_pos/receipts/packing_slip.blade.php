{{-- resources/views/sale_pos/partials/packing_slip.blade.php (versi thermal) --}}
<table style="width:100%; color:#000">
  <tbody>
    <tr>
      <td>
        <style>
          .rcpt{width:80mm;max-width:80mm;margin:0 auto;font-family:ui-monospace,Menlo,Consolas,"Courier New",monospace;font-size:11px;line-height:1.35;color:#000}
          .hd{text-align:center}
          .logo{max-height:60px;display:block;margin:4px auto 6px}
          .store-name{font-weight:700;font-size:14px;text-transform:uppercase}
          .muted{opacity:.95}
          .ttl{font-weight:700;margin-top:6px}
          .blk{margin:6px 0}
          .sep{border:0;border-top:1px dashed #000;margin:6px 0}
          .meta-line{display:flex;justify-content:space-between;gap:8px}
          .items{width:100%;border-collapse:collapse}
          .items thead th{border-bottom:1px solid #000;padding:3px 0;text-align:left;font-weight:700}
          .items td{vertical-align:top;padding:3px 0}
          .ta-r{text-align:right}
          .v-top{vertical-align:top}
          .item-name{font-weight:600}
          .subtxt{font-size:10px}
          .mod{font-size:10px;padding-left:8px}
          .barcode img{display:block;margin:4px auto;max-width:100%}
          @media print{
            @page{size:80mm auto;margin:4mm}
            body{margin:0}
            .rcpt{width:auto;max-width:none}
          }
        </style>

        <div class="rcpt">
          {{-- HEADER --}}
          <div class="hd">
            @if(!empty($receipt_details->logo))
              <img class="logo" src="{{$receipt_details->logo}}">
            @endif
            @if(!empty($receipt_details->display_name))
              <div class="store-name">{{$receipt_details->display_name}}</div>
            @endif
            @if(!empty($receipt_details->address))
              <div class="muted">{!! $receipt_details->address !!}</div>
            @endif
            @if(!empty($receipt_details->contact))
              <div class="muted">{!! $receipt_details->contact !!}</div>
            @endif
            @if(!empty($receipt_details->website))
              <div class="muted">{{ $receipt_details->website }}</div>
            @endif
            @if(!empty($receipt_details->tax_info1))
              <div class="muted">{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}</div>
            @endif
            @if(!empty($receipt_details->tax_info2))
              <div class="muted">{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}</div>
            @endif
            @if(!empty($receipt_details->location_custom_fields))
              <div class="muted">{{ $receipt_details->location_custom_fields }}</div>
            @endif
          </div>

          <hr class="sep">

          {{-- NOMOR & TANGGAL --}}
          <div class="meta-line subtxt">
            <div>
              @if(!empty($receipt_details->invoice_no_prefix))
                {!! $receipt_details->invoice_no_prefix !!}
              @endif
              {{ $receipt_details->invoice_no }}
            </div>
            @if(!empty($receipt_details->invoice_date))
              <div>{{ $receipt_details->date_label ?? __('messages.date') }}: {{ $receipt_details->invoice_date }}</div>
            @endif
          </div>

          <hr class="sep">

          {{-- CUSTOMER --}}
          <div class="blk">
            @if(!empty($receipt_details->customer_label))
              <div class="ttl">{{ $receipt_details->customer_label }}</div>
            @endif
            {{-- @if(!empty($receipt_details->customer_name))
              <div>{{ $receipt_details->customer_name }}</div>
            @endif --}}
            @if(!empty($receipt_details->customer_info))
              <div class="subtxt">{!! $receipt_details->customer_info !!}</div>
            @endif
            @if(!empty($receipt_details->client_id_label))
              <div class="subtxt"><strong>{{ $receipt_details->client_id_label }}</strong> {{ $receipt_details->client_id }}</div>
            @endif
            @if(!empty($receipt_details->customer_tax_label))
              <div class="subtxt"><strong>{{ $receipt_details->customer_tax_label }}</strong> {{ $receipt_details->customer_tax_number }}</div>
            @endif
            @if(!empty($receipt_details->customer_custom_fields))
              <div class="subtxt">{!! $receipt_details->customer_custom_fields !!}</div>
            @endif
            @if(!empty($receipt_details->sales_person_label))
              <div class="subtxt"><strong>{{ $receipt_details->sales_person_label }}</strong> {{ $receipt_details->sales_person }}</div>
            @endif
          </div>

          {{-- SHIPPING --}}
          <div class="blk">
            <div class="ttl">@lang('lang_v1.shipping_address')</div>
            <div class="subtxt">{!! $receipt_details->shipping_address !!}</div>

            @if(!empty($receipt_details->shipping_custom_field_1_label))
              <div class="subtxt"><strong>{!!$receipt_details->shipping_custom_field_1_label!!}:</strong> {!!$receipt_details->shipping_custom_field_1_value ?? ''!!}</div>
            @endif
            @if(!empty($receipt_details->shipping_custom_field_2_label))
              <div class="subtxt"><strong>{!!$receipt_details->shipping_custom_field_2_label!!}:</strong> {!!$receipt_details->shipping_custom_field_2_value ?? ''!!}</div>
            @endif
            @if(!empty($receipt_details->shipping_custom_field_3_label))
              <div class="subtxt"><strong>{!!$receipt_details->shipping_custom_field_3_label!!}:</strong> {!!$receipt_details->shipping_custom_field_3_value ?? ''!!}</div>
            @endif
            @if(!empty($receipt_details->shipping_custom_field_4_label))
              <div class="subtxt"><strong>{!!$receipt_details->shipping_custom_field_4_label!!}:</strong> {!!$receipt_details->shipping_custom_field_4_value ?? ''!!}</div>
            @endif
            @if(!empty($receipt_details->shipping_custom_field_5_label))
              {{-- perbaikan: gunakan label ke-5 yang benar --}}
              <div class="subtxt"><strong>{!!$receipt_details->shipping_custom_field_5_label!!}:</strong> {!!$receipt_details->shipping_custom_field_5_value ?? ''!!}</div>
            @endif
          </div>

          <hr class="sep">

          {{-- ITEMS --}}
          <table class="items">
            <thead>
              <tr>
                <th>{{$receipt_details->table_product_label}}</th>
                <th class="ta-r">{{$receipt_details->table_qty_label}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($receipt_details->lines as $line)
                <tr>
                  <td>
                    <div class="item-name">
                      @if(!empty($line['sub_sku'])) ({{$line['sub_sku']}}) @endif
                      {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}}
                    </div>

                    @if(!empty($line['brand']))<div class="subtxt">{{ $line['brand'] }}</div>@endif
                    @if(!empty($line['product_custom_fields']))<div class="subtxt">{{ $line['product_custom_fields'] }}</div>@endif
                    @if(!empty($line['sell_line_note']))<div class="subtxt">{!! $line['sell_line_note'] !!}</div>@endif

                    @if(!empty($line['lot_number']) || !empty($line['product_expiry']))
                      <div class="subtxt">
                        @if(!empty($line['lot_number']))
                          {{ $line['lot_number_label'] }}: {{ $line['lot_number'] }}
                        @endif
                        @if(!empty($line['product_expiry']))
                          @if(!empty($line['lot_number'])) · @endif
                          {{ $line['product_expiry_label'] }}: {{ $line['product_expiry'] }}
                        @endif
                      </div>
                    @endif

                    {{-- Rak/Baris/Pos (opsional) --}}
                    @if(!empty($line['rack']) || !empty($line['row']) || !empty($line['position']))
                      <div class="subtxt">
                        Lokasi barang :  {{ $line['rack'] ?? '-' }}
                        @if(!empty($line['row'])) · Baris: {{ $line['row'] }} @endif
                        @if(!empty($line['position'])) · Pos: {{ $line['position'] }} @endif
                      </div>
                    @endif

                    {{-- Modifiers (opsional) --}}
                    @if(!empty($line['modifiers']))
                      @foreach($line['modifiers'] as $modifier)
                        <div class="mod">
                          - {{$modifier['name']}} {{$modifier['variation']}}
                          @if(!empty($modifier['sub_sku'])) ({{$modifier['sub_sku']}}) @endif
                          @if(!empty($modifier['sell_line_note'])) — {!!$modifier['sell_line_note']!!} @endif
                        </div>
                      @endforeach
                    @endif
                  </td>
                  <td class="ta-r v-top">{{$line['quantity']}} {{$line['units']}}</td>
                </tr>
              @endforeach
                
              <tr style=" border-top: #000 solid 1px;">
                
                <th style="padding-top: 10px">Total Qty</th>
                <th class="ta-r">{{$receipt_details->total_quantity}}</th>
              </tr>
              <tr>
                <th>Grand Total</th>
                <th class="ta-r"><strong>{{$receipt_details->total}}</strong></th>
              </tr>
              <tr>
                <td></td>
                <td class="ta-r">
 Cetak  :
    {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->isoFormat('DD/MM/YYYY HH:mm') }}
  
</td>
              </tr>
             
            </tbody>

          </table>

          @if($receipt_details->show_barcode)
            <hr class="sep">
            <div class="barcode">
              <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 40, array(0,0,0), true) }}">
            </div>
          @endif
	 
        
			{{-- @if(!empty($receipt_details->total_items_label))
				<div class="flex-box">
					<p class="left text-left">
						{!! $receipt_details->total_items_label !!}
					</p>
					<p class="width-50 text-right">
						{{$receipt_details->total_items}}
					</p>
				</div>
			@endif --}}
          {{-- FOOTER --}}
          @if(!empty($receipt_details->footer_text))
            <hr class="sep">
            <div class="subtxt">{!! $receipt_details->footer_text !!}</div>
          @endif
        </div>

        {{-- Auto-print (opsional): hapus komentar untuk aktifkan --}}
        {{--
        <script>
          window.addEventListener('load', function(){ window.print(); });
        </script>
        --}}
      </td>
    </tr>
  </tbody>
</table>

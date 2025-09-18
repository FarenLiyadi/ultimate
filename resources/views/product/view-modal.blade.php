<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
	    </div>
	    <div class="modal-body">
      		<div class="row">
      			<div class="col-sm-9">
	      			<div class="col-sm-4 invoice-col">
	      				<b>@lang('product.sku'):</b>
						{{$product->sku }}<br>
						<b>@lang('product.brand'): </b>
						{{$product->brand->name ?? '--' }}<br>
						<b>@lang('product.unit'): </b>
						{{$product->unit->short_name ?? '--' }}<br>
						<b>@lang('product.barcode_type'): </b>
						{{$product->barcode_type ?? '--' }}
						@php 
    						$custom_labels = json_decode(session('business.custom_labels'), true);
						@endphp

                        @for($i = 1; $i <= 20; $i++)
                            @php
                                $db_field = 'product_custom_field' . $i;
                                $label = 'custom_field_' .$i;
                            @endphp

                            @if(!empty($product->$db_field))
                                <br/>
                                <b>{{ $custom_labels['product'][$label] ?? '' }}: </b>
                                {{$product->$db_field }}
                            @endif
                        @endfor
						
						<br>
						<strong>@lang('lang_v1.available_in_locations'):</strong>
						@if(count($product->product_locations) > 0)
							{{implode(', ', $product->product_locations->pluck('name')->toArray())}}
						@else
							@lang('lang_v1.none')
						@endif
						@if(!empty($product->media->first())) <br>
							<strong>@lang('lang_v1.product_brochure'):</strong>
							<a href="{{$product->media->first()->display_url}}" download="{{$product->media->first()->display_name}}">
								<span class="label label-info">
									<i class="fas fa-download"></i>
									{{$product->media->first()->display_name}}
								</span>
							</a>
						@endif
	      			</div>

	      			<div class="col-sm-4 invoice-col">
						<b>@lang('product.category'): </b>
						{{$product->category->name ?? '--' }}<br>
						<b>@lang('product.sub_category'): </b>
						{{$product->sub_category->name ?? '--' }}<br>	
						
						<b>@lang('product.manage_stock'): </b>
						@if($product->enable_stock)
							@lang('messages.yes')
						@else
							@lang('messages.no')
						@endif
						<br>
						@if($product->enable_stock)
							<b>@lang('product.alert_quantity'): </b>
							{{$product->alert_quantity ?? '--' }}
						@endif

						@if(!empty($product->warranty))
							<br>
							<b>@lang('lang_v1.warranty'): </b>
							{{$product->warranty->display_name }}
						@endif
	      			</div>
					
	      			<div class="col-sm-4 invoice-col">
	      				<b>@lang('product.expires_in'): </b>
	      				@php
	  						$expiry_array = ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ];
	  					@endphp
	      				@if(!empty($product->expiry_period) && !empty($product->expiry_period_type))
							{{$product->expiry_period}} {{$expiry_array[$product->expiry_period_type]}}
						@else
							{{$expiry_array['']}}
	      				@endif
	      				<br>
						@if($product->weight)
							<b>@lang('lang_v1.weight'): </b>
							{{$product->weight }}<br>
						@endif
						<b>@lang('product.applicable_tax'): </b>
						{{$product->product_tax->name ?? __('lang_v1.none') }}<br>
						@php
							$tax_type = ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')];
						@endphp
						<b>@lang('product.selling_price_tax_type'): </b>
						{{$tax_type[$product->tax_type]  }}<br>
						<b>@lang('product.product_type'): </b>
						@lang('lang_v1.' . $product->type)
						
	      			</div>
	      			<div class="clearfix"></div>
	      			<br>
      				<div class="col-sm-12">
      					{!! $product->product_description !!}
      				</div>
	      		</div>
      			<div class="col-sm-3 col-md-3 invoice-col">
      				<div class="thumbnail">
      					<img src="{{$product->image_url}}" alt="Product image">
      				</div>
      			</div>
      		</div>
      		@if($rack_details->count())
      		@if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
      			<div class="row">
      				<div class="col-md-12">
      					<h4>@lang('lang_v1.rack_details'):</h4>
      				</div>
      				<div class="col-md-12">
      					<div class="table-responsive">
      					<table class="table table-condensed bg-gray">
      						<tr class="bg-green">
      							<th>@lang('business.location')</th>
      							@if(session('business.enable_racks'))
      								<th>@lang('lang_v1.rack')</th>
      							@endif
      							@if(session('business.enable_row'))
      								<th>@lang('lang_v1.row')</th>
      							@endif
      							@if(session('business.enable_position'))
      								<th>@lang('lang_v1.position')</th>
      							@endif
      							</tr>
      						@foreach($rack_details as $rd)
      							<tr>
	      							<td>{{$rd->name}}</td>
	      							@if(session('business.enable_racks'))
	      								<td>{{$rd->rack}}</td>
	      							@endif
	      							@if(session('business.enable_row'))
	      								<td>{{$rd->row}}</td>
	      							@endif
	      							@if(session('business.enable_position'))
	      								<td>{{$rd->position}}</td>
	      							@endif
      							</tr>
      						@endforeach
      					</table>
      					</div>
      				</div>
      			</div>
      		@endif
      		@endif
      		@if($product->type == 'single')
      			@include('product.partials.single_product_details')
      		@elseif($product->type == 'variable')
      			@include('product.partials.variable_product_details')
      		@elseif($product->type == 'combo')
      			@include('product.partials.combo_product_details')
      		@endif
      		@if($product->enable_stock == 1)
	      		<div class="row">
	      			<div class="col-md-12">
	      				<strong>@lang('lang_v1.product_stock_details')</strong>
	      			</div>
	      			<div class="col-md-12" id="view_product_stock_details" data-product_id="{{$product->id}}">
	      			</div>
	      		</div>
      		@endif
			{{-- ===== Variation Unit Prices (Inc. Tax) ===== --}}
@php
  // susun map: [variation_id][unit_id][group_key] = price
  $existing_prices = [];
  foreach ($unitPrices ?? [] as $p) {
    $gkey = $p->price_group_id ?? 'default';
    $existing_prices[$p->variation_id][$p->unit_id][$gkey] = (float)$p->price_inc_tax;
  }
@endphp

@if(!empty($product->variations))
  <div class="row">
    <div class="col-md-12">
      <h4 class="m-t-10">Harga Jual Satuan (Inc. Tax)</h4>
    </div>

    @foreach($product->variations as $v)
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-condensed table-bordered">
            <thead>
              <tr class="bg-gray">
                <th style="width:180px;">@lang('product.unit')</th>
                <th style="width:160px;">Default</th>
                @foreach(($price_groups_dropdown ?? []) as $pgid => $pgname)
                  <th style="width:160px;">{{ $pgname }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach(($units_for_product ?? []) as $uid => $uname)
                @php
                  // ambil default; kalau kosong & ini base unit → fallback ke sell_price_inc_tax (Edit Produk)
                  $def = data_get($existing_prices, "{$v->id}.{$uid}.default");
                  if ($def === null && (int)$uid === (int)$product->unit_id) {
                      $def = (float)$v->sell_price_inc_tax;
                  }
                @endphp
                <tr>
                  <td><strong>{{ $uname }}</strong></td>
                  <td>
                    @if($def !== null)
                      <span class="display_currency" data-currency_symbol="true">{{ $def }}</span>
                    @else
                      —
                    @endif
                  </td>

                  @foreach(($price_groups_dropdown ?? []) as $pgid => $pgname)
                    @php
                      $g = data_get($existing_prices, "{$v->id}.{$uid}.{$pgid}");
                      // fallback: jika group kosong → biarkan kosong (supaya jelas belum di-set)
                    @endphp
                    <td>
                      @if($g !== null)
                        <span class="display_currency" data-currency_symbol="true">{{ $g }}</span>
                      @else
                        —
                      @endif
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  </div>
@endif
{{-- ===== Qty Pricing Rules (Final price dihitung) ===== --}}
@php
  // kelompokkan rules: [variation_id][unit_id] = list tier
  $rules_map = [];
  foreach (($qtyRules ?? []) as $r) {
    $rules_map[$r->variation_id][$r->unit_id][] = $r;
  }

  // helper ambil base price untuk (var, unit, price_group_id)
  $getBase = function($varId, $unitId, $pgId) use ($existing_prices, $product) {
      $pgkey = $pgId ?? 'default';
      $base  = data_get($existing_prices, "{$varId}.{$unitId}.{$pgkey}");
      if ($base === null) {
          // fallback: coba default unit
          $base = data_get($existing_prices, "{$varId}.{$unitId}.default");
      }
      if ($base === null && (int)$unitId === (int)$product->unit_id) {
          // fallback terakhir: harga jual default di Edit Produk (base unit saja)
          $varObj = $product->variations->firstWhere('id', $varId);
          $base = $varObj ? (float) $varObj->sell_price_inc_tax : null;
      }
      return $base !== null ? (float)$base : null;
  };

  // helper hitung final dari base + rule
  $calcFinal = function($base, $type, $value) {
      if ($base === null) return null;
      $type = strtolower($type ?: 'fixed');
      $val  = (float)($value ?? 0);
      if ($type === 'percent' || $type === 'percentage') {
          return max(0, $base * (1 - ($val/100)));
      }
      // fixed (nilai diskon)
      return max(0, $base - $val);
  };
@endphp

@if(!empty($rules_map))
  <div class="row">
    <div class="col-md-12">
      <h4 class="m-t-20">Harga qty</h4>
    </div>

    @foreach($product->variations as $v)
      @foreach(($units_for_product ?? []) as $uid => $uname)
        @php $tiers = $rules_map[$v->id][$uid] ?? []; @endphp
        @if(!empty($tiers))
          <div class="col-md-12">
            <h5 class="m-t-10">{{ $uname }}</h5>
            <div class="table-responsive">
              <table class="table table-condensed table-bordered">
                <thead>
                  <tr class="bg-gray">
                    <th style="width:160px;">Price Group</th>
                    <th style="width:110px;">Min Qty</th>
                    <th style="width:160px;">Final / Unit (Inc. Tax)</th>
                    <th style="width:180px;">Lokasi</th>
                    <th style="width:220px;">Periode</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tiers as $r)
                    @php
                      $pgName = $r->price_group_id ? ($price_groups_dropdown[$r->price_group_id] ?? ('#'.$r->price_group_id)) : 'Default';
                      $base   = $getBase($v->id, $uid, $r->price_group_id);
                      $final  = $calcFinal($base, $r->discount_type, $r->discount_value);

                      $locName = $r->location_id ? ($business_locations[$r->location_id] ?? ('#'.$r->location_id)) : __('lang_v1.all');
                      $period  = ($r->valid_from || $r->valid_to)
                                  ? trim(($r->valid_from ?? '…').' – '.($r->valid_to ?? '…'))
                                  : '—';
                    @endphp
                    <tr>
                      <td>{{ $pgName }}</td>
                      <td>{{ (int)$r->min_qty }}</td>
                      <td>
                        @if($final !== null)
                          <span class="display_currency" data-currency_symbol="true">{{ $final }}</span>
                          @if($base !== null && $final !== $base)
                            <small class="text-muted">
                              (base: <span class="display_currency" data-currency_symbol="true">{{ $base }}</span>)
                            </small>
                          @endif
                        @else
                          —
                        @endif
                      </td>
                      <td>{{ $locName }}</td>
                      <td>{{ $period }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif
      @endforeach
    @endforeach
  </div>
@endif



      	</div>
      	<div class="modal-footer">
      		<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" 
	        aria-label="Print" 
	          onclick="$(this).closest('div.modal').printThis();">
	        <i class="fa fa-print"></i> @lang( 'messages.print' )
	      </button>
	      	<button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	</div>
</div>

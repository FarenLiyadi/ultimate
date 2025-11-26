<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-condensed bg-gray">
				<thead>
					<tr class="bg-green">
						<th>SKU</th>
		                <th>@lang('business.product')</th>
		                <th>@lang('business.location')</th>
		                <th>@lang('sale.unit_price')</th>
		                <th>@lang('report.current_stock')</th>
		                <th>@lang('lang_v1.total_stock_price')</th>
		                <th>@lang('report.total_unit_sold')</th>
		                <th>@lang('lang_v1.total_unit_transfered')</th>
		                <th>@lang('lang_v1.total_unit_adjusted')</th>
		            </tr>
	            </thead>
	            <tbody>
	            	@foreach($product_stock_details as $product)
	            		<tr>
	            			<td>{{$product->sku}}</td>
	            			<td>
	            				@php
	            				$name = $product->product;
			                    if ($product->type == 'variable') {
			                        $name .= ' - ' . $product->product_variation . '-' . $product->variation_name;
			                    }
			                    @endphp
			                    {{$name}}
	            			</td>
	            			<td>{{$product->location_name}}</td>
	            			<td>
                        		<span class="display_currency"data-currency_symbol=true >{{$product->unit_price ?? 0}}</span>
                        	</td>
	            			<td>
    {{-- base stock --}}
    @php
        $stock = (float) ($product->stock ?? 0);
    @endphp

    <span data-is_quantity="true" class="display_currency" data-currency_symbol="false">
        {{ $stock }}
    </span> {{ $product->unit }}

    {{-- MULTI UNITS --}}
    @if(!empty($product->multi_units_branch))
        @php
            $rows = explode('|', $product->multi_units_branch);
            $converted_units = [];
        @endphp

        @foreach($rows as $row)
            @php
                // format: multiplier:UNIT:PRICE
                [$multiplier, $unit_name, $price] = explode(':', $row);

                $multiplier = (float) $multiplier;

                if($multiplier > 1){
                    $qty = floor($stock / $multiplier);
                    if($qty > 0){
                        $converted_units[] = $qty . ' ' . $unit_name;
                    }
                }
            @endphp
        @endforeach

        @if(!empty($converted_units))
            <br>
            <small class="text-muted">
                ({{ implode(', ', $converted_units) }})
            </small>
        @endif
    @endif
</td>

                        	<td>
                        		<span class="display_currency"data-currency_symbol=true >{{$product->unit_price * $product->stock}}</span>
                        	</td>
                        	<td>
                        		<span data-is_quantity="true" class="display_currency"data-currency_symbol=false >{{$product->total_sold ?? 0}}</span>{{$product->unit}}
                        	</td>
                        	<td>
                        		<span data-is_quantity="true" class="display_currency"data-currency_symbol=false >{{$product->total_transfered ?? 0}}</span>{{$product->unit}}
                        	</td>
                        	<td>
                        		<span data-is_quantity="true" class="display_currency"data-currency_symbol=false >{{$product->total_adjusted ?? 0}}</span>{{$product->unit}}
                        	</td>
	            		</tr>
	            	@endforeach
	            </tbody>
	     	</table>
     	</div>
    </div>
</div>
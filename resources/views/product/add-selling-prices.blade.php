@extends('layouts.app')
@section('title', __('lang_v1.add_selling_price_group_prices'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-black">Edit Harga Jual</h1>
    <h1 class="tw-text-xl md:tw-text-xl tw-font-bold tw-text-black"> {{$product->name}} ({{$product->sku}})</h1>
	
</section>

<!-- Main content -->
<section class="content">
	{{-- {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveSellingPrices']), 'method' => 'post', 'id' => 'selling_price_form' ]) !!}
	
	{!! Form::hidden('product_id', $product->id); !!}
	<div class="row">
		<div class="col-xs-12">
		<div class="box box-solid">
			<div class="box-header">
	            <h3 class="box-title">@lang('sale.product'): {{$product->name}} ({{$product->sku}})</h3>
	        </div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="table-responsive">
							<table class="table table-condensed table-bordered table-th-green text-center table-striped">
								<thead>
									<tr>
										@if($product->type == 'variable')
											<th>
												@lang('lang_v1.variation')
											</th>
										@endif
										<th>@lang('lang_v1.default_selling_price_inc_tax')</th>
										@foreach($price_groups as $price_group)
											<th>{{$price_group->name}} @show_tooltip(__('lang_v1.price_group_price_type_tooltip'))</th>
										@endforeach
									</tr>
								</thead>
								<tbody>
									@foreach($product->variations as $variation)
										<tr>
										@if($product->type == 'variable')
											<td>
												{{$variation->product_variation->name}} - {{$variation->name}} ({{$variation->sub_sku}})
											</td>
										@endif
										<td><span class="display_currency" data-currency_symbol="true">{{$variation->sell_price_inc_tax}}</span></td>
											@foreach($price_groups as $price_group)
												<td>
													{!! Form::text('group_prices[' . $price_group->id . '][' . $variation->id . '][price]', !empty($variation_prices[$variation->id][$price_group->id]['price']) ? @num_format($variation_prices[$variation->id][$price_group->id]['price']) : 0, ['class' => 'form-control input_number input-sm'] ); !!}
                                                    
                                                    @php
                                                        $price_type = !empty($variation_prices[$variation->id][$price_group->id]['price_type']) ? $variation_prices[$variation->id][$price_group->id]['price_type'] : 'fixed';

                                                        $name = 'group_prices[' . $price_group->id . '][' . $variation->id . '][price_type]';
                                                    @endphp

                                                    <select name={{$name}} class="form-control">
                                                        <option value="fixed" @if($price_type == 'fixed') selected @endif>@lang('lang_v1.fixed')</option>
                                                        <option value="percentage" @if($price_type == 'percentage') selected @endif>@lang('lang_v1.percentage')</option>
                                                    </select>
												</td>
											@endforeach
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			{!! Form::hidden('submit_type', 'save', ['id' => 'submit_type']); !!}
			<div class="text-center">
      			<div class="btn-group">
					<button id="opening_stock_button" @if($product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="tw-dw-btn tw-text-white tw-dw-btn-lg bg-purple submit_form">@lang('lang_v1.save_n_add_opening_stock')</button>
					<button type="submit" value="save_n_add_another" class="tw-dw-btn tw-text-white tw-dw-btn-lg bg-maroon submit_form">@lang('lang_v1.save_n_add_another')</button>
          			<button type="submit" value="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg submit_form">@lang('messages.save')</button>
          		</div>
          	</div>
		</div>
	</div>

	{!! Form::close() !!} --}}
	<form action="{{ route('products.pricing.save', $product->id) }}" method="POST">
  @csrf

<h5 class="tw-text-xl md:tw-text-2xl font-bold mb-3">
    Harga per Satuan (Per Cabang)
</h5>

<style>
    /* =============================================
       FIX SPACE KOSONG BAWAAN TEMPLATE UltimatePOS
       ============================================= */
    section.content > .clearfix {
        display: none !important;
        height: 0 !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* =============================================
       TAB CONTENT (hilangkan min-height bawaan bootstrap 3 theme)
       ============================================= */
    .tab-content > .tab-pane {
        min-height: 0 !important;
        padding-top: 10px !important;
    }

    /* =============================================
       TAB HEADER SCROLLABLE
       ============================================= */
    .horizontal-tabs-wrapper {
        overflow-x: auto;
        white-space: nowrap;
        border-bottom: 1px solid #ddd;
        margin-bottom: -1px; /* menyatukan border ke konten */
    }

    .nav-tabs .nav-link {
        white-space: nowrap;
        padding: 10px 16px;
        font-weight: 600;
        border-radius: 0 !important;
    }

    .nav-tabs .nav-link.active {
        background: #fff !important;
        border: 1px solid #ddd !important;
        border-bottom-color: transparent !important;
        color: #000 !important;
    }

    /* =============================================
       TAB CONTENT PANEL
       ============================================= */
    .tab-content {
        border: 1px solid #ddd;
        border-top: none;
        padding: 15px;
        border-radius: 0 0 8px 8px;
        background: #fff;
    }
</style>
<style>
/* 1 — Hapus jarak atas setelah tab */
.tab-content {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* 2 — Hilangkan margin div pembungkus di dalam section.content */
section.content > .row,
section.content > .col-xs-12,
section.content > .col-sm-12 {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* 3 — Border wrap tab harus langsung rapat tanpa gap */
.tab-pane {
    margin-top: 0 !important;
    padding-top: 5px !important;
}
</style>
<style>
/* FIX BOOTSTRAP TAB GAP (UltimatePOS uses BS3) */
.tab-content > .tab-pane {
    display: none !important;
}

.tab-content > .active {
    display: block !important;
}
</style>


<div class="horizontal-tabs-wrapper">
    <ul class="nav nav-tabs" style="display:flex; flex-wrap:nowrap; min-width:max-content;">
        @foreach($business_locations as $locId => $locName)
           <li class="{{ $loop->first ? 'active' : '' }}" style="flex:0 0 auto;">
    <a data-toggle="tab"
       href="#loc_{{ $locId }}">
       {{ $locName }}
    </a>
</li>
        @endforeach
    </ul>
</div>

<div class="tab-content">
    @foreach($business_locations as $locId => $locName)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
             id="loc_{{ $locId }}">

            @include('product.partials.variation_unit_prices', [
                'location_id'            => $locId,
                'unitPricesForLocation'  => $unitPricesGrouped[$locId] ?? collect(),
                'price_groups_dropdown'  => $price_groups_dropdown,
                'units_for_product'      => $units_for_product,
                'product'                => $product,
            ])

        </div>
    @endforeach
</div>


	<hr>
<div style="margin-bottom: 16px"></div>
  {{-- Section: Diskon Qty per Satuan --}}
  @include('product.partials.qty_pricing_rules', ['price_groups' => $price_groups_dropdown])      {{-- TANPA <form> di dalam file ini --}}

  <div class="mt-3 text-end">
    <button type="submit" class="btn btn-primary">Simpan Semua</button>
  </div>
</form>   
</section>
@stop
{{-- @section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			$('button.submit_form').click( function(e){
				e.preventDefault();
				$('input#submit_type').val($(this).attr('value'));

				if($("form#selling_price_form").valid()) {
		            $("form#selling_price_form").submit();
		        }
			});
		});
	</script>

@endsection --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    if (window.jQuery) {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('.nav-tabs li').removeClass('active');
            $(e.target).parent().addClass('active');
        });
    } else {
        console.warn("jQuery belum siap");
    }

});
</script>



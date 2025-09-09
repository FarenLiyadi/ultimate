@extends('layouts.guest')
@section('title', $title)
@section('content')
<style>
  @media print {
    .no-print { display: none !important; }
  }
</style>
<div class="container">
    <div class="spacer no-print"></div>
    <div class="row no-print">
        <div class="col-md-12 text-right mb-12" >
            @if(!empty($payment_link))
                <a href="{{$payment_link}}" class="btn btn-info no-print" style="margin-right: 20px;"><i class="fas fa-money-check-alt" title="@lang('lang_v1.pay')"></i> @lang('lang_v1.pay')
                </a>
            @endif
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print tw-dw-btn-sm" id="print_invoicee" 
                 aria-label="Print"><i class="fas fa-print"></i> @lang( 'messages.print' )
            </button>
            @php $psId = request('package_slip'); @endphp
            @auth
            @if($psId)
            <a href="#" class="print-invoice no-print"
                data-href="{{ url('sells/'.$psId.'/print') }}?package_slip=true">
                <i class="fas fa-file-alt"></i> Packing Slip
            </a>
            @endif
                <a href="{{action([\App\Http\Controllers\SellController::class, 'index'])}}" class="tw-dw-btn tw-dw-btn-success tw-text-white no-print tw-dw-btn-sm" ><i class="fas fa-backward"></i>
                </a>
            @endauth
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2 col-sm-12" style="border: 1px solid #ccc;">
            <div class="spacer no-print"></div>
            <div id="invoice_content">
                {!! $receipt['html_content'] !!}
            </div>
            {{-- kontainer khusus AJAX packing slip --}}
            <div id="receipt_section" style="display:none"></div>
            <div class="spacer no-print"></div>
        </div>
    </div>
    <div class="spacer no-print"></div>
</div>




@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '#print_invoicee', function(){
            $('#invoice_content').printThis();
        });
    });
    @if(!empty(request()->input('print_on_load')))
        $(window).on('load', function(){
            $('#invoice_content').printThis();
        });
    @endif
$(document).on('click', 'a.print-invoice', function (e) {
  e.preventDefault();
  var href = $(this).data('href');

  $.ajax({
    method: 'GET',
    url: href,
    success: function (res, status, xhr) {
      var ct = (xhr.getResponseHeader('Content-Type') || '').toLowerCase();
      var $invoice = $('#invoice_content');
      var $section = $('#receipt_section');

      // Isi konten (JSON atau HTML)
      if (ct.includes('application/json') && res && res.success == 1 && res.receipt && res.receipt.html_content) {
        $section.html(res.receipt.html_content).show();
        if (typeof __currency_convert_recursively === 'function') {
          __currency_convert_recursively($section);
        }
        var oldTitle = document.title;
        if (res.receipt.print_title) document.title = res.receipt.print_title;
        if (res.print_title) document.title = res.print_title;
      } else {
        $section.html(res).show();
      }

      // ⬇️ Sembunyikan invoice saat print
      $invoice.addClass('no-print');

      // Print hanya packing slip
      if (typeof __print_receipt === 'function') {
        __print_receipt('receipt_section');   // jika ini window.print()
      } else {
        $section.printThis();                 // fallback: print div saja
      }

      // Tampilkan lagi invoice & (opsional) sembunyikan kembali section
      setTimeout(function () {
        $invoice.removeClass('no-print');
        // $section.hide(); // opsional: kalau ingin disembunyikan lagi
        // document.title = oldTitle; // kalau kamu set judul di atas
      }, 1200);
    },
    error: function () {
      toastr.error('Gagal memuat Packing Slip');
    }
  });
});

    
</script>


@endsection
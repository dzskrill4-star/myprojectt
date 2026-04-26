@extends('Template::layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body">
            <h5>@lang('Payment via Stripe Storefront')</h5>
            <form action="{{ $data->url }}" method="{{ $data->method }}">
                <ul class="list-group text-center">
                    <li class="list-group-item d-flex justify-content-between">
                        @lang('You have to pay '):
                        <strong>{{ showAmount($deposit->final_amount, currencyFormat:false) }} {{ __($deposit->method_currency) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        @lang('You will get '):
                        <strong>{{ showAmount($deposit->amount) }}</strong>
                    </li>
                </ul>
                <script src="{{ $data->src }}" class="stripe-button" @foreach ($data->val as $key => $value)
            data-{{ $key }}="{{ $value }}" @endforeach></script>
            </form>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        (function($) {
            "use strict";
            $('button[type="submit"]').removeClass().addClass("btn btn--base w-100 mt-3").text("Pay Now");
        })(jQuery);
    </script>
@endpush

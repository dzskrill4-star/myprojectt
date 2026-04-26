@extends('Template::layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-body">
                        <h5>@lang('Payment via NMI')</h5>
                        <form role="form" class="disableSubmission appPayment" id="payment-form" method="{{ $data->method }}" action="{{ $data->url }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-12">
                                    <div class="card-wrapper"></div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">@lang('Name on Card')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="name" type="text" value="{{ old('name') }}" required autocomplete="off" autofocus />
                                        <span class="input-group-text"><i class="fa fa-font"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">@lang('Card Number')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="billing-cc-number" type="tel" value="{{ old('billing-cc-number') }}" autocomplete="off" required autofocus />
                                        <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">@lang('Expiration Date')</label>
                                    <input class="form-control form--control" name="billing-cc-exp" type="tel" value="{{ old('billing-cc-exp') }}" placeholder="e.g. MM/YY" autocomplete="off" required />
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">@lang('CVC Code')</label>
                                    <input class="form-control form--control" name="billing-cc-cvv" type="tel" value="{{ old('billing-cc-cvv') }}" autocomplete="off" required />
                                </div>
                            </div>
                            <button class="btn btn--base w-100 mt-4" type="submit"> @lang('Submit')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/card.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="billing-cc-number"]',
                    expiryInput: 'input[name="billing-cc-exp"]',
                    cvcInput: 'input[name="billing-cc-cvv"]',
                    nameInput: 'input[name="name"]'
                }
            });

            @if ($deposit->from_api)
                $('.appPayment').on('submit', function() {
                    $(this).find('[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                });
            @endif
        })(jQuery);
    </script>
@endpush

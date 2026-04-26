@extends('Template::layouts.master')
@section('content')
    <form action="{{ route('user.payment.submit', $order->id) }}" method="post" class="deposit-form">
        @csrf
        <input type="hidden" name="currency">
        <div class="row justify-content-center gy-sm-4 gy-3">
            <div class="col-lg-7">
                <div class="card custom--card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Select Payment Option')</h5>
                        <div class="payment-system-list is-scrollable gateway-option-list">
                            @php
                                $sortedGateways = $gatewayCurrency->sortByDesc(function ($gatewayItem) {
                                    $name = strtolower(str_replace(' ', '', $gatewayItem->name));
                                    return $name === 'usdt' ? 2 : ($name === 'baridimob' ? 1 : 0);
                                });
                            @endphp
                            @foreach ($sortedGateways as $data)
                                @php
                                    $isUSDT = strtolower($data->name) === 'usdt';
                                    $isBaridi = strtolower(str_replace(' ', '', $data->name)) === 'baridimob';
                                    $userBaridiEnabled = auth()->check() && auth()->user()->baridi == 1;
                                    $show = $userBaridiEnabled ? ($isUSDT || $isBaridi) : $isUSDT;
                                @endphp
                                @if ($show)
                                <label for="{{ titleToKey($data->name) }}" class="payment-item gateway-option">
                                    <div class="payment-item__info">
                                        <span class="payment-item__check"></span>
                                        <span class="payment-item__name">{{ $isBaridi ? 'Baridi Mob' : __($data->name) }}</span>
                                    </div>
                                    <div class="payment-item__thumb">
                                        @php
                                            $methodImage = $isUSDT ? 'usdt.png' : ($isBaridi ? 'baridimob.png' : ($data->method->image ?? ''));
                                        @endphp
                                        <img class="payment-item__thumb-img"
                                            src="{{ $isUSDT || $isBaridi ? asset('assets/images/gateway/' . $methodImage) : getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                            style="width:70px; filter:none; opacity:1;"
                                            alt="payment-thumb">
                                    </div>
                                    <input class="payment-item__radio gateway-input"
                                           id="{{ titleToKey($data->name) }}"
                                           hidden
                                           data-gateway='@php echo json_encode($data) @endphp'
                                           data-is-baridi="{{ $isBaridi ? 1 : 0 }}"
                                           type="radio"
                                           name="gateway"
                                           value="{{ $data->method_code }}"
                                           @if (old('gateway')) @checked(old('gateway') == $data->method_code) @else @checked($loop->first) @endif
                                           data-min-amount="{{ showAmount($data->min_amount) }}"
                                           data-max-amount="{{ showAmount($data->max_amount) }}">
                                </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card custom--card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Your Order')</h5>

                        <ul class="list-group list-group-flush rounded mb-4 gap-2 p-3 order-item-list">
                            <li class="d-flex justify-content-between flex-column">
                                <small class="text--dark">@lang('Plan Title')</small>
                                <span class="fw-bold">{{ __($order->plan_details?->title) }}</span>
                            </li>
                            <li class="d-flex justify-content-between flex-column">
                                <small class="text--dark">@lang('Plan Price')</small>
                                <span class="fw-bold">{{ showAmount($order->amount) }}</span>
                            </li>
                        </ul>

                        <h6 class="fw-bold mb-1">@lang('Order Summary')</h6>
                        <ul>
                            <li class="py-1 d-flex justify-content-between">
                                <span>@lang('Subtotal')</span>
                                <span class="fw-semibold">{{ getAmount($order->amount) }} {{ __(gs('cur_text')) }}</span>
                            </li>

                            <li class="py-1 d-flex justify-content-between">
                                <span>
                                    @lang('Processing Charge')
                                    <span title="@lang('Processing charge for payment gateways')" class="processing-fee-info"><i class="las la-info-circle"></i>
                                    </span>
                                </span>

                                <span class="fw-semibold">
                                    <span class="processing-fee">@lang('0.00')</span> {{ __(gs('cur_text')) }}
                                </span>
                            </li>

                            <li class="py-1 d-flex justify-content-between">
                                <span class="fw-bold">@lang('Total')</span>
                                <span class="fw-bold"><span class="final-amount">@lang('0.00')</span>
                                    {{ __(gs('cur_text')) }}</span>
                            </li>

                            <li class="py-1 d-flex justify-content-between gateway-conversion d-none">
                                <span class="fw-bold">@lang('Rate')</span>
                                <span class="fw-bold">
                                    <span class="conversion-rate">@lang('0.00')</span>
                                </span>
                            </li>

                            <li class="py-1 d-flex justify-content-between conversion-currency d-none total-amount">
                                <span class="fw-bold">@lang('Payable Amount')</span>
                                <span class="fw-bold">
                                    <span class="payable-amount">@lang('0.00')</span>
                                </span>
                            </li>
                        </ul>

                        <div class="d-none crypto-message text-muted mb-3 fst-italic mt-3">
                            <i class="la la-info-circle"></i> @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                        </div>

                        <button type="submit" class="btn btn--base w-100 mt-3" disabled>
                            @lang('Confirm Payment')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('style')
    <style>
        .order-item-list {
            background: linear-gradient(140deg, #ffffff29, #ffffff2b, #afacac6e);
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let amount = parseFloat({{ $order->amount }});
            let gateway, minAmount, maxAmount;

            $('.amount').focus();

            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', () => gatewayChange());

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                // Override rate for Baridi Mob (DZD) to fixed 250
                if (gatewayElement.data('is-baridi') == 1) {
                    gateway.rate = 250;
                    gateway.currency = 'DZD';
                }

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                $(".processing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                calculation();
            }

            gatewayChange();

            function calculation() {
                if (!gateway) return;

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.conversion-rate').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.conversion-currency .payable-amount').text((parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
                        8 : 2)) + ` ${gateway.currency}`)
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }

            let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

            let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush

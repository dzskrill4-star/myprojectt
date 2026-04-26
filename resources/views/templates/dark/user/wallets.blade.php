@extends('Template::layouts.master')

@section('content')
    @if ($userCoinBalances->count())
        <div class="wallet-card-wrapper">
            @foreach ($userCoinBalances as $item)
                <div class="wallet-card">
                    <div class="wallet-card-body">
                        <div class="top">
                            <div class="flex-shrink-0">
                                <img alt="Image" class="logo" src="{{ getImage(getFilePath('miner') . '/' . $item->miner->coin_image, getFileSize('miner')) }}">
                            </div>

                            <div class="top-content">
                                <div class="top-content-heading">
                                    <h5 class="title">{{ strtoupper($item->miner->currency_code) }} @lang('Wallet')</h5>
                                </div>
                                <p>
                                    <small class="fw-bold">{{ showAmount($item->balance, 8, exceptZeros: true, currencyFormat: false) }}
                                        {{ strtoupper($item->miner->currency_code) }}
                                    </small>
                                </p>
                                <button class="btn btn--base btn--sm moveToSiteCurrency" data-id="{{ $item->id }}" data-currency_code="{{ strtoupper($item->miner->currency_code) }}" data-crypto_rate = "{{ getAmount($item->miner->rate, 8) }}" data-crypto_balance = "{{ getAmount($item->balance, 8) }}">@lang('Transfer') <i class="las la-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="80" height="80" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                <g>
                    <path d="M53 57H7c-2.8 0-5-2.2-5-5V14c0-2.8 2.2-5 5-5h38c2.8 0 5 2.2 5 5v2h-2v-2c0-1.7-1.3-3-3-3H7c-1.7 0-3 1.3-3 3v38c0 1.7 1.3 3 3 3h46c1.7 0 3-1.3 3-3V42h2v10c0 2.8-2.2 5-5 5z" fill="#e1e1e1" opacity="1" data-original="#000000" class=""></path>
                    <path d="M58 30h-2V20c0-1.7-1.3-3-3-3H12v-2h41c2.8 0 5 2.2 5 5z" fill="#e1e1e1" opacity="1" data-original="#000000" class=""></path>
                    <path d="M57 43H43c-1.7 0-3-1.3-3-3v-8c0-1.7 1.3-3 3-3h14c2.8 0 5 2.2 5 5v4c0 2.8-2.2 5-5 5zM43 31c-.6 0-1 .4-1 1v8c0 .6.4 1 1 1h14c1.7 0 3-1.3 3-3v-4c0-1.7-1.3-3-3-3z" fill="#e1e1e1" opacity="1" data-original="#000000" class=""></path>
                    <path d="M47 39c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-4c-.6 0-1 .4-1 1s.4 1 1 1 1-.4 1-1-.4-1-1-1zM22 50c-7.7 0-14-6.3-14-14s6.3-14 14-14 14 6.3 14 14-6.3 14-14 14zm0-26c-6.6 0-12 5.4-12 12s5.4 12 12 12 12-5.4 12-12-5.4-12-12-12z" fill="#e1e1e1" opacity="1" data-original="#000000" class=""></path>
                    <path d="m12.096 44.483 18.382-18.382 1.414 1.414L13.51 45.897z" fill="#e1e1e1" opacity="1" data-original="#000000" class=""></path>
                </g>
            </svg>

            <p class="mt-2 mb-0">@lang('You have no coin wallet yet'). <a class="text--base" href="{{ route('plans') }}">@lang('Purchase a plan')</a></p>
        </div>
    @endif

    <div class="modal fade" id="walletMoveModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header mb-0">
                    <h5 class="modal-title">@lang('Transfer from') <span class="coinCode"></span> @lang('Wallet') @lang('to') @lang('Earning Wallet')</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label class="form-label">@lang('Amount')</label>
                                <button type="button" class="maxLimit text--base">@lang('Max')</button>
                            </div>

                            <div class="input-group">
                                <input class="form-control form--control" id="amount" name="amount" required type="number" step="any" value="{{ old('amount') }}" placeholder="@lang('Enter amount')">
                                <span class="input-group-text coinCode"></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span>@lang('Rate') </span>
                                <div>1 <small class="coinCode"></small> = <small class="text--base cryptoRate">00</small> {{ gs('cur_text') }}</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span>@lang('You will get') </span>
                                <div>
                                    <span class="convertedAmount">0000</span> <span class="">{{ __(gs('cur_text')) }}</span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--base w-100" type="submit">@lang('Move') <i class="las la-arrow-right"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            $('.moveToSiteCurrency').each(function() {
                let walletBalance = parseFloat($(this).data('crypto_balance')) || 0;
                if (walletBalance > 0) {
                    $(this).prop('disabled', false);
                } else {
                    $(this).prop('disabled', true);
                }
            });

            let cryptoRate = 0;

            $('.moveToSiteCurrency').on('click', function() {
                let modal = $('#walletMoveModal');
                modal.find('[name="amount"]').val('');
                let data = $(this).data();
                let walletBalance = parseFloat($(this).data('crypto_balance')) || 0;
                let coinCode = data.currency_code;
                let cryptoRate = data.crypto_rate;

                modal.find('.coinCode').text(coinCode);
                modal.find('.cryptoRate').text(cryptoRate.toFixed(6));

                modal.find('.maxLimit').on('click', function() {
                    modal.find('[name="amount"]').val(walletBalance.toFixed(6));
                    modal.find('.convertedAmount').text((cryptoRate * walletBalance).toFixed(6));
                    modal.find('[name="amount"]').trigger('input');
                });

                modal.find('[name="amount"]').on('input', function() {
                    let amount = parseFloat($(this).val()) || 0;
                    let convertedAmount = (amount * cryptoRate).toFixed(6);
                    modal.find('.convertedAmount').text(convertedAmount);
                });
                modal.find('form').attr('action', `{{ route('user.wallet.transfer', '') }}/${data.id}`);
                modal.modal('show');
            });

            $('#walletMoveModal').on('hidden.bs.modal', function() {
                $(this).find('form').attr('action', '');
                $(this).find('[name="amount"]').val('');
                $(this).find('.convertedAmount').text('0000');
            });

        })(jQuery)
    </script>
@endpush

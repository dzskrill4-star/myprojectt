@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form method="GET">
                        <div class="d-flex flex-wrap gap-4">
                            @if(request()->search)
                                <input type="hidden" name="search" value="{{request()->search}}">
                            @endif
                            <div class="flex-grow-1">
                                <label>@lang('Wallet Type')</label>
                                <select name="wallet_type" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="{{ Status::DEPOSIT_WALLET }}" @selected(request()->wallet_type == Status::DEPOSIT_WALLET)>@lang('Deposit Wallet')</option>
                                    <option value="{{ Status::PROFIT_WALLET }}" @selected(request()->wallet_type == Status::PROFIT_WALLET)>@lang('Earning Wallet')</option>
                                    <option value="{{ Status::CRYPTO_WALLET }}" @selected(request()->wallet_type == Status::CRYPTO_WALLET)>@lang('Coin Wallet')</option>
                                </select>
                            </div>


                            <div class="flex-grow-1">
                                <label>@lang('Type')</label>
                                <select name="trx_type" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                    <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                                </select>
                            </div>

                            <div class="flex-grow-1">
                                <label>@lang('Currency')</label>
                                <select name="currency" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->currency }}" @selected(request()->currency == $currency->currency)>{{strtoupper($currency->currency) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Remark')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="remark">
                                    <option value="">@lang('All')</option>
                                    @foreach ($remarks->whereNotNull('remark') as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>

                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('TRX No.')</th>
                                    <th>@lang('Transacted At')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $transaction->user->fullname }}</span>
                                            <br>
                                            <span class="small"> <a href="{{ route('admin.users.detail', $transaction->user_id) }}"><span>@</span>{{ $transaction->user->username }}</a> </span>
                                        </td>

                                        <td>
                                            <strong>{{ $transaction->trx }}</strong>
                                        </td>

                                        <td>
                                            {{ showDateTime($transaction->created_at) }}<br>{{ diffForHumans($transaction->created_at) }}
                                        </td>

                                        <td class="budget">
                                            <span class="fw-bold @if ($transaction->trx_type == '+') text--success @else text--danger @endif">
                                                {{ showAmount($transaction->post_balance, 8, exceptZeros: true, currencyFormat: false) }}
                                                {{ __(strtoupper($transaction->currency)) }}
                                            </span>
                                        </td>

                                        <td class="budget">
                                            {{ showAmount($transaction->post_balance, 8, exceptZeros: true, currencyFormat: false) }}
                                            {{ __(strtoupper($transaction->currency)) }}
                                        </td>

                                        <td>{{ __($transaction->details) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="TRX No."/>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush

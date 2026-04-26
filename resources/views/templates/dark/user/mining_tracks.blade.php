@extends('Template::layouts.master')

@section('content')
    <div class="table--responsive--lg table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>@lang('Title')</th>
                    <th>@lang('Return /Day')</th>
                    <th>@lang('Total Days')</th>
                    <th>@lang('Remaining Days')</th>
                    <th>@lang('Total Received')</th>
                    <th> @lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            {{ __($order->plan_details->title) }} - {{ $order->plan_details->speed }}
                        </td>

                        <td>
                            @if ($order->min_return_per_day == $order->max_return_per_day)
                                {{ showAmount($order->min_return_per_day, 8, exceptZeros: true, currencyFormat: false) }}
                            @else
                                {{ showAmount($order->min_return_per_day, 8, exceptZeros: true, currencyFormat: false) . ' - ' . showAmount($order->max_return_per_day, 8, exceptZeros: true, currencyFormat: false) }}
                            @endif
                            {{ strtoupper($order->currency_code) }}
                        </td>

                        <td>{{ $order->period }}</td>

                        <td>
                            {{ $order->period_remain }}
                        </td>

                        <td>
                            {{showAmount($order->total_earned_amount, 8, exceptZeros: true, currencyFormat: false)}}  {{ strtoupper($order->currency_code) }}
                        </td>

                        <td>
                            <button class="btn btn--base btn--xsm viewBtn" data-date="{{ __(showDateTime($order->created_at, 'd M, Y')) }}" data-trx="{{ $order->trx }}" data-plan="{{ $order->plan_details->title }}" data-miner="{{ $order->plan_details->miner }}" data-speed="{{ $order->plan_details->speed }}" data-rpd="@if ($order->min_return_per_day == $order->max_return_per_day) {{ showAmount($order->min_return_per_day, 8, exceptZeros: true, currencyFormat: false) }} @else {{ showAmount($order->min_return_per_day, 8, exceptZeros: true, currencyFormat: false) . ' - ' . showAmount($order->max_return_per_day, 8, exceptZeros: true, currencyFormat: false) }} @endif {{ strtoupper($order->currency_code) }}" data-period={{ $order->period }} data-period_r={{ $order->period_remain }} data-status="{{ $order->status }}" data-maintenance_cost="{{ getAmount($order->maintenance_cost) }}"  @if ($order->status == 0) data-order_id="{{ encrypt($order->id) }}" @endif><i class="las la-desktop"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($orders->hasPages())
            <div class="mt-4">
                {{ paginateLinks($orders) }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">@lang('Track Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Plan Title')</span>
                            <span class="plan-title"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Hashrate')</span>
                            <span class="speed"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Currency')</span>
                            <span class="miner-name"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Return /Day')</span>
                            <span class="plan-rpd"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Maintenance Cost')</span>
                            <span class="maintenance-cost" title="@lang('On Return Amount')"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Total Days')</span>
                            <span class="plan-period"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Remaining Days')</span>
                            <span class="plan-period-r"></span>
                        </li>

                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span class="font-weight-bold">@lang('Created At')</span>
                            <span class="p-date"></span>
                        </li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <a class="btn btn--base w-100" href="">@lang('Pay Now')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <a href="{{ route('plans') }}" class="btn btn--xsm btn--base">@lang('Mining Plans')</a>
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {
            $('.viewBtn').on('click', function() {
                var modal = $('#viewModal');

                let data = $(this).data();

                modal.find('.p-date').text(data.date);
                modal.find('.plan-title').text(data.plan);
                modal.find('.plan-price').text(data.price);
                modal.find('.miner-name').text(data.miner);
                modal.find('.speed').text(data.speed);
                modal.find('.plan-rpd').text(data.rpd);
                modal.find('.plan-period').text(data.period);
                modal.find('.plan-period-r').text(data.period_r);
                modal.find('.maintenance-cost').text(data.maintenance_cost + '%');

                if (data.status == 0) {
                    modal.find('.modal-footer').show();
                    modal.find('.modal-footer a').attr('href',
                        `{{ route('user.payment', '') }}/${data.order_id}`);
                } else {
                    modal.find('.modal-footer').hide();
                }
                modal.modal('show');
            })
        })(jQuery)
    </script>
@endpush

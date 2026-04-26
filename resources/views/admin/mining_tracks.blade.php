@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two custom-data-table table">
                            <thead>
                                <tr>
                                    <th>@lang('Order No.')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Return /Day')</th>
                                    <th>@lang('Total Days')</th>
                                    <th>@lang('Remaining Days')</th>
                                    <th>@lang('Total Returned')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->trx }} </td>
                                        <td>{{ showDateTime($order->created_at) }} </td>
                                        <td><a href="{{route('admin.users.detail', $order->user_id)}}">{{ $order->user->username }}</a></td>
                                        <td>{{ __($order->plan_details->title) }} </td>
                                        <td>{{ strtoupper($order->currency_code) }} </td>
                                        <td>
                                            {{ showAmount($order->min_return_per_day, 8, exceptZeros: true, currencyFormat: false) }}
                                            -
                                            {{ showAmount($order->max_return_per_day, 8, exceptZeros: true, currencyFormat: false) }}
                                            <strong>{{ strtoupper($order->currency_code) }}</strong>
                                        </td>
                                        <td>{{ $order->period }}</td>
                                        <td>{{ $order->period_remain }}</td>
                                        <td>
                                            {{ showAmount($order->total_return_amount, 8, exceptZeros: true, currencyFormat: false) }} {{ strtoupper($order->currency_code) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>


@endsection

@push('breadcrumb-plugins')
    <x-search-form minerFilter="yes" placeholder="Order No." />
@endpush

@push('style')
    <style>
        .select2-container {
            min-width: 180px;
        }
    </style>
@endpush

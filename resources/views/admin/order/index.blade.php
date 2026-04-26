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
                                    <th>@lang('Time')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan Title')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Payment Via')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>

                            <tbody class="list">
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->trx }} </td>
                                        <td>{{ showDateTime($order->created_at, 'Y-m-d h:i A') }}</td>
                                        <td><a href="{{route('admin.users.detail', $order->user_id)}}">{{ $order->user->username }}</a></td>
                                        <td>{{ __($order->plan_details->title) }}</td>
                                        <td>{{ __($order->plan_details->miner) }}</td>
                                        <td>{{ showAmount($order->amount) }}</td>
                                        <td>{{ $order?->deposit ? $order?->deposit->methodName() : 'Wallet Balance' }}</td>
                                        <td>@php echo $order->statusBadge; @endphp</td>
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


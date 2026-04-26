@extends('Template::layouts.master')
@section('content')

        <div class="col-lg-12">
            <div class="table--responsive--lg table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Trx. No.')</th>
                            <th>@lang('Transacted')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Post Balance')</th>
                            <th>@lang('Detail')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>
                                    <small><strong>#{{ $transaction->trx }}</strong></small>
                                </td>

                                <td>
                                    <small title="{{ showDateTime($transaction->created_at) }}">{{ diffForHumans($transaction->created_at) }}</small>
                                </td>

                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        <small class="fw-bold @if ($transaction->trx_type == '+') text--success @else text--danger @endif">
                                            {{ $transaction->trx_type }} {{ showAmount($transaction->amount, 16, exceptZeros: true, currencyFormat: false) }}
                                            {{ __(strtoupper($transaction->currency)) }}
                                        </small>

                                        @if ($transaction->badgeReward)
                                            <img src="{{ getImage(getFilePath('badge') . '/' . $transaction->badgeReward?->badge->image, getFileSize('badge')) }}" class="badge-logo" data-amount="{{ $transaction->badgeReward->amount }}" data-currency="{{ $transaction->badgeReward->currency }}" data-remark="{{ keyToTitle($transaction->badgeReward->remark) }}" data-badge="{{ $transaction->badgeReward->badge->name }}" alt="@lang('image')">
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <small>
                                        {{ showAmount($transaction->post_balance, 8, exceptZeros: true, currencyFormat: false) }}
                                        {{ __(strtoupper($transaction->currency)) }}
                                    </small>
                                </td>

                                <td>
                                    @php
                                        $details = $transaction->details;
                                        if (is_string($details) && preg_match('/commission\s+from/i', $details)) {
                                            $details = preg_replace('/commission\s+from/i', __('commission from'), $details, 1);
                                        } else {
                                            $details = __($details);
                                        }
                                    @endphp
                                    <small>{{ strLimit($details, 40) }}</small>
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
            <div class="mt-3">
                {{ paginateLinks($transactions) }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="rewardModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Badge Reward')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span>@lang('Amount')</span>
                            <span class="reward-amount"></span>
                        </li>
                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span>@lang('Remark')</span>
                            <span class="reward-remark"></span>
                        </li>
                        <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                            <span>@lang('Badge')</span>
                            <span class="reward-badge"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <x-search-form btn="input-group-text" placeholder="Trx. No." />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.filter_field').on('change', () => {
                window.location.href = $('#filterForm').attr('action') + '?' + $(this).serialize();
                $('#filterForm').submit();
            });

            $('.badge-logo').on('click', function() {
                const modal = $('#rewardModal');
                modal.find('.reward-amount').text($(this).data('amount') + ' ' + $(this).data('currency'));
                modal.find('.reward-remark').text($(this).data('remark'));
                modal.find('.reward-badge').text($(this).data('badge'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .filter--form .form-control {
            padding-block: .375rem;
        }

        .badge-logo {
            width: 20px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid #575757;
        }
    </style>
@endpush

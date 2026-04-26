@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="table--responsive--lg table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Trx. No.')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Charge')</th>
                            <th>@lang('Payable')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($deposits as $deposit)
                            <tr>
                                <td><small class="fw-semibold">#{{ $deposit->trx }}</small></td>
                                <td>{{ showAmount($deposit->amount, exceptZeros: true) }}</td>
                                <td>{{ showAmount($deposit->charge, exceptZeros: true) }}</td>
                                <td>{{ showAmount($deposit->amount + $deposit->charge, exceptZeros: true) }}</td>
                                <td>@php echo $deposit->statusBadge @endphp</td>

                                @php
                                    $depositData = [
                                        'transaction_number' => "#{$deposit->trx}",
                                        'initiated_at' => showDateTime($deposit->created_at, 'M d, Y h:i A'),
                                        'gateway_name' => __($deposit->gateway?->name?? ''),
                                        'amount' => showAmount($deposit->amount, exceptZeros: true),
                                        'charge' => showAmount($deposit->charge, exceptZeros: true),
                                        'payable_amount' => showAmount($deposit->amount + $deposit->charge, exceptZeros: true),
                                        'conversion_rate' => $deposit->rate != 1 ? '1 ' . gs('cur_text') . ' = ' . showAmount($deposit->rate, currencyFormat: false) . ' ' . $deposit->currency : null,
                                        'final_amount' => $deposit->rate != 1 ? showAmount($deposit->final_amount, currencyFormat: false, exceptZeros: true) . ' ' . __($deposit->method_currency) : null,
                                    ];

                                    $details = $deposit->detail != null ? json_encode($deposit->detail) : null;

                                @endphp

                                <td>
                                    <button @if ($deposit->method_code >= 1000) data-info="{{ $details }}" @endif @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif class="btn btn btn--base btn--xsm detailBtn" data-deposit_data='{{ json_encode($depositData) }}' type="button">
                                        <i class="las la-desktop"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ paginateLinks($deposits) }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-2">
                    <ul class="list-group userData">
                    </ul>
                    <div class="feedback d-none"></div>
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

            const listComponent = (label, value, valueClass = null) => {
                return `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text--dark">${label}</span>
                            <span class="${valueClass ?? ''}">${value}</span>
                        </li>`;
            }

            $('.detailBtn').on('click', function() {
                let modal = $('#detailModal');
                let userData = $(this).data('info');
                let html = '';
                let depositData = $(this).data('deposit_data');

                html += listComponent(`@lang('Transaction Number')`, depositData.transaction_number, 'fw-semibold text--sm');
                html += listComponent(`@lang('Initiated At')`, depositData.initiated_at, 'fst-italic text--sm');
                html += listComponent(`@lang('Payment Gateway')`, depositData.gateway_name);
                html += listComponent(`@lang('Deposit Amount')`, depositData.amount, 'fw-semibold');
                html += listComponent(`@lang('Charge')`, depositData.charge, 'fw-semibold text--danger');
                html += listComponent(`@lang('Payable Amount')`, depositData.payable_amount, 'fw-semibold');

                if (depositData.conversion_rate) {
                    html += listComponent(`@lang('Conversion Rate')`, depositData.conversion_rate, 'fw-semibold');
                }

                if (depositData.final_amount) {
                    html += listComponent(`@lang('Receivable Amount')`, depositData.final_amount, 'fw-semibold');
                }

                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += listComponent(element.name, element.value);
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong class="text--black">@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                    modal.find('.feedback').removeClass('d-none');
                } else {
                    var adminFeedback = '';
                    modal.find('.feedback').addClass('d-none');
                }

                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

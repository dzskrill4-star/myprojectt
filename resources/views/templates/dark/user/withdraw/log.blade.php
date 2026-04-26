@extends('Template::layouts.master')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="table--responsive--lg table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Trx No.')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Charge')</th>
                            <th>@lang('Receivable')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdraws as $withdraw)
                            @php
                                $details = [];

                                foreach ($withdraw->withdraw_information as $key => $info) {
                                    $details[] = $info;
                                    if ($info->type == 'file') {
                                        $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <small class="fw-semibold"> #{{ $withdraw->trx }}</small>
                                </td>

                                <td class="text-end text-md-center">
                                    {{ showAmount($withdraw->amount, exceptZeros: true) }}
                                </td>

                                <td>
                                    {{ showAmount($withdraw->charge, exceptZeros: true) }}
                                </td>

                                <td>
                                    {{ showAmount($withdraw->amount - $withdraw->charge, exceptZeros: true) }}
                                </td>

                                <td class="text-end text-md-center">
                                    @php echo $withdraw->statusBadge @endphp
                                </td>

                                <td>
                                    @php
                                        $withdrawData = [
                                            'transaction_number' => "#{$withdraw->trx}",
                                            'initiated_at' => showDateTime($withdraw->created_at, 'M d, Y h:i A'),
                                            'method_name' => __($withdraw?->method->name),
                                            'amount' => showAmount($withdraw->amount, exceptZeros: true),
                                            'charge' => showAmount($withdraw->charge, exceptZeros: true),
                                            'receivable_amount' => showAmount($withdraw->amount - $withdraw->charge, exceptZeros: true),
                                            'conversion_rate' => $withdraw->rate != 1 ? '1 ' . gs('cur_text') . ' = ' . showAmount($withdraw->rate, currencyFormat: false) . ' ' . $withdraw->currency : null,
                                            'final_amount' => $withdraw->rate != 1 ? showAmount($withdraw->final_amount, currencyFormat: false, exceptZeros: true) . ' ' . __($withdraw->currency) : null,
                                        ];
                                    @endphp

                                    <button type="button" class="btn btn--base btn--xsm detailBtn" data-withdraw_data='{{ json_encode($withdrawData) }}' data-user_data="{{ json_encode($details) }}" @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif data-bs-toggle="modal" data-bs-target="#detailModal">
                                        <i class="las la-desktop"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text--base text-end text-md-center" colspan="100%">{{ __($emptyMessage) }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ paginateLinks($withdraws) }}
                </div>
            </div>
        </div>
    </div>

    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData"></ul>
                    <div class="feedback"></div>
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
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var withdrawData = $(this).data('withdraw_data');

                var html = ``;

                html += listComponent(`@lang('Transaction Number')`, withdrawData.transaction_number, 'fw-semibold text--sm');
                html += listComponent(`@lang('Initiated At')`, withdrawData.initiated_at, 'fst-italic text--sm');
                html += listComponent(`@lang('Withdrawal Method')`, withdrawData.method_name);
                html += listComponent(`@lang('Requested Amount')`, withdrawData.amount, 'fw-semibold');
                html += listComponent(`@lang('Processing Charge')`, withdrawData.charge, 'fw-semibold text--danger');
                html += listComponent(`@lang('Receivable Amount')`, withdrawData.receivable_amount, 'fw-semibold');

                if (withdrawData.conversion_rate) {
                    html += listComponent(`@lang('Conversion Rate')`, withdrawData.conversion_rate, 'fw-semibold');
                }

                if (withdrawData.final_amount) {
                    html += listComponent(`@lang('Receivable Amount')`, withdrawData.final_amount, 'fw-semibold');
                }

                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += listComponent(element.name, element.value);
                    } else {
                        html += listComponent(element.name, `<a href="${element.value}" class="text--base"><i class="fa-regular fa-file"></i> @lang('Attachment')</a>`);
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') !== undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush

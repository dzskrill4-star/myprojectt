@php
    $content = getContent('transaction.content', true);
    $deposits = App\Models\Deposit::where('status', Status::PAYMENT_SUCCESS)->with('user')->orderBy('id', 'DESC')->take(5)->get();
    $withdraws = App\Models\Withdrawal::whereIn('status', [Status::PAYMENT_SUCCESS, Status::PAYMENT_PENDING])
        ->with('user', 'userCoinBalance.miner')
        ->orderBy('id', 'DESC')
        ->take(5)
        ->get();
@endphp

<section class="transaction py-100 section-bg">
    <div class="container">
        <div class="section-heading">
            <h3 class="section-heading__title">{{ __($content->data_values->heading ?? '') }}</h3>
            <p class="section-heading__desc">{{ __($content->data_values->description ?? '') }}</p>
        </div>
        <div class="row">
            <div class="col-lg-6 pe-lg-4">
                <div class="transaction-content">
                    <h5 class="transaction-content__title">@lang('Latest Payments')</h5>
                    <table class="table--responsive--lg table">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deposits as $deposit)
                                <tr>
                                    <td>
                                        <span class="user-info__name">{{ $deposit->user->fullname }} </span>
                                    </td>
                                    <td>{{ showAmount($deposit->amount) }}</td>
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
            <div class="col-lg-6 ps-lg-4">
                <div class="transaction-content right">
                    <h5 class="transaction-content__title text-end">@lang('Latest Withdrawals')</h5>
                    <table class="table--responsive--lg table">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($withdraws as $withdraw)
                                <tr>
                                    <td>
                                        <span class="user-info__name">{{ $withdraw->user->fullname }}</span>
                                    </td>
                                    <td>{{ showAmount($withdraw->amount) }}</td>
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
        </div>
    </div>
</section>

@extends('Template::layouts.master')

@section('content')
    <div class="card custom--card">

        <div class="card-body">
            @php
                $methodNameRaw = $data->gateway->name ?? '';
                $isBaridi = strtolower(str_replace(' ', '', $methodNameRaw)) === 'baridimob';
                $displayName = $isBaridi ? 'Deposit DZD (Baridi Mob)' : __($methodNameRaw);
                $dir = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
            @endphp
            <h5>@lang('Payment Via') {{ $displayName }}</h5>
            <form action="{{ route('user.deposit.manual.update') }}" method="POST" class="disableSubmission" enctype="multipart/form-data">
                @csrf
                <div class="row gy-4">
                    <div class="col-sm-12 text-center">
                            <b class="text--success">{{ showAmount($data['final_amount'], currencyFormat:false) . ' ' . $data['method_currency'] }} </b> @lang('for successful payment')
                        </p>
                        <h4 class="my-4 text-center">@lang('Please follow the instruction below')</h4>

                        <div class="text-center" dir="{{ $dir }}">
                            <div class="instruction-box text-center" style="max-width:680px; margin:0 auto; text-align:inherit;">
                                {!! $isBaridi ? __('manual.deposit_baridimob_instructions') : __('manual.deposit_usdt_trc20_instructions') !!}
                            </div>
                        </div>
                    </div>

                    <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                    @if ($isBaridi)
                    <div class="col-sm-12">
                        <label class="form-label">@lang('manual.payment_receipt_required')</label>
                        <input type="file" name="receipt_image" class="form-control" accept="image/*,application/pdf" required>
                    </div>
                    @endif

                    <div class="col-sm-12">
                        <div class="form-group">
                            <button class="btn btn--base w-100" type="submit">@lang('Pay Now')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
<style>
    .instruction-box div { display:block; margin-bottom:6px; }
    .instruction-box div:last-child { margin-bottom:0; }
</style>
@endpush

@extends('Template::layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <h5 class="card-title">{{ __('manual.withdraw_via_usdt') }}</h5>
            
        </div>
                <div class="card-body">
            <div class="alert custom--alert--base">
                <p class="mb-0"></i>{{ __('manual.You_are_requesting') }}
                <b>{{ showAmount($withdraw->amount) }}</b> {{ __('manual.for_withdraw') }}.    
                </b>{{ __('manual.The_admin_will_send_you') }}

                    </b>{{ __('manual.to_your_account') }}
            </p>
            </div>
            <form action="{{ route('user.withdraw.submit') }}" class="disableSubmission" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
{{ __('manual.withdraw_warning') }}
                </div>
                <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                @if (auth()->user()->ts)
                    <div class="form-group">
                        <label>@lang('Google Authenticator Code')</label>
                        <input type="text" name="authenticator_code" class="form-control form--control" required>
                    </div>
                @endif
                <div class="form-group">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

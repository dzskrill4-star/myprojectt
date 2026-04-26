@extends('Template::layouts.master')

@section('content')
    <div class="card custom--card card-deposit">
        <div class="card-body card-body-deposit text-center">
            <h5>@lang('Payment Preview')</h5>
            <h4 class="my-2"> @lang('PLEASE SEND EXACTLY') <span class="text-success"> {{ $data->amount }}</span> {{ __($data->currency) }}</h4>
            <h5 class="mb-2">@lang('TO') <span style="color: #ff3b3b !important;"> {{ $data->sendto }}</span></h5>
            <img src="{{ $data->img }}" alt="@lang('Image')">
            <h4 class="bold mt-4 text-white">@lang('SCAN TO SEND')</h4>
        </div>
    </div>
@endsection

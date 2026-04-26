@extends('Template::layouts.frontend_unauth')
@section('content')
    <section class="account section-bg py-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-6">
                    <div class="card custom--card">
                        <div class="card-body">
                            <h4 class="mb-1">{{ __($pageTitle) }}</h4>
                            <div class="mb-4">
                                <p>@lang('To recover your account please provide your email or username to find your account.')</p>
                            </div>
                            <form method="POST" action="{{ route('user.password.email') }}" class="verify-gcaptcha">
                                @csrf
                                <div class="row gy-3">
                                    <div class="col-12">
                                        <input class="form--control" name="value" type="text" value="{{ old('value') }}" placeholder="@lang('Email or Username')" required autofocus="off">
                                    </div>
                                    <div class="col-12">
                                        <x-captcha />
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

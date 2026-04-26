@php
    $content = getContent('login.content', true);
@endphp
@extends('Template::layouts.frontend_unauth')
@section('content')
    <section class="account section-bg py-100">
        <div class="container">
            <div class="row align-items-center justify-content-center justify-content-lg-between gy-4">
                <div class="col-lg-5 d-lg-block d-none">
                    <div class="account-content">
                        <div class="account-content__thumb">
                            <img src="{{ frontendImage('login', $content->data_values->image ?? '', '420x410') }}" alt="Login">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-10">
                    <div class="contact-form">
                        <h4 class="account-title pb-3 text-center"> {{ __($content->data_values->title ?? '') }}</h4>

                        @include('Template::partials.social_login')

                        <form class="verify-gcaptcha" method="POST" action="{{ route('user.login') }}" autocomplete="off">
                            @csrf

                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">@lang('Username')</label>
                                    <input class="form--control" name="username" type="text" value="{{ old('username') }}" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">@lang('Password')</label>
                                    <div class="input-group">
                                        <input class="form--control" id="your-password" name="password" type="password" required>
                                        <div class="password-show-hide fas fa-eye toggle-password" id="#your-password"></div>
                                    </div>
                                </div>

                                <x-captcha />

                                <div class="col-12">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="form--check mb-0">
                                            <input class="form-check-input" id="rem-me" name="remember" type="checkbox">
                                            <label class="form-check-label mb-0 ms-1" for="rem-me">@lang('Remember Me')</label>
                                        </div>
                                        <div>
                                            <a class="checkbox__forgot-pass text--base" href="{{ route('user.password.request') }}">@lang('Forgot Password?')</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn--base w-100" type="submit"> @lang('Login') </button>
                                </div>
                            </div>

                        </form>

                        @if (gs('registration'))
                            <div class="mt-3">
                                <p>@lang('Don\'t have an account?') <a class="text--base" href="{{ route('user.register') }}">@lang('Register')</a></p>
                            </div>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML =
                    `<span style="color:red;">@lang('Captcha field is required.')</span>`;
                return false;
            }

            return true;
        }

        function verifyCaptcha() {
            document.getElementById('g-recaptcha-error').innerHTML = '';
        }
    </script>
@endpush

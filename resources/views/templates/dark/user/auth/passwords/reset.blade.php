@extends('Template::layouts.frontend_unauth')
@section('content')
    <section class="account section-bg py-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="card custom--card">
                        <div class="card-body">
                            <h4 class="mb-1">{{ __($pageTitle) }}</h4>
                            <div class="mb-3">
                                <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                            </div>
                            <form action="{{ route('user.password.update') }}" method="POST">
                                @csrf
                                <input name="email" type="hidden" value="{{ $email }}">
                                <input name="token" type="hidden" value="{{ $token }}">

                                <div class="row gy-3">
                                    <div class="col-12">
                                        <label class="form-label">@lang('Password')</label>
                                        <div class="input-group">
                                            <input class="form--control @if (gs('secure_password')) secure-password @endif" name="password" required type="password">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">@lang('Confirm Password')</label>
                                        <input class="form--control" name="password_confirmation" required type="password">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn--base w-100" type="submit"> @lang('Submit')</button>
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

@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush

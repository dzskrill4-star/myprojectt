@extends('Template::layouts.app')
@section('panel')
    <div class="sidebar-overlay"></div>

    @include('Template::partials.header_auth')
    <div class="dashboard py-40 section-bg">
        <div class="container">
            <div class="dashboard-wrapper">
                @include('Template::partials.sidenav')
                <div class="dashboard-body">
                    @if (!Route::is('user.home'))
                        <div class="d-flex gap-3 flex-wrap justify-content-between mb-3">
                            <h5 class="mb-0">{{ __($pageTitle) }}</h5>
                            @stack('breadcrumb')
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @include('Template::partials.footer_bottom')
@endsection


@push('style')
    <link href="{{ asset($activeTemplateTrue . 'css/dashboard.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #171f2a;
        }
    </style>
@endpush

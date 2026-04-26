@extends('Template::layouts.app')
@section('panel')
    @include('Template::partials.header')
    @if (!request()->routeIs('home'))
        @include('Template::partials.breadcrumb')
    @endif

    @yield('content')

    @include('Template::sections.footer')

    {{-- Cookie Consent Disabled --}}
    {{-- @include('Template::partials.cookie_modal') --}}
@endsection

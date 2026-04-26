@extends('Template::layouts.app')
@section('panel')
    @include('Template::partials.header')

    @yield('content')

    @include('Template::sections.footer')

    {{-- Cookie Consent Disabled --}}
    {{-- @include('Template::partials.cookie_modal') --}}
@endsection


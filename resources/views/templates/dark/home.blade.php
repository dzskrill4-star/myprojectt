@extends('Template::layouts.frontend')
@section('content')
    @include('Template::sections.banner')
    @include('Template::sections.calculate')

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection

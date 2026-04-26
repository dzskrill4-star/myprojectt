@extends('Template::layouts.frontend')

@section('content')
    @if ($sections != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection

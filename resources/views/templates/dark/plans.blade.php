@extends('Template::layouts.frontend')
@php
    $content = getContent('plan.content', true);
@endphp
@section('content')
    <section class="plan py-100 section-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @include('Template::partials.plan_card', ['miners' => $miners])
                </div>
            </div>
        </div>
    </section>
    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif

@endsection

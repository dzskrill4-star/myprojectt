@extends('Template::layouts.frontend')
@section('content')
    <div class="dashboard-section py-100">
        <div class="container">

    @php
        $data = $policy->data_values;
    @endphp

    @if(app()->getLocale() == 'ar')
        {!! purify_html($data->description_ar ?? '') !!}
    @else
        {!! purify_html($data->description_en ?? '') !!}
    @endif

  </div>
</div>
@endsection

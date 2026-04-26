@php
    $content = getContent('banner.content', true);
@endphp

<section class="banner bg-img bg-overlay-one" style="background-image: url({{ frontendImage('banner' , $content->data_values->image ?? '', '1920x815') }});">
    <div class="container">
        <div class="row align-items-center justify-content-center justify-content-lg-start">
            <div class="col-lg-6 col-md-12">
                <div class="banner-content text-center text-lg-start">
                    <h1 class="banner-content__title">{{ __($content->data_values->heading ?? '') }}</h1>
                    <div class="banner-content__desc">
<h1>{{ __('Simple & Powerful Litecoin Mining') }}</h1>
<p>{{ __('Start mining Litecoin with high speed & secure system') }}</p>

<div class="box">⚡ {{ __('Fast Performance') }}</div>
<div class="box">🔒 {{ __('Secure System') }}</div>
<div class="box">🤖 {{ __('Smart Fully Automated System') }}</div>

</div>

                    <div class="d-flex flex-column gap-2">
                        <a class="btn btn--base" href="{{ $content->data_values->button_url ?? '' }}">{{ __($content->data_values->button_text ?? '') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

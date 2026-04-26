<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ textDirection() }}" itemscope itemtype="http://schema.org/WebPage">
<head>
        <meta property="og:image" content="{{ asset('assets/images/share-og-v2.jpg') }}">

    <meta property="og:image:secure_url" content="{{ asset('assets/images/share-og-v2.jpg') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ gs()->siteName(__($pageTitle ?? '')) }}</title>

    {{-- SEO basics (بدون og:image) --}}
{{-- @include('partials.seo') --}}

    {{-- Open Graph (Facebook / WhatsApp / Messenger) --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="Litecoin Mining & Investment Platform">
    <meta property="og:description" content="Official Litecoin investment community. Real withdrawals & verified proofs.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Litecoin Invest">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1472">
    <meta property="og:image:height" content="704">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Litecoin Mining & Investment Platform">
    <meta name="twitter:description" content="Official Litecoin investment community. Real withdrawals & verified proofs.">
    <meta name="twitter:image" content="{{ asset('assets/images/share-og-v2.jpg') }}">

    {{-- CSS --}}
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">

    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/core.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/components.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/effects.css') }}" rel="stylesheet">

    @if (request()->routeIs('user.login') || request()->routeIs('user.register'))
        <link href="{{ asset($activeTemplateTrue . 'css/auth.css') }}" rel="stylesheet">
    @endif

    @stack('style-lib')
    @stack('style')

    <link href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/rtl-ltr.css') }}" rel="stylesheet">
</head>

@php echo  loadExtension('google-analytics') @endphp

<body>
    @stack('fbComment')
    <div class="preloader">
        <div class="loader-p"></div>
    </div>

    @yield('panel')

    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    @stack('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>


    @php echo  loadExtension('tawk-chat') @endphp

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        'use strict';
        (function($) {
            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });
        })(jQuery)
    </script>
</body>

</html>

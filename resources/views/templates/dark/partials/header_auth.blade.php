@php
    $contactCaption = getContent('contact_us.content', true);
    $pages = App\Models\Page::where('is_default', Status::NO)->where('tempname', $activeTemplate)->get();

    if (gs('multi_language')) {
        $language = getLanguages();
        $default = getLanguages(true);
    }
@endphp

<header class="header-bottom py-3" id="header">
    <div class="container">
        <nav class="navbar">
            <a class="navbar-brand logo" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="Logo">
            </a>

            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between ms-auto">
                @include('Template::partials.auth_menu_dropdown')

                <div class="sidenav-bar d-lg-none d-block">
                    <span class="sidenav-bar__icon">
                        <i class="las la-bars"></i>
                    </span>
                </div>
            </div>
        </nav>
    </div>
</header>

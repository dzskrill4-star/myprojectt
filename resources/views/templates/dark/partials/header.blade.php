@php
    $contactCaption = getContent('contact_us.content', true);
    $pages = App\Models\Page::where('is_default', Status::NO)->where('tempname', $activeTemplate)->get();

    if (gs('multi_language')) {
        $language = getLanguages();
        $default = getLanguages(true);
    }
@endphp

<header class="header-bottom" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="language-switcher-top d-flex align-items-center ms-auto me-3">
    @include('Template::partials.language_dropdown')
</div>

            <a class="navbar-brand logo" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="Logo">
            </a>
            <button class="navbar-toggler header-button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="navbar-collapse collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu m-auto">
                    <li class="nav-item"><a class="nav-link {{ menuActive('home') }}"
                           href="{{ route('home') }}">@lang('Home')</a></li>
                    @foreach ($pages as $item)
                        <li class="nav-item"><a class="nav-link {{ menuActive('pages', $item->slug) }}" href="{{ route('pages', ['slug' => $item->slug]) }}">{{ __($item->name) }}</a></li>
                    @endforeach
                    <li class="nav-item"><a class="nav-link {{ menuActive('plans') }}" href="{{ route('plans') }}">@lang('Mining Plans')</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://forum.technow.top/">@lang('Forum')</a></li>
                    <li class="nav-item"><a class="nav-link {{ menuActive('contact') }}" href="{{ route('contact') }}">@lang('Contact')</a></li>
                </ul>

                <div class="gap-x-2 d-flex flex-wrap flex-lg-nowrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-sm-0 user-btn-group flex-wrap gap-1">
                        @guest
                            @if (gs('registration'))
                                <a class="btn btn--base btn--sm ms-sm-3 register-btn ms-0 outline"
                                   href="{{ route('user.register') }}">@lang('Register')</a>
                            @endif
                            <a class="btn btn--base btn--sm ms-sm-3 ms-0"
                               href="{{ route('user.login') }}">@lang('Login')</a>
                        @else
                            @if (!request()->routeIs('user*') && !request()->routeIs('ticket*'))
                                <a class="btn btn--base btn--sm ms-sm-3 ms-0"
                                   href="{{ route('user.home') }}">@lang('Dashboard')</a>
                            @endif
                            <a class="btn btn--danger btn--sm ms-sm-3 ms-0"
                               href="{{ route('user.logout') }}">@lang('Logout')</a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>

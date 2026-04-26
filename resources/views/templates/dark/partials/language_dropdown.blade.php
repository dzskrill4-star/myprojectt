@if (gs('multi_language'))
    <div class="dropdown">
        <button class="dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="language-content">
                <span class="language_flag">
                    <img src="{{ getImage(getFilePath('language') . '/' . $default->image, getFileSize('language')) }}" alt="">
                </span>
                <span class="language_text_select">{{ __($default->name) }}</span>
            </span>
            <span class="collapse-icon"><i class="las la-angle-down"></i></span>
        </button>
        <ul class="dropdown-menu langList">
            @foreach ($language->where('code', '!=', $default->code) as $lang)
                <li>
                    <a href="{{ route('lang', $lang->code) }}" class="dropdown-item d-flex gap-2">
                        <span class="language_flag">
                            <img src="{{ getImage(getFilePath('language') . '/' . $lang->image, getFileSize('language')) }}" alt="flag">
                        </span>
                        <p class="language_text">{{ __($lang->name) }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

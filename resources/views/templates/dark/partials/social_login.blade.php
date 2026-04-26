@php
    $text = isset($register) ? 'Continue ' : 'Login';
    $isGoogleEnabled = gs('socialite_credentials')?->google->status == Status::ENABLE;
    $isFacebookEnabled = gs('socialite_credentials')?->facebook->status == Status::ENABLE;
    $isLinkedInEnabled = gs('socialite_credentials')?->linkedin->status == Status::ENABLE;
@endphp

@if ($isGoogleEnabled || $isFacebookEnabled || $isLinkedInEnabled)
    <div class="social-auth text-center mb-3">
        <div class="social-auth-list">
            @if ($isGoogleEnabled)
                <a href="{{ route('user.social.login', 'google') }}" class="social-login-btn google-color">
                    <span class="auth-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                            <g>
                                <path d="M113.47 309.408 95.648 375.94l-65.139 1.378C11.042 341.211 0 299.9 0 256c0-42.451 10.324-82.483 28.624-117.732h.014L86.63 148.9l25.404 57.644c-5.317 15.501-8.215 32.141-8.215 49.456.002 18.792 3.406 36.797 9.651 53.408z" style="" fill="#fbbb00" data-original="#fbbb00" class=""></path>
                                <path d="M507.527 208.176C510.467 223.662 512 239.655 512 256c0 18.328-1.927 36.206-5.598 53.451-12.462 58.683-45.025 109.925-90.134 146.187l-.014-.014-73.044-3.727-10.338-64.535c29.932-17.554 53.324-45.025 65.646-77.911h-136.89V208.176h245.899z" style="" fill="#518ef8" data-original="#518ef8"></path>
                                <path d="m416.253 455.624.014.014C372.396 490.901 316.666 512 256 512c-97.491 0-182.252-54.491-225.491-134.681l82.961-67.91c21.619 57.698 77.278 98.771 142.53 98.771 28.047 0 54.323-7.582 76.87-20.818l83.383 68.262z" style="" fill="#28b446" data-original="#28b446"></path>
                                <path d="m419.404 58.936-82.933 67.896C313.136 112.246 285.552 103.82 256 103.82c-66.729 0-123.429 42.957-143.965 102.724l-83.397-68.276h-.014C71.23 56.123 157.06 0 256 0c62.115 0 119.068 22.126 163.404 58.936z" style="" fill="#f14336" data-original="#f14336"></path>
                            </g>
                        </svg>
                    </span>
                    <span>
                        @lang($text . ' with Google')
                    </span>
                </a>
            @endif

            @if ($isFacebookEnabled)
                <a href="{{ route('user.social.login', 'facebook') }}" class="social-login-btn facebook-color">
                    <span class="auth-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                            <g>
                                <path fill="#1877f2" d="M512 256c0 127.78-93.62 233.69-216 252.89V330h59.65L367 256h-71v-48.02c0-20.25 9.92-39.98 41.72-39.98H370v-63s-29.3-5-57.31-5c-58.47 0-96.69 35.44-96.69 99.6V256h-65v74h65v178.89C93.62 489.69 0 383.78 0 256 0 114.62 114.62 0 256 0s256 114.62 256 256z" opacity="1" data-original="#1877f2" class=""></path>
                                <path fill="#ffffff" d="M355.65 330 367 256h-71v-48.021c0-20.245 9.918-39.979 41.719-39.979H370v-63s-29.296-5-57.305-5C254.219 100 216 135.44 216 199.6V256h-65v74h65v178.889c13.034 2.045 26.392 3.111 40 3.111s26.966-1.066 40-3.111V330z" opacity="1" data-original="#ffffff" class=""></path>
                            </g>
                        </svg>
                    </span>

                    @lang($text . ' with Facebook')
                </a>
            @endif

            @if ($isLinkedInEnabled)
                <a href="{{ route('user.social.login', 'linkedin') }}" class="social-login-btn linkedin-color">
                    <span class="auth-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 176 176" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                            <g>
                                <g data-name="Layer 2">
                                    <rect width="176" height="176" fill="#0077b5" rx="24" opacity="1" data-original="#0077b5" class=""></rect>
                                    <g fill="#fff">
                                        <path d="M63.4 48a15 15 0 1 1-15-15 15 15 0 0 1 15 15zM60 73v66.27a3.71 3.71 0 0 1-3.71 3.73H40.48a3.71 3.71 0 0 1-3.72-3.72V73a3.72 3.72 0 0 1 3.72-3.72h15.81A3.72 3.72 0 0 1 60 73zM142.64 107.5v32.08a3.41 3.41 0 0 1-3.42 3.42h-17a3.41 3.41 0 0 1-3.42-3.42v-31.09c0-4.64 1.36-20.32-12.13-20.32-10.45 0-12.58 10.73-13 15.55v35.86A3.42 3.42 0 0 1 90.3 143H73.88a3.41 3.41 0 0 1-3.41-3.42V72.71a3.41 3.41 0 0 1 3.41-3.42H90.3a3.42 3.42 0 0 1 3.42 3.42v5.78c3.88-5.82 9.63-10.31 21.9-10.31 27.18 0 27.02 25.38 27.02 39.32z" fill="#ffffff" opacity="1" data-original="#ffffff" class=""></path>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </span>

                    @lang($text . ' with linkedin')
                </a>
            @endif
        </div>
        <span class="text-muted">@lang('Or')</span>
    </div>
    @push('style')
        <style>
            .social-auth {
                margin-top: 15px;
            }

            .auth-devide {
                position: relative;
                text-align: center;
            }

            .auth-devide::after {
                content: "";
                position: absolute;
                height: 1px;
                width: 50%;
                top: 50%;
                left: 50%;
                z-index: 1;
                transform: translateX(-50%);
                background-color: rgb(206 212 218);
            }

            .auth-devide span {
                background: rgb(255 255 255);
                font-size: 18px;
                position: relative;
                z-index: 2;
                padding-inline: 6px;
            }

            .social-auth-list {
                display: flex;
                flex-direction: column;
                gap: 16px;
                margin-bottom: 15px;
            }

            .social-login-btn {
                display: flex;
                justify-content: center;
                border-radius: .3rem;
                gap: 13px;
                padding: .75rem;
                color: #ddd;
                border: 1px solid #e7e7e7;
                transition: all 0.3s ease;
            }

            .social-login-btn:hover {
                background-color: hsl(var(--base));
                color: #fff;
            }

            .auth-icon {
                width: 24px;
            }

            .auth-icon svg {
                width: inherit;
                height: inherit;
            }

            .social-login-btn:hover {
                border: 1px solid hsl(var(--base) / 0.5);
                box-shadow: 0 4px 3px 0 hsl(0deg 0% 13.53% / 3%);
            }
        </style>
    @endpush
@endif


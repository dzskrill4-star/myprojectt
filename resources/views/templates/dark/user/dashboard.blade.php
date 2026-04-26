
@extends('Template::layouts.master')
@section('content')
    <div class="notice"></div>
    @if (gs('kv') && $user->kv != Status::KYC_VERIFIED)
        @php
            $kyc = getContent('kyc.content', true);
        @endphp
        <div class="row mb-3">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="alert-heading mb-0">@lang('KYC Documents Rejected')</h4>
                                <button class="btn btn--danger btn-sm" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Show Reason')</button>
                            </div>
                            <div class="alert alert-danger" role="alert">
                                <p>{{ __($kyc->data_values->reject ?? '') }} <a class="text--base text-decoration-underline" href="{{ route('user.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.</p>
                                <a class="text--base text-decoration-underline mt-2" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                            </div>
                        @elseif($user->kv == Status::KYC_UNVERIFIED)
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading mb-1">@lang('KYC Verification required')</h4>
                                <p>{{ __($kyc->data_values->required ?? '') }} <a class="text--base text-decoration-underline" href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a></p>
                            </div>
                        @elseif($user->kv == Status::KYC_PENDING)
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">@lang('KYC Verification pending')</h4>
                                <p>{{ __($kyc->data_values->pending ?? '') }} <a class="text--base text-decoration-underline" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- dashboard-section start -->
    <div class="row gy-4">
        <div class="col-sm-6">
            <div class="dashboard-card d-flex flex-column gap-3 h-100 justify-content-between">
                <div class="dashboard-card__top">
                    <div class="dashboard-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                            <g>
                                <linearGradient id="a" x1="262.399" x2="259.689" y1="468.969" y2="389.513" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FFB92D" stop-color="#ffb92d"></stop>
                                    <stop offset="1" style="stop-color:#F59500" stop-color="#f59500"></stop>
                                </linearGradient>
                                <path d="M451.284 469.456H60.716c-20.816 0-37.691-16.875-37.691-37.691v-325.22c0-20.816 16.875-37.691 37.691-37.691h390.568c20.816 0 37.691 16.875 37.691 37.691v325.22c0 20.816-16.875 37.691-37.691 37.691z" style="fill:url(#a);" fill=""></path>
                                <linearGradient id="b" x1="275.392" x2="268.012" y1="416.213" y2="355.343" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FFB92D" stop-color="#ffb92d"></stop>
                                    <stop offset="1" style="stop-color:#F59500" stop-color="#f59500"></stop>
                                </linearGradient>
                                <path d="M442.574 460.522H69.426c-19.888 0-36.011-16.123-36.011-36.011V113.797c0-19.888 16.122-36.011 36.011-36.011h373.147c19.888 0 36.011 16.122 36.011 36.011v310.715c.001 19.888-16.122 36.01-36.01 36.01z" style="fill:url(#b);" fill=""></path>
                                <linearGradient id="c" x1="251.99" x2="251.99" y1="414.413" y2="368.043" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#A7F3CE" stop-color="#a7f3ce"></stop>
                                    <stop offset="1" style="stop-color:#61DB99" stop-color="#61db99"></stop>
                                </linearGradient>
                                <path d="M463.721 174.051 369.776 11.335C363.519.496 349.658-3.218 338.819 3.041L42.621 174.051h421.1z" style="fill:url(#c);" fill=""></path>
                                <linearGradient id="d" x1="254.809" x2="254.809" y1="334.143" y2="204.023" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FF4C54" stop-color="#ff4c54"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <path d="M23.025 305.009h465.95V181.597c0-15.577-12.627-28.204-28.204-28.204H51.229c-15.577 0-28.204 12.627-28.204 28.204v123.412z" style="fill:url(#d);" fill=""></path>
                                <linearGradient id="e" x1="254.809" x2="254.809" y1="237.523" y2="36.563" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FF4C54" stop-color="#ff4c54"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <path d="M23.025 258.595v173.17c0 20.816 16.875 37.692 37.692 37.692h390.567c20.816 0 37.691-16.875 37.691-37.691V258.595c0-12.857-10.423-23.28-23.28-23.28H46.305c-12.857 0-23.28 10.422-23.28 23.28z" style="fill:url(#e);" fill=""></path>
                                <linearGradient id="f" x1="460.479" x2="426.619" y1="196.424" y2="196.424" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#BE3F45;stop-opacity:0" stop-color="#be3f45;stop-opacity:0"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <path d="M418.679 169.277v300.181h32.605c20.816 0 37.691-16.875 37.691-37.691V239.573l-70.296-70.296z" style="fill:url(#f);" fill=""></path>
                                <linearGradient id="g" x1="157.653" x2="329.903" y1="231.185" y2="151.729" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FFB92D" stop-color="#ffb92d"></stop>
                                    <stop offset="1" style="stop-color:#F59500" stop-color="#f59500"></stop>
                                </linearGradient>
                                <path d="M386.897 511.717 59.81 473.729c-20.968-2.436-36.785-20.195-36.785-41.304v-328.69a13.914 13.914 0 0 0 12.308 13.821l361.157 41.946c20.968 2.436 36.785 20.195 36.785 41.304v269.608c0 24.865-21.679 44.173-46.378 41.303z" style="fill:url(#g);" fill=""></path>
                                <linearGradient id="h" x1="221.581" x2="214.46" y1="54.273" y2="-.147" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#BE3F45;stop-opacity:0" stop-color="#be3f45;stop-opacity:0"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <path d="M386.897 446.063 59.81 408.075c-20.968-2.436-36.785-20.195-36.785-41.304v65.654c0 21.109 15.817 38.869 36.785 41.304l327.087 37.988c24.699 2.869 46.379-16.438 46.379-41.304v-65.654c-.001 24.866-21.68 44.172-46.379 41.304z" style="fill:url(#h);" fill=""></path>
                                <linearGradient id="i" x1="44.49" x2="44.49" y1="376.824" y2="28.814" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FFF465" stop-color="#fff465"></stop>
                                    <stop offset="1" style="stop-color:#FFE600" stop-color="#ffe600"></stop>
                                </linearGradient>
                                <path d="M52.35 154.966c-.274 0-.552-.016-.831-.046l-14.968-1.646a7.529 7.529 0 1 1 1.644-14.968l14.968 1.646a7.529 7.529 0 0 1-.813 15.014z" style="fill:url(#i);" fill=""></path>
                                <linearGradient id="j" x1="244.542" x2="244.542" y1="376.813" y2="29.533" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FFF465" stop-color="#fff465"></stop>
                                    <stop offset="1" style="stop-color:#FFE600" stop-color="#ffe600"></stop>
                                </linearGradient>
                                <path
                                    d="M359.915 486.911c-.316 0-.638-.021-.96-.061l-29.091-3.699a7.53 7.53 0 0 1-6.519-8.42c.525-4.124 4.294-7.027 8.42-6.519l29.091 3.699a7.529 7.529 0 0 1-.941 15zm28.634-3.727a7.529 7.529 0 0 1-6.816-4.323 7.527 7.527 0 0 1 3.602-10.019c7.639-3.6 13.384-9.57 15.763-16.382a7.534 7.534 0 0 1 9.591-4.626 7.53 7.53 0 0 1 4.626 9.591c-3.757 10.758-12.124 19.651-23.562 25.039a7.506 7.506 0 0 1-3.204.72zm-86.815-3.671c-.316 0-.638-.021-.96-.061l-29.091-3.699a7.529 7.529 0 0 1-6.519-8.42c.525-4.124 4.294-7.028 8.42-6.519l29.091 3.699a7.529 7.529 0 0 1-.941 15zm-58.181-7.399a7.82 7.82 0 0 1-.96-.061l-29.091-3.699a7.529 7.529 0 1 1 1.901-14.939l29.091 3.699a7.529 7.529 0 0 1-.941 15zm-58.183-7.399a7.82 7.82 0 0 1-.96-.061l-29.092-3.7a7.529 7.529 0 0 1-6.519-8.42c.525-4.125 4.296-7.029 8.42-6.519l29.092 3.7a7.529 7.529 0 0 1 6.519 8.42 7.53 7.53 0 0 1-7.46 6.58zm-58.181-7.399a7.82 7.82 0 0 1-.96-.061l-29.092-3.7a7.529 7.529 0 0 1-6.519-8.42c.525-4.125 4.296-7.03 8.42-6.519l29.092 3.7a7.53 7.53 0 0 1-.941 15zm282.707-23.975a7.528 7.528 0 0 1-7.529-7.529v-29.325a7.528 7.528 0 0 1 7.529-7.529 7.528 7.528 0 0 1 7.529 7.529v29.325a7.527 7.527 0 0 1-7.529 7.529zm0-58.649a7.528 7.528 0 0 1-7.529-7.529v-29.325a7.528 7.528 0 0 1 7.529-7.529 7.528 7.528 0 0 1 7.529 7.529v29.325a7.527 7.527 0 0 1-7.529 7.529zm0-58.65a7.528 7.528 0 0 1-7.529-7.529v-29.325a7.528 7.528 0 0 1 7.529-7.529 7.528 7.528 0 0 1 7.529 7.529v29.325a7.527 7.527 0 0 1-7.529 7.529zm0-58.65a7.528 7.528 0 0 1-7.529-7.529v-29.325a7.528 7.528 0 0 1 7.529-7.529 7.528 7.528 0 0 1 7.529 7.529v29.325a7.527 7.527 0 0 1-7.529 7.529zm-10.53-55.645a7.504 7.504 0 0 1-5.177-2.064c-5.581-5.288-13.326-8.657-21.811-9.488l-.199-.021a7.528 7.528 0 0 1-6.68-8.292 7.527 7.527 0 0 1 8.292-6.68l.128.014c11.727 1.149 22.629 5.959 30.628 13.536a7.53 7.53 0 0 1-5.181 12.995zm-55.523-14.734c-.274 0-.552-.016-.831-.046l-29.149-3.205a7.53 7.53 0 0 1-6.662-8.306c.455-4.133 4.176-7.108 8.306-6.662l29.149 3.205a7.53 7.53 0 0 1-.813 15.014zm-58.297-6.41c-.274 0-.552-.016-.831-.046l-29.15-3.205a7.53 7.53 0 0 1-6.662-8.306c.454-4.134 4.181-7.112 8.306-6.662l29.15 3.205a7.53 7.53 0 0 1-.813 15.014zm-58.3-6.409c-.274 0-.552-.016-.831-.046l-29.149-3.205a7.53 7.53 0 0 1 1.644-14.968l29.149 3.205a7.53 7.53 0 0 1-.813 15.014zm-58.299-6.409c-.274 0-.552-.016-.831-.046l-29.149-3.205a7.529 7.529 0 1 1 1.644-14.968l29.149 3.205a7.53 7.53 0 0 1-.813 15.014zm-58.299-6.41c-.274 0-.552-.016-.831-.046l-29.149-3.205a7.53 7.53 0 0 1-6.662-8.306c.455-4.132 4.171-7.108 8.306-6.662l29.149 3.205a7.529 7.529 0 0 1-.813 15.014z"
                                    style="fill:url(#j);" fill=""></path>
                                <linearGradient id="k" x1="61.026" x2="61.026" y1="376.833" y2="28.833" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FFF465" stop-color="#fff465"></stop>
                                    <stop offset="1" style="stop-color:#FFE600" stop-color="#ffe600"></stop>
                                </linearGradient>
                                <path d="M68.937 449.907c-.316 0-.636-.02-.959-.061l-14.939-1.899a7.53 7.53 0 0 1 1.899-14.939l14.939 1.899a7.529 7.529 0 0 1 6.52 8.419 7.533 7.533 0 0 1-7.46 6.581z" style="fill:url(#k);" fill=""></path>
                                <path d="M345.568 56.068 297.38 82.29c-6.277 3.415-14.134 1.096-17.55-5.181-3.415-6.277-1.096-14.134 5.181-17.55L333.2 33.338c6.277-3.415 14.134-1.096 17.55 5.181 3.415 6.276 1.096 14.134-5.182 17.549z" style="" fill="#61db99" data-original="#61db99"></path>
                                <linearGradient id="l" x1="405.748" x2="302.708" y1="125.843" y2="228.883" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#BE3F45;stop-opacity:0" stop-color="#be3f45;stop-opacity:0"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <path d="M433.275 363.821 385.454 316c-6.747-7.347-16.429-11.955-27.19-11.955-20.386 0-36.912 16.527-36.912 36.912 0 10.761 4.608 20.444 11.955 27.19l99.969 99.969V363.821z" style="fill:url(#l);" fill=""></path>
                                <linearGradient id="m" x1="356.669" x2="356.669" y1="198.003" y2="144.833" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FF4C54" stop-color="#ff4c54"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <circle cx="358.26" cy="340.952" r="36.912" style="fill:url(#m);" fill=""></circle>
                                <linearGradient id="n" x1="356.669" x2="356.669" y1="158.993" y2="195.683" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#FF4C54" stop-color="#ff4c54"></stop>
                                    <stop offset="1" style="stop-color:#BE3F45" stop-color="#be3f45"></stop>
                                </linearGradient>
                                <circle cx="358.26" cy="340.952" r="25.474" style="fill:url(#n);" fill=""></circle>
                                <linearGradient id="o" x1="297.264" x2="307.764" y1="404.334" y2="357.964" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#A7F3CE" stop-color="#a7f3ce"></stop>
                                    <stop offset="1" style="stop-color:#61DB99" stop-color="#61db99"></stop>
                                </linearGradient>
                                <circle cx="348.582" cy="331.275" r="5.271" style="fill:url(#o);" fill=""></circle>
                                <linearGradient id="p" x1="316.545" x2="327.045" y1="404.331" y2="357.961" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#A7F3CE" stop-color="#a7f3ce"></stop>
                                    <stop offset="1" style="stop-color:#61DB99" stop-color="#61db99"></stop>
                                </linearGradient>
                                <circle cx="367.938" cy="331.275" r="5.271" style="fill:url(#p);" fill=""></circle>
                                <linearGradient id="q" x1="293.109" x2="303.609" y1="403.393" y2="357.023" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#A7F3CE" stop-color="#a7f3ce"></stop>
                                    <stop offset="1" style="stop-color:#61DB99" stop-color="#61db99"></stop>
                                </linearGradient>
                                <circle cx="348.582" cy="350.64" r="5.271" style="fill:url(#q);" fill=""></circle>
                                <linearGradient id="r" x1="312.39" x2="322.89" y1="403.39" y2="357.021" gradientTransform="matrix(1.0039 0 0 -1.0039 .192 516.562)" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" style="stop-color:#A7F3CE" stop-color="#a7f3ce"></stop>
                                    <stop offset="1" style="stop-color:#61DB99" stop-color="#61db99"></stop>
                                </linearGradient>
                                <circle cx="367.938" cy="350.64" r="5.271" style="fill:url(#r);" fill=""></circle>
                            </g>
                        </svg>
                    </div>

                    <div>
                        <div class="dashboard-card__title"> @lang('Deposit Wallet')</div>
                        <div class="dashboard-card__value">{{ showAmount($user->balance) }}</div>
                    </div>
                </div>

                <div class="dashboard-card__bottom">
                    <a href="{{ route('plans') }}" class="btn btn--success">
                        <i class="la la-plus-circle"></i> @lang('Buy a plan')
                    </a>
                    <a class="text--sm text-decoration-underline mt-1" href="{{ route('user.transactions') }}?wallet_type={{ Status::DEPOSIT_WALLET }}">@lang('View Transactions')</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="dashboard-card d-flex flex-column gap-3 h-100 justify-content-between">
                <div class="dashboard-card__top">
                    <div class="dashboard-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                            <g>
                                <g fill-rule="evenodd" clip-rule="evenodd">
                                    <path fill="#da8c36" d="M457.253 503H61.603c-9.508 0-17.263-7.755-17.263-17.264v-284.01c0-16.054 13.135-29.23 29.189-29.189h359.604c4.754 0 8.632 3.878 8.632 8.632v49.745h17.264c4.754 0 8.632 3.878 8.632 8.632v253.046c-.001 5.733-4.676 10.408-10.408 10.408z" opacity="1" data-original="#da8c36" class=""></path>
                                    <path fill="#c97629" d="M441.764 181.169v49.745H73.528c-16.053 0-29.188-13.135-29.188-29.188 0-16.054 13.135-29.23 29.188-29.189h359.604c4.755 0 8.632 3.877 8.632 8.632zM364.68 316.68c-16.958 0-30.833 13.876-30.833 30.834V386.4c0 16.958 13.875 30.833 30.833 30.833h102.98V316.68z" opacity="1" data-original="#c97629"></path>
                                    <circle cx="384.187" cy="366.957" r="17.881" fill="#eff4f7" opacity="1" data-original="#eff4f7"></circle>
                                    <path fill="#ffda2d" d="M121.177 62.46h16.908c3.74.001 6.029-4.308 3.637-7.403l-34.209-44.27c-1.842-2.383-5.43-2.382-7.271 0l-34.209 44.27c-2.393 3.096-.103 7.405 3.638 7.403h16.906v81.566c0 3.316 2.705 6.02 6.02 6.02h22.559c3.315 0 6.021-2.705 6.021-6.02V62.46z" opacity="1" data-original="#ffda2d"></path>
                                    <path fill="#72d561" d="M300.354 12.372 66.399 246.326c-4.495 4.495-4.495 11.851 0 16.346l129.044 129.044c4.495 4.495 11.85 4.495 16.346 0l233.955-233.955c4.495-4.495 4.495-11.85 0-16.346L316.7 12.372c-4.495-4.496-11.851-4.496-16.346 0z" opacity="1" data-original="#72d561" class=""></path>
                                    <path fill="#3cbe52" d="M130.761 224.872 278.899 76.734c16.363 16.363 42.892 16.363 59.255 0l43.226 43.226c-16.363 16.363-16.363 42.892 0 59.255L233.242 327.354c-16.363-16.363-42.892-16.363-59.255 0l-43.226-43.226c16.363-16.364 16.363-42.893 0-59.256z" opacity="1" data-original="#3cbe52" class=""></path>
                                    <path fill="#72d561"
                                        d="M251.701 168.554a7.974 7.974 0 0 1-2.5 15.75 6.169 6.169 0 0 0-2.856.213c-.829.266-1.633.752-2.339 1.457l-3.104 3.105c-1.149 1.15-1.724 2.674-1.724 4.205s.575 3.056 1.724 4.205c1.15 1.15 2.675 1.725 4.206 1.725s3.056-.575 4.205-1.724l2.204-2.204c4.254-4.253 9.88-6.381 15.518-6.38v-.031c5.618 0 11.244 2.138 15.518 6.412s6.412 9.899 6.412 15.518a22.01 22.01 0 0 1-2.541 10.28l1.488 1.488a7.998 7.998 0 0 1 0 11.312 7.998 7.998 0 0 1-11.312 0l-1.25-1.25a22.168 22.168 0 0 1-4.677 2.124 22.065 22.065 0 0 1-10.231.776 7.974 7.974 0 0 1 2.5-15.75 6.169 6.169 0 0 0 2.856-.213c.829-.266 1.633-.752 2.339-1.457l3.104-3.105c1.149-1.149 1.724-2.674 1.724-4.205s-.575-3.056-1.724-4.205a5.923 5.923 0 0 0-4.205-1.725v-.031a5.941 5.941 0 0 0-4.206 1.755l-2.204 2.204c-4.274 4.274-9.899 6.411-15.518 6.411s-11.244-2.138-15.518-6.412c-4.274-4.275-6.412-9.899-6.412-15.518 0-3.537.848-7.076 2.541-10.279l-1.488-1.488a8 8 0 0 1 11.312-11.313l1.25 1.25a22.183 22.183 0 0 1 4.676-2.124 22.07 22.07 0 0 1 10.232-.776zm-50.893 77.44a7.998 7.998 0 0 1 11.312 0 7.998 7.998 0 0 1 0 11.312l-13.74 13.74a7.998 7.998 0 0 1-11.312 0 7.998 7.998 0 0 1 0-11.312zM313.762 133.04a7.998 7.998 0 0 1 11.312 0 7.998 7.998 0 0 1 0 11.312l-13.74 13.74a7.998 7.998 0 0 1-11.312 0 7.998 7.998 0 0 1 0-11.312z"
                                        opacity="1" data-original="#72d561" class=""></path>
                                    <path fill="#da8c36" d="M51.954 221.333v207.733h160.413l170.001-151.13v-47.022H73.529c-8.525 0-16.226-3.703-21.575-9.581z" opacity="1" data-original="#da8c36" class=""></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <div>
                        
                        <div class="dashboard-card__title"> @lang('profit wallet')</div>
                        <div class="dashboard-card__value">{{ showAmount($user->profit_wallet) }}</div>
                    </div>
                </div>
                <div class="dashboard-card__bottom">
                    <a href="{{ route('user.withdraw') }}" class="btn btn-sm btn--warning">
                        <i class="la la-minus-circle"></i> @lang('Withdraw')
                    </a>

                    <a class="text--sm text-decoration-underline mt-1" href="{{ route('user.transactions') }}?wallet_type={{ Status::PROFIT_WALLET }}">@lang('View Transactions')</a>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6 d-none">
            <div class="dashboard-card d-flex gap-3 h-100 position-relative">
                <a class="text--sm text-decoration-underline position-absolute h-100 w-100 left-0 top-0" href="{{ route('user.transactions') }}?wallet_type={{ Status::CRYPTO_WALLET }}"></a>

                <div class="dashboard-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" x="0" y="0" viewBox="0 0 60 60" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                        <g>
                            <path fill="#9ddd5f" d="M30 56h6.001C48.703 56 59 45.702 59 32.999 59 20.296 48.703 9.998 36.001 9.998H17.002V2.769c0-1.57-1.94-2.36-3.07-1.25l-12.4 12.23c-.71.69-.71 1.81 0 2.5l12.399 12.23c1.13 1.11 3.07.32 3.07-1.25v-7.23H38c7.179 0 12.999 5.82 12.999 13s-5.82 13-12.999 13h-8.001v10z" opacity="1" data-original="#9ddd5f" class=""></path>
                            <path fill="#f4b04d" d="M33 52v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#f4b04d" class=""></path>
                            <path fill="#ffcd50" d="M33 48v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#ffcd50" class=""></path>
                            <path fill="#f4b04d" d="M17 52v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#f4b04d" class=""></path>
                            <path fill="#ffcd50" d="M17 48v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#ffcd50" class=""></path>
                            <path fill="#f4b04d" d="M17 44v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#f4b04d" class=""></path>
                            <path fill="#ffcd50" d="M17 40v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#ffcd50" class=""></path>
                            <path fill="#f4b04d" d="M17 36v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4zM33 44v4c0 1.66-3.58 3-8 3s-8-1.34-8-3v-4z" opacity="1" data-original="#f4b04d" class=""></path>
                            <g fill="#ffcd50">
                                <ellipse cx="25" cy="44" rx="8" ry="3" fill="#ffcd50" opacity="1" data-original="#ffcd50" class=""></ellipse>
                                <ellipse cx="9" cy="36" rx="8" ry="3" fill="#ffcd50" opacity="1" data-original="#ffcd50" class=""></ellipse>
                            </g>
                        </g>
                    </svg>
                </div>

                <div>
                    
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card d-flex gap-3 h-100 position-relative">
                <a class="text--sm text-decoration-underline position-absolute h-100 w-100 left-0 top-0" href="{{ route('user.transactions') }}?remark=referral_commission"></a>

                <div class="dashboard-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                        <g>
                            <path fill="#4ac55e" fill-rule="evenodd" d="M18.4 227.9h421.3c10 0 18.3 8.2 18.3 18.3v219.9c0 10.1-8.2 18.3-18.3 18.3H18.4C8.3 484.4.1 476.2.1 466.1V246.2c.1-10 8.3-18.3 18.3-18.3z" clip-rule="evenodd" opacity="1" data-original="#4ac55e" class=""></path>
                            <path fill="#99ff99" fill-rule="evenodd" d="M83.2 265.7H375c0 21.1 17.2 38.3 38.3 38.3v104.3c-21.1 0-38.3 17.2-38.3 38.3H83.2c0-21.1-17.2-38.3-38.3-38.3V304c21 0 38.3-17.2 38.3-38.3z" clip-rule="evenodd" opacity="1" data-original="#99ff99"></path>
                            <path fill="#4ac55e" fill-rule="evenodd" d="M229.1 417.7c81.4-2.6 81.4-120.6 0-123.2-81.4 2.7-81.4 120.6 0 123.2z" clip-rule="evenodd" opacity="1" data-original="#4ac55e" class=""></path>
                            <path fill="#99ff99" d="M229.1 397.7c-5.2-.1-5.9-5.1-5.5-9.2h-7.9c-7.2 0-7.2-11 0-11 7-.6 21 3.1 21.3-7.9 0-4.4-3.5-7.9-7.9-7.9-21.6 0-25.9-30.9-5.5-37.1v-4.5c0-7.2 11-7.2 11 0v3.7h7.9c7.2 0 7.2 11 0 11-7 .6-21-3.1-21.3 7.9 0 4.4 3.5 7.9 7.9 7.9 21.6.1 25.9 30.9 5.5 37.1.3 4.3.2 10-5.5 10z" opacity="1" data-original="#99ff99"></path>
                            <path fill="#ffe45e" fill-rule="evenodd" d="M146.3 240.5c55.2 0 100.1-45 100.1-100.1C241 7.6 51.6 7.6 46.2 140.4c0 55.1 45 100.1 100.1 100.1z" clip-rule="evenodd" opacity="1" data-original="#ffe45e"></path>
                            <path fill="#00beae" d="M146.3 195.4c-6.8-.1-5.5-8.1-5.5-12.7h-12.9c-7.2 0-7.2-11 0-11h18.4c7.1 0 12.9-5.8 12.9-12.9s-5.8-12.9-12.9-12.9c-28.2-.1-32.6-40.8-5.5-47.2.2-4.6-1.5-13.3 5.5-13.3 6.8.1 5.5 8.1 5.5 12.7h12.9c7.2 0 7.2 11 0 11h-18.4c-7.1 0-12.9 5.8-12.9 12.9s5.8 12.9 12.9 12.9c28.2.1 32.6 40.8 5.5 47.2-.1 4.6 1.6 13.3-5.5 13.3z" opacity="1" data-original="#00beae"></path>
                            <path fill="#ff8048" fill-rule="evenodd" d="M391 27.7c13 0 22.5 20.4 34.1 24.3 11.7 3.9 31-6.9 40.7.2S471 81 478.1 90.7s29.2 12 33.1 23.7-12.3 27.1-12.3 40c0 13 16.2 28.4 12.3 40-3.9 11.7-26 14-33.1 23.7s-2.6 31.4-12.3 38.5-29-3.7-40.7.2C413.5 260.6 404 281 391 281s-22.5-20.4-34.1-24.3c-11.7-3.9-31 6.9-40.7-.2s-5.2-28.8-12.3-38.5-29.2-12-33.1-23.7 12.3-27.1 12.3-40c0-13-16.2-28.4-12.3-40s26-14 33.1-23.7 2.6-31.4 12.3-38.5 29.1 3.7 40.7-.2c11.7-3.9 21.2-24.2 34.1-24.2z" clip-rule="evenodd" opacity="1" data-original="#ff8048"></path>
                            <path fill="#ffe45e" d="M424.1 213.4c-29.3-.5-29.3-44.1 0-44.6 29.3.6 29.3 44.1 0 44.6zm0-33.5c-14.7.1-14.7 22.4 0 22.5 14.7-.1 14.7-22.5 0-22.5zM349.3 201.6c-4.7.2-7.4-6.1-3.9-9.4l83.5-83.5c5-5.1 13 2.8 7.8 7.8L353.2 200c-1.1 1-2.5 1.6-3.9 1.6zM357.9 139.8c-29.3-.5-29.3-44.1 0-44.6 29.4.5 29.3 44.1 0 44.6zm0-33.5c-14.7.1-14.7 22.4 0 22.5 14.8-.1 14.8-22.5 0-22.5z" opacity="1" data-original="#ffe45e"></path>
                        </g>
                    </svg>
                </div>

                <div>
                    <div class="dashboard-card__title"> @lang('Total Ref. Commissions')</div>
                    <div class="dashboard-card__value">{{ showAmount($totalReferralCommission) }}</div>
                </div>
            </div>
        </div>

                <div class="col-xl-4 col-sm-6 d-none">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                        <g>
                            <g data-name="Artboard 23">
                                <path d="M8.477 62a23 23 0 0 1 29.432-35.038l4.933 4.523A22.993 22.993 0 0 1 45.718 36L44 58.965A23.087 23.087 0 0 1 41.523 62Z" style="" fill="#2d9739" data-original="#2d9739"></path>
                                <path d="M45.718 36a22.993 22.993 0 0 0-2.876-4.515l-4.933-4.524-.087-.059a17.692 17.692 0 0 0-19.63 0A23 23 0 0 0 14.477 62h27.046A23.087 23.087 0 0 0 44 58.965Z" style="" fill="#44bc59" data-original="#44bc59"></path>
                                <path d="M44.473 33.761a23.078 23.078 0 0 0-1.631-2.276l-2.207-2.024a14.871 14.871 0 0 1-3.427 1.739l-4.372 1.49A1.248 1.248 0 0 0 32 33.878v6.87a1.222 1.222 0 0 0 1.86 1.067l4.393-2.746a28.882 28.882 0 0 0 6.22-5.308Z" style="" fill="#2d9739" data-original="#2d9739"></path>
                                <rect width="14" height="4" x="18" y="19" rx="2" style="" fill="#ffc239" data-original="#ffc239" class=""></rect>
                                <circle cx="25" cy="43" r="12" style="" fill="#abe4a3" data-original="#abe4a3"></circle>
                                <path d="M25 31a11.927 11.927 0 0 0-6.548 1.952 11.991 11.991 0 0 0 16.6 16.6A11.991 11.991 0 0 0 25 31Z" style="" fill="#cdf8c8" data-original="#cdf8c8" class=""></path>
                                <path d="m20 19-2.78-5c-.685-1.662.249-4 1.78-4h12c1.531 0 2.465 2.338 1.78 4L30 19ZM25 42a2 2 0 1 1 2-2h2a4 4 0 0 0-3-3.858V35h-2v1.142A3.992 3.992 0 0 0 25 44a2 2 0 1 1-2 2h-2a4 4 0 0 0 3 3.858V51h2v-1.142A3.992 3.992 0 0 0 25 42Z" style="" fill="#2d9739" data-original="#2d9739"></path>
                                <path d="M2 61h60v2H2zM37 43h4v16h-4zM44 36h4v23h-4zM51 29h4v30h-4z" style="" fill="#ffc239" data-original="#ffc239" class=""></path>
                                <path d="M36.628 40.084a11.99 11.99 0 0 0-4.5-6.724 1.246 1.246 0 0 0-.125.518v6.87a1.222 1.222 0 0 0 1.86 1.067Z" style="" fill="#abe4a3" data-original="#abe4a3"></path>
                                <path d="M58 23h4v36h-4zM30 47h4v12h-4z" style="" fill="#ffc239" data-original="#ffc239" class=""></path>
                                <path d="M20 19a1.979 1.979 0 0 0-.974.263A2 2 0 0 0 21 21h10a1.979 1.979 0 0 0 .974-.263A2 2 0 0 0 30 19Z" style="" fill="#ffd55d" data-original="#ffd55d"></path>
                                <path d="M32.78 14c.685-1.662-.249-4-1.78-4H20.637a1.49 1.49 0 0 0-1.437 1.981l.008.019 1.481 2.664A4.545 4.545 0 0 0 24.665 17h6.447Z" style="" fill="#44bc59" data-original="#44bc59"></path>
                                <path d="M32 47v9a2 2 0 0 0 2 2V47ZM39 43v13a2 2 0 0 0 2 2V43ZM46 36v20a2 2 0 0 0 2 2V36ZM53 29v27a2 2 0 0 0 2 2V29ZM60 23v33a2 2 0 0 0 2 2V23Z" style="" fill="#ffd55d" data-original="#ffd55d"></path>
                                <path d="M59.505 14.269a1 1 0 0 1-1.236 1.236L53 14c-2.52 8.377-7.434 16.522-14.747 21.092l-4.393 2.746A1.222 1.222 0 0 1 32 36.771V29.9a1.248 1.248 0 0 1 .836-1.186l4.372-1.49C43.058 25.231 48.791 19.184 50 13l-4.459-2.229a1 1 0 0 1-.107-1.727l9.425-6.284a1 1 0 0 1 1.516.557Z" style="" fill="#f3a606" data-original="#f3a606" class=""></path>
                                <path d="m33.86 35.246 4.393-2.746c6.793-4.245 11.516-11.576 14.174-19.312a1.986 1.986 0 0 1 2.429-1.25l3.413.975a.982.982 0 0 0 .814-.124l-2.708-9.472a1 1 0 0 0-1.516-.557l-9.425 6.284a1 1 0 0 0 .107 1.727L50 13c-1.209 6.184-6.942 12.231-12.792 14.225l-4.372 1.49A1.248 1.248 0 0 0 32 29.9v4.28a1.222 1.222 0 0 0 1.86 1.066Z" style="" fill="#ffc239" data-original="#ffc239" class=""></path>
                            </g>
                        </g>
                    </svg>
                </div>

                <div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- dashboard-section end -->
    <div class="pt-40">
        <h5>@lang('Latest Transactions')</h5>
        <div class="list-group">
            @forelse($transactions as $trx)
                <div class="list-group-item d-flex gap-2 flex-wrap justify-content-between">
                    <span>
                        <small class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                            {{ $trx->trx_type }} {{ showAmount($trx->amount, 8, exceptZeros: true, currencyFormat: false) }}
                            {{ __(strtoupper($trx->currency)) }}
                        </small>

                        <br>
                        <small>@php echo $trx->walletType(); @endphp</small>
                    </span>

                    <span class="text-start text-sm-end">
                        @php
                            $details = $trx->details;
                            if (is_string($details) && preg_match('/commission\s+from/i', $details)) {
                                $details = preg_replace('/commission\s+from/i', __('commission from'), $details, 1);
                            } else {
                                $details = __($details);
                            }
                        @endphp
                        <small @if (Str::length($details) > 40) title="{{ $details }}" @endif>{{ strLimit($details, 40) }}</small>
                        <br>
                        <small> {{ diffForHumans($trx->created_at) }}</small>
                    </span>
                </div>
            @empty
                <div class="list-group-item text-center">
                    <p class="p-3">@lang('No transaction yet')</p>
                </div>
            @endforelse
        </div>
    </div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

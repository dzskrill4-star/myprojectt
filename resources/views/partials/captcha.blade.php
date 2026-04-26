@php
    $customCaptcha = loadCustomCaptcha();
    $googleCaptcha = loadReCaptcha();
@endphp

@if ($googleCaptcha || $customCaptcha)
    <div class="col-12 d-flex flex-column gap-3">
        @if ($googleCaptcha)
            <div>
                @php echo $googleCaptcha @endphp
            </div>
        @endif

        @if ($customCaptcha)
            <div class="form-group mb-3">
                <div class="mb-3">
                    @php echo $customCaptcha @endphp
                </div>
                <label class="form-label">@lang('Captcha')</label>
                <input type="text" name="captcha" class="form-control form--control" required>
            </div>
        @endif
    </div>
@endif

@if ($googleCaptcha)
    @push('script')
        <script>
            (function($) {
                "use strict"
                $('.verify-gcaptcha').on('submit', function() {
                    var response = grecaptcha.getResponse();
                    if (response.length == 0) {
                        document.getElementById('g-recaptcha-error').innerHTML = '<span class="text--danger">@lang('Captcha field is required.')</span>';
                        return false;
                    }
                    return true;
                });

                window.verifyCaptcha = () => {
                    document.getElementById('g-recaptcha-error').innerHTML = '';
                }
            })(jQuery);
        </script>
    @endpush
@endif

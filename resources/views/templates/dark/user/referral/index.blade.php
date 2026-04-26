@extends('Template::layouts.master')
@section('content')
<style>
    #ref-username {
        background: #1c1f26 !important; /* خلفية داكنة */
        color: #fff !important;         /* نص أبيض */
        font-weight: 600;
        border: 1px solid #333;
    }
</style>

    <div class="card custom--card">
        <div class="card-body">
            <div class="form-group mb-4">
    <div class="d-flex justify-content-between">
        <span>@lang('Referral Username')</span>
        @if(auth()->user()->referrer)
            <span class="text--info">@lang('You are referred by') {{ auth()->user()->referrer->fullname }}</span>
        @endif
    </div>
    <div class="input-group">
        <input id="ref-username" class="form-control" name="text" type="text"
       value="{{ auth()->user()->username }}" readonly>

        <button type="button" id="copy-username" class="input-group-text btn btn--base"
        onclick="navigator.clipboard.writeText(`{{ auth()->user()->username }}`)">
    <i class="las la-copy"></i>
</button>

    </div>
    <small class="text-muted d-block mt-1">
        @lang('manual.share_username')

    </small>
</div>
            @if ($user->allReferrals->count() > 0 && $maxLevel > 0)
                <label>@lang('My Referrals')</label>
                <div class="treeview-container">
                    <ul class="treeview">
                        <li class="items-expanded"> {{ $user->fullname }} ( {{ $user->username }} )
                            @include('Template::partials.under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true])
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('style')
    <link type="text/css" href="{{ asset('assets/global/css/jquery.treeView.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('assets/global/js/jquery.treeView.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            $('.treeview').treeView();
            $('.copyBoard').click(function() {
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);

                /*For mobile devices*/
                document.execCommand("copy");
                notify('success', "Copied: " + copyText.value);
            });
        })(jQuery);
    </script>
@endpush

@extends('Template::layouts.master')
@section('content')
    <div class="row gy-4">
        @php
            $userBadges = auth()->user()?->badges->pluck('id')->toArray() ?? [];
        @endphp

        @foreach ($badges as $badge)
            <div class="col-sm-6 col-xl-4">
                <div @class(['achievement', 'locked' => !in_array($badge->id, $userBadges)])>
                    <div class="image icon mb-3">
                        <img src="{{ getImage(getFilePath('badge') . '/' . $badge->image, getFileSize('badge')) }}" alt="{{ __($badge->name) }}">
                    </div>

                    <h4 class="mb-2">{{ __($badge->name) }}</h4>
                    <h6 class="mb-3">@lang('Min Earning'): {{ showAmount($badge->earning_amount) }}</h6>

                    @if (in_array($badge->id, $userBadges))
                        <span class="achievement__ribbon success">@lang('Unlocked')</span>
                    @endif

                    <ul class="badge-benefits">
                        <li>@lang('Maintenance Discount'):
                            {{ getBadgeAmount($badge->discount_maintenance_cost) }}
                        </li>
                        <li>@lang('Plan Purchase Discount'):
                            {{ getBadgeAmount($badge->plan_price_discount) }}
                        </li>
                        <li>@lang('Increase Earning Boost'):
                            {{ getBadgeAmount($badge->earning_boost) }}
                        </li>
                        <li>@lang('Enhance Referral Bonus'):
                            {{ getBadgeAmount($badge->referral_bonus_boost) }}
                        </li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@endsection


@push('style')
    <style>
        .achievement {
            background: #d4edda1a;
            padding: 24px 32px;
            border-radius: 10px;
            text-align: center;
            position: relative;
        }

        .achievement.locked {
            background-color: #d4edda1a;
            position: relative;
            cursor: pointer;
        }

        .achievement.locked::after {
            content: '\f023';
            font-family: 'Line Awesome Free';
            font-weight: 900;
            width: 100%;
            height: 100%;
            position: absolute;
            inset: 0;
            background-color: rgb(0 0 0 / 0%);
            border-radius: inherit;
            display: inline-flex;
            align-items: start;
            justify-content: end;
            font-size: 36px;
            color: rgb(119 119 119);
            padding: 21px;
            cursor: initial;
        }

        .icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 10px auto;
        }

        .progress {
            height: 10px;
            background: #ddd;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress span {
            display: block;
            height: 100%;
            background: #28a745;
        }

        .badge-benefits {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .badge-benefits li {
            text-align: center;
        }

        .badge-benefits li:not(:last-child) {
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 16px;
        }

        .badge-benefits li::before {
            content: '';
            width: 0.5em;
            height: 0.5em;
            border-radius: 50%;
            display: none;
            background-color: #4CAF50;
        }

        .achievement__ribbon {
            position: absolute;
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1;
            height: 20px;
            top: 10px;
            right: -10px;
            padding: 0px 12px;
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            border-radius: 30px;
            border-bottom-right-radius: 0;
            z-index: 1;
        }

        .achievement__ribbon::before {
            content: "";
            position: absolute;
            width: 10px;
            height: 20px;
            top: 100%;
            right: 0;
            z-index: 2;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .achievement__ribbon::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 10px;
            top: 100%;
            right: 0;
            z-index: 1;
        }

        .achievement__ribbon.success {
            color: #fff;
            background-color: #22c55e;
        }

        .achievement__ribbon.success::before {
            background-color: #15803d;
        }

        .achievement__ribbon.success::after {
            background-color: #22c55e;
        }
    </style>
@endpush

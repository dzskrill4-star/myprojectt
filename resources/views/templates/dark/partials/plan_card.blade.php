@if(app()->getLocale() == 'ar')
<style>
/* تثبيت العنصر */
.price-item__body ul.text-list li.text-list__item {
    position: relative;
    padding-right: 30px !important;
    padding-left: 0 !important;
}

/* النقطة البيضاء */
.price-item__body ul.text-list li.text-list__item::before {
    left: auto !important;
    right: 0 !important;
    top: 50% !important;
    transform: translate(0, -50%) !important;
}

/* النقطة الخضراء */
.price-item__body ul.text-list li.text-list__item::after {
    left: auto !important;
    right: 0 !important;
    top: 50% !important;
    transform: translate(0, -50%) !important;
    margin: 0 !important;
}
</style>
@endif



<div class="plan-tab">
    @if ($miners?->count() > 1)
        <ul class="nav custom--tab nav-pills mb-3" id="pills-tab" role="tablist">
            @foreach ($miners as $item)
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($loop->first) active @endif" id="tabName{{ $loop->iteration }}" data-bs-toggle="pill" data-bs-target="#pills-{{ $loop->iteration }}" type="button" role="tab" aria-controls="pills-{{ $loop->iteration }}">{{ $item->name }}</button>
                </li>
            @endforeach
        </ul>
    @endif
    <div class="tab-content" id="pills-tabContent">
        @foreach ($miners as $item)
            <div class="tab-pane fade @if ($loop->first) show active @endif" id="pills-{{ $loop->iteration }}" role="tabpanel" aria-labelledby="tabName{{ $loop->iteration }}">
                <div class="row gy-4 justify-content-center">
                    @foreach ($item->activePlans as $plan)
                        <div class="col-xl-4 col-md-6 col-sm-8">
                            <div class="price-item">

                                <div class="price-item__header">
                                    <div class="price-item__thumb">
<img src="{{ asset('assets/images/plans/' . strtolower(str_replace(' ', '_', $plan->title)) . '.png') }}">
                                    </div>
                                    <h5 class="price-item__title">{{ __($plan->title) }}</h5>
                                    <h2 class="price-item__price"> {{ gs('cur_sym') . showAmount($plan->price, currencyFormat: false) }}<span class="price-item__price-month ms-1">@lang('for') {{ $plan->period }} {{ __(Str::plural($plan->periodUnitText, $plan->period)) }}</span> </h2>

                                    <div class="price-item__button">
                                        @guest
                                            <a class="btn btn--base" href="{{ route('user.login') }}">@lang('Buy Now')</a>
                                        @else
                                            <button class="btn btn--base buy-plan" data-id="{{ $plan->id }}" data-title="{{ $plan->title }}" data-price="{{ showAmount($plan->price, currencyFormat: false) }}" type="button">@lang('Buy Now')</button>
                                        @endguest
                                    </div>
                                </div>

                                <div class="price-item__content">
                                    <div class="price-item__body">
                                        <ul class="text-list">
                                            <li class="text-list__item">
    @if(app()->getLocale() == 'ar')
    <div style="display:flex; flex-direction:row-reverse; align-items:center; gap:8px;">
        <span>سرعة التعدين</span>
<span style="direction: rtl; text-align:right; display:inline-block;">
    @if(app()->getLocale() == 'ar')
        {{ getAmount($plan->speed) }} ميغا هاش/ث
    @else
        {{ getAmount($plan->speed) }} {{ $plan->speedUnitText }}
    @endif
</span>
    </div>
@else
    <div style="display:flex; align-items:center; gap:8px;">
        <span>Hashrate</span>
        <span>{{ getAmount($plan->speed) }} {{ $plan->speedUnitText }}</span>
    </div>
@endif

</li>





<li class="text-list__item">
    @if(app()->getLocale() == 'ar')
        <div style="display:flex; flex-direction:row-reverse; align-items:center; gap:8px;">
            <span>العائد اليومي</span>
            <span style="direction:rtl; text-align:right; display:inline-block;">
                {{ showAmount($plan->min_return_per_day, currencyFormat: false) }}
                @if ($plan->max_return_per_day)
                    - {{ showAmount($plan->max_return_per_day, currencyFormat: false) }}
                @endif
                دولار
            </span>
        </div>
    @else
        <div style="display:flex; align-items:center; gap:8px;">
            <span>Return per day</span>
            <span>
                {{ showAmount($plan->min_return_per_day, currencyFormat: false) }}
                @if ($plan->max_return_per_day)
                    - {{ showAmount($plan->max_return_per_day, currencyFormat: false) }}
                @endif
                USDT
            </span>
        </div>
    @endif
</li>


<li class="text-list__item">
    @if(app()->getLocale() == 'ar')
        <div style="display:flex; flex-direction:row-reverse; align-items:center; gap:8px;">
            <span>تكلفة الصيانة اليومية</span>
            <span style="direction:rtl; text-align:right; display:inline-block;">
                {{ getAmount($plan->maintenance_cost) }}%
            </span>
        </div>
    @else
        <div style="display:flex; align-items:center; gap:8px;">
            <span>Maintenance Cost Per Day</span>
            <span>
                {{ getAmount($plan->maintenance_cost) }}%
            </span>
        </div>
    @endif
</li>

                                            @foreach ($plan->features ?? [] as $feature)
    <li class="text-list__item">
        @if(app()->getLocale() == 'ar')
            <div style="display:flex; flex-direction:row-reverse; align-items:center; gap:8px;">
                <span style="direction:rtl; text-align:right;">
                    @lang($feature)
                </span>
            </div>
        @else
            <div style="display:flex; align-items:center; gap:8px;">
                <span>
                    {{ $feature }}
                </span>
            </div>
        @endif
    </li>
@endforeach


                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

@auth
    @include('Template::partials.buy_plan_modal')
@endauth

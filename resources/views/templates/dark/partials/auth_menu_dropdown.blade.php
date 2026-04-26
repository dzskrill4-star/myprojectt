<div class="dropdown">
    <button class="dropdown-btn" type="button" data-bs-toggle="dropdown">
        <svg width="20" height="20" x="0" y="0" viewBox="0 0 45.532 45.532" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
            <g>
                <path d="M22.766.001C10.194.001 0 10.193 0 22.766s10.193 22.765 22.766 22.765c12.574 0 22.766-10.192 22.766-22.765S35.34.001 22.766.001zm0 6.807a7.53 7.53 0 1 1 .001 15.06 7.53 7.53 0 0 1-.001-15.06zm-.005 32.771a16.708 16.708 0 0 1-10.88-4.012 3.209 3.209 0 0 1-1.126-2.439c0-4.217 3.413-7.592 7.631-7.592h8.762c4.219 0 7.619 3.375 7.619 7.592a3.2 3.2 0 0 1-1.125 2.438 16.702 16.702 0 0 1-10.881 4.013z" fill="#fff" opacity="1" data-original="#fff" class=""></path>
            </g>
        </svg>
        <span class="username">{{ strLimit(auth()->user()->fullname, 15) }}</span>
        <span class="collapse-icon"><i class="las la-angle-down"></i></span>
    </button>

    <ul class="dropdown-menu dropdown-menu-end" >
        <li class="dropdown-item">
            <a href="{{ route('user.home') }}" @class(['d-inline-flex gap-2 align-items-center', 'active' => Route::is('user.home')])>
                <span class="menu-icon"><x-icons.dashboard-icon /></span>
                @lang('Dashboard')
            </a>
        </li>

            <li class="dropdown-item"><a href="{{ route('user.referral') }}" @class(['d-inline-flex gap-2 align-items-center', 'active' => Route::is('user.referral')])>
                <i class="la la-users"></i> @lang('My Referrals')</a>
            </li>

        <li class="dropdown-item"><a href="{{ route('user.profile.setting') }}" @class(['d-inline-flex gap-2 align-items-center', 'active' => Route::is('user.profile.setting')])>
            <i class="la la-user"></i> @lang('My Profile')</a>
        </li>

        <li class="dropdown-item"><a href="{{ route('user.change.password') }}" @class(['d-inline-flex gap-2 align-items-center', 'active' => Route::is('user.change.password')])>
            <i class="la la-user-lock"></i> @lang('Change Password')</a>
        </li>

        <li class="dropdown-item">
            <a href="{{ route('user.badge') }}" @class(['d-inline-flex gap-2 align-items-center', 'active' => Route::is('user.badge')]) class="">
                <i class="las la-award"></i> @lang('Achievements')
            </a>
        </li>

        <li class="dropdown-item"><a href="{{ route('user.logout') }}" class="d-inline-flex gap-2 align-items-center">
            <i class="la la-sign-out"></i> @lang('Logout')</a>
        </li>
    </ul>
</div>

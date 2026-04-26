@php
$unreadTicketMessagesCount = \App\Models\SupportMessage::whereNotNull('admin_id')
    ->where('is_read', 0)
    ->whereHas('ticket', function ($q) {
        $q->where('user_id', auth()->id());
    })
    ->count();
@endphp

<aside class="dashboard-sidebar-menu">

    <button class="dashboard-sidebar__close-icon  d-lg-none d-block">
        <i class="las la-times"></i>
    </button>

    <ul class="dashboard-menu">
        <li class="dashboard-menu__item">
            <a href="{{ route('user.home') }}" @class(['dashboard-menu__link', menuActive('user.home')])>
                <span class="dashboard-menu__link-icon"><x-icons.dashboard-icon/></span>
                @lang('Dashboard')
            </a>
        </li>


        <li class="dashboard-menu__item">
            <a href="{{ route('user.plans.purchased') }}" @class(['dashboard-menu__link', menuActive('user.plans.purchased')])>
                <span class="dashboard-menu__link-icon"><x-icons.mining-tracks-icon/></span>
                @lang('Mining Tracks')
            </a>
        </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('user.deposit.index') }}" @class(['dashboard-menu__link', menuActive('user.deposit.index')])>
                <span class="dashboard-menu__link-icon"><x-icons.deposit-money-icon/></span>
                @lang('Deposit Money')
            </a>
        </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('user.withdraw') }}" @class(['dashboard-menu__link', menuActive('user.withdraw')])>
                <span class="dashboard-menu__link-icon"><x-icons.withdraw-money-icon/></span>
                @lang('Withdraw Money')
            </a>
        </li>

        @if(auth()->user()->id == 1)
        <li class="dashboard-menu__item has-dropdown">
            <a href="javascript:void(0)" class="dashboard-menu__link">
                <span class="dashboard-menu__link-icon"><i class="las la-exchange-alt"></i></span>
                <span class="dashboard-menu__link-text">
                    @lang('P2P') 
                    <span class="badge bg-warning text-dark ms-1" style="font-size: 9px; padding: 2px 6px;">@lang('Coming Soon')</span>
                </span>
                <span class="dashboard-menu__link-arrow"><i class="las la-angle-down"></i></span>
            </a>
            <ul class="dashboard-submenu">
                <li class="dashboard-submenu__item">
                    <a href="{{ route('user.p2p.marketplace') }}" @class(['dashboard-submenu__link', menuActive('user.p2p.marketplace')])>
                        <i class="las la-store me-2"></i> @lang('P2P Marketplace')
                    </a>
                </li>
                <li class="dashboard-submenu__item">
                    <a href="{{ route('user.p2p.sellers-buyers') }}" @class(['dashboard-submenu__link', menuActive('user.p2p.sellers-buyers')])>
                        <i class="las la-users me-2"></i> @lang('Sellers & Buyers')
                    </a>
                </li>
                <li class="dashboard-submenu__item">
                    <a href="{{ route('user.p2p.deal-details', 1) }}" @class(['dashboard-submenu__link', menuActive('user.p2p.deal-details')])>
                        <i class="las la-file-alt me-2"></i> @lang('Deal Details') <span class="text-muted small">(@lang('Demo'))</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <li class="text-muted fs--14 mt-2">
            @lang('Report')
        </li>

            <li class="dashboard-menu__item">
                <a href="{{ route('user.referral.log') }}" @class(['dashboard-menu__link', menuActive('user.referral.log')])>
                    <span class="dashboard-menu__link-icon"> <x-icons.referral-commissions-icon/></span>
                    @lang('Ref. Commissions')
                </a>
            </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('user.deposit.history') }}" @class(['dashboard-menu__link', menuActive('user.deposit.history')])>
                <span class="dashboard-menu__link-icon"><x-icons.deposit-history-icon/></span>
                @lang('Deposit History')
            </a>
        </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('user.orders') }}" @class(['dashboard-menu__link', menuActive('user.orders')])>
                <span class="dashboard-menu__link-icon"><x-icons.orders-history-icon/></span>
                @lang('Order History')
            </a>
        </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('user.withdraw.history') }}" @class(['dashboard-menu__link', menuActive('user.withdraw.history')])>
                <span class="dashboard-menu__link-icon"><x-icons.withdrawals-log-icon/></span>
                @lang('Withdrawals Log')
            </a>
        </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('user.transactions') }}" @class(['dashboard-menu__link', menuActive('user.transactions')])>
                <span class="dashboard-menu__link-icon"><x-icons.transactions-icon/></span>
                @lang('Transactions')
            </a>
        </li>


        <li class="text-muted fs--14 mt-2">@lang('Support')</li>

        <li class="dashboard-menu__item">
            <a href="{{ route('ticket.open') }}" @class(['dashboard-menu__link', menuActive('ticket.open')])>
                <span class="dashboard-menu__link-icon"><x-icons.open-support-ticket-icon/></span>
                @lang('Open Support Ticket')
            </a>
        </li>

        <li class="dashboard-menu__item">
            <a href="{{ route('ticket.index') }}" @class([
                'dashboard-menu__link',
                menuActive(['ticket.index', 'ticket.view']),
            ])>
                <span class="dashboard-menu__link-icon"><x-icons.support-tickets-icon/></span>
                @lang('Support Tickets')
                @if(isset($unreadTicketMessagesCount) && $unreadTicketMessagesCount > 0)
                    <span class="ticket-badge">{{ $unreadTicketMessagesCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</aside>

@push('style')
<style>
/* P2P Submenu Styles */
.dashboard-menu__item.has-dropdown {
    position: relative;
}

.dashboard-menu__item.has-dropdown .dashboard-menu__link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
}

.dashboard-menu__link-text {
    flex: 1;
    display: flex;
    align-items: center;
}

.dashboard-menu__link-arrow {
    transition: transform 0.3s ease;
    margin-left: auto;
    font-size: 18px;
}

.dashboard-menu__item.has-dropdown.active .dashboard-menu__link-arrow {
    transform: rotate(180deg);
}

.dashboard-submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    list-style: none;
    padding: 0;
    margin: 0;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
}

.dashboard-menu__item.has-dropdown.active .dashboard-submenu {
    max-height: 300px;
    padding: 8px 0;
    margin-top: 8px;
}

.dashboard-submenu__item {
    list-style: none;
}

.dashboard-submenu__link {
    display: block;
    padding: 10px 20px 10px 50px;
    color: inherit;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 6px;
    margin: 2px 10px;
    font-size: 14px;
}

.dashboard-submenu__link:hover {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    transform: translateX(-3px);
}

.dashboard-submenu__link.active,
.dashboard-submenu__link:focus {
    background: rgba(13, 110, 253, 0.15);
    color: #0d6efd;
    font-weight: 500;
}

.badge {
    font-weight: 600;
    border-radius: 4px;
}

/* Support Tickets Unread Badge */
.ticket-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #dc3545;
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 10px;
    margin-left: auto;
    line-height: 1;
}

.dashboard-menu__link {
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>
@endpush

@push('script')
    <script>
        'use strict';
        (function ($) {
            // P2P Dropdown Toggle
            $('.dashboard-menu__item.has-dropdown > .dashboard-menu__link').on('click', function(e) {
                e.preventDefault();
                const parent = $(this).closest('.has-dropdown');
                
                // Close other dropdowns
                $('.dashboard-menu__item.has-dropdown').not(parent).removeClass('active');
                
                // Toggle current dropdown
                parent.toggleClass('active');
            });

            // Keep dropdown open if current page is one of submenu items
            const currentPath = window.location.pathname;
            if (currentPath.includes('/user/p2p/')) {
                $('.dashboard-menu__item.has-dropdown').addClass('active');
            }

            // P2P Restricted Access Handler
            $(document).on('click', '.js-p2p-restricted', function (e) {
                e.preventDefault();
                
                // Detect current language from HTML lang attribute or body class
                const currentLang = $('html').attr('lang') || document.documentElement.lang || 'ar';
                const isArabic = currentLang === 'ar' || currentLang.startsWith('ar');
                
                const messageAr = $(this).data('message-ar') || 'هذه المنطقة قيد التطوير';
                const messageEn = $(this).data('message-en') || 'This area is under development';
                const message = isArabic ? messageAr : messageEn;

                if (typeof notify === 'function') {
                    notify('info', message);
                    return;
                }

                alert(message);
            });

            $(document).on('click', '.js-p2p-coming-soon', function (e) {
                e.preventDefault();
                const message = $(this).data('message') || 'هذه المنطقة قيد التشغيل حالياً';

                if (typeof notify === 'function') {
                    notify('info', message);
                    return;
                }

                alert(message);
            });
        })(jQuery);
    </script>
@endpush

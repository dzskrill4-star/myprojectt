@extends('templates.dark.layouts.app')

@section('panel')
<div class="container-lg my-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold mb-2">P2P Marketplace</h1>
            <p class="text-muted fs-5">تبادل آمن وسهل بين المستخدمين</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createOfferModal">
                <i class="fas fa-plus"></i> إنشاء عرض جديد
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="buy-tab" data-bs-toggle="tab" data-bs-target="#buy-content" type="button" role="tab">
                <i class="fas fa-shopping-cart"></i> شراء
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sell-tab" data-bs-toggle="tab" data-bs-target="#sell-content" type="button" role="tab">
                <i class="fas fa-store"></i> بيع
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="my-offers-tab" data-bs-toggle="tab" data-bs-target="#my-offers-content" type="button" role="tab">
                <i class="fas fa-list"></i> عروضي
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content" type="button" role="tab">
                <i class="fas fa-history"></i> السجل
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Buy Section -->
        <div class="tab-pane fade show active" id="buy-content" role="tabpanel">
            @include('templates.dark.user.p2p.buy-section')
        </div>

        <!-- Sell Section -->
        <div class="tab-pane fade" id="sell-content" role="tabpanel">
            @include('templates.dark.user.p2p.sell-section')
        </div>

        <!-- My Offers Section -->
        <div class="tab-pane fade" id="my-offers-content" role="tabpanel">
            @include('templates.dark.user.p2p.my-offers-section')
        </div>

        <!-- History Section -->
        <div class="tab-pane fade" id="history-content" role="tabpanel">
            @include('templates.dark.user.p2p.history-section')
        </div>
    </div>
</div>

<!-- Create Offer Modal -->
@include('templates.dark.user.p2p.create-offer-modal')

<!-- Order Details Modal -->
@include('templates.dark.user.p2p.order-details-modal')

@endsection

@push('script-lib')
<link rel="stylesheet" href="{{ asset('assets/global/css/p2p-marketplace.css') }}">
@endpush

@push('script')
<script src="{{ asset('assets/global/js/p2p-marketplace.js') }}"></script>
@endpush

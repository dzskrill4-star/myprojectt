@extends('templates.dark.layouts.app')

@section('panel')
<div class="p2p-container mt-4">
    <!-- Breadcrumb -->
    <nav class="p2p-breadcrumb mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb bg-light p-3 rounded">
            <li class="breadcrumb-item"><a href="{{ route('user.p2p.marketplace') }}">P2P Marketplace</a></li>
            <li class="breadcrumb-item active">تفاصيل الصفقة</li>
        </ol>
    </nav>

    <!-- Main Content -->
    <div class="row">
        <!-- Left: Deal Details -->
        <div class="col-lg-8 mb-4">
            <!-- Deal Header -->
            <div class="p2p-deal-header card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img id="dealAvatar" src="" alt="البائع" class="rounded-circle" style="width: 70px; height: 70px; border: 3px solid #0d6efd;">
                        </div>
                        <div class="col flex-grow-1">
                            <h4 id="dealTitle" class="mb-0">تفاصيل الصفقة</h4>
                            <p id="dealSeller" class="text-muted mb-0">البائع</p>
                        </div>
                        <div class="col-auto">
                            <span id="dealStatus" class="badge bg-success">نشط</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset & Price Info -->
            <div class="p2p-deal-section card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom p-4">
                    <h5 class="mb-0"><i class="fas fa-coins"></i> معلومات الأصل</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">نوع الأصل</label>
                            <h6 id="assetType" class="mb-0">USDT</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">الكمية المتاحة</label>
                            <h6 id="assetAmount" class="mb-0">5,000 USDT</h6>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">السعر الموحد</label>
                            <h5 id="unitPrice" class="mb-0 text-primary">1,000 ر.س</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">السعر الإجمالي</label>
                            <h5 id="totalPrice" class="mb-0 text-success">5,000,000 ر.س</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="p2p-deal-section card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom p-4">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> طرق الدفع المقبولة</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row" id="paymentMethods">
                        <div class="col-md-6 mb-3">
                            <div class="p2p-payment-method">
                                <i class="fas fa-university"></i>
                                <span>تحويل بنكي</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p2p-payment-method">
                                <i class="fas fa-mobile"></i>
                                <span>محفظة USDT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trading Terms -->
            <div class="p2p-deal-section card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom p-4">
                    <h5 class="mb-0"><i class="fas fa-handshake"></i> شروط التداول</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الحد الأدنى للشراء</label>
                            <p class="mb-0 font-weight-bold" id="minAmount">1,000 ر.س</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الحد الأقصى للشراء</label>
                            <p class="mb-0 font-weight-bold" id="maxAmount">5,000,000 ر.س</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">وقت التداول</label>
                            <p class="mb-0 font-weight-bold" id="tradingTime">12:00 - 00:00 (توقيت مكة)</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">وقت التسليم</label>
                            <p class="mb-0 font-weight-bold" id="deliveryTime">5-10 دقائق</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seller Notes -->
            <div class="p2p-deal-section card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom p-4">
                    <h5 class="mb-0"><i class="fas fa-sticky-note"></i> ملاحظات البائع</h5>
                </div>
                <div class="card-body p-4">
                    <p id="sellerNotes" class="mb-0">
                        يرجى التأكد من إدخال البيانات الصحيحة. سيتم التحقق من جميع المعاملات. شكراً على ثقتك!
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Seller Info & Action -->
        <div class="col-lg-4">
            <!-- Seller Card -->
            <div class="p2p-deal-section card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4 text-center">
                    <img id="sellerAvatar" src="" alt="البائع" class="rounded-circle mb-3" style="width: 100px; height: 100px; border: 3px solid #0d6efd;">
                    
                    <h5 id="sellerName" class="mb-1">اسم البائع</h5>
                    <p class="text-muted small mb-3">معدّل الاستجابة: 98%</p>
                    
                    <div class="p2p-seller-stats mb-4">
                        <div class="stat-item">
                            <div class="stat-value">4.8</div>
                            <div class="stat-label">التقييم</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">1,234</div>
                            <div class="stat-label">عملية</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">99%</div>
                            <div class="stat-label">الإتمام</div>
                        </div>
                    </div>

                    <div class="p2p-seller-badges mb-4">
                        <span class="badge bg-success mb-2"><i class="fas fa-check-circle"></i> موثق</span>
                        <span class="badge bg-info mb-2"><i class="fas fa-award"></i> بائع محترف</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg" disabled onclick="buyNow()">
                            <i class="fas fa-shopping-cart"></i> شراء الآن
                        </button>
                        <button class="btn btn-outline-secondary" disabled onclick="contactSeller()">
                            <i class="fas fa-message"></i> اتصل بالبائع
                        </button>
                        <button class="btn btn-light" onclick="reportDeal()">
                            <i class="fas fa-flag"></i> إبلاغ عن المشكلة
                        </button>
                    </div>

                    <!-- Info Message -->
                    <div class="alert alert-warning mt-3 mb-0" role="alert">
                        <small>
                            <i class="fas fa-info-circle"></i> هذه صفحة عرض توضيحي. جميع الأزرار معطلة.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="p2p-deal-section card border-0 shadow-sm mt-4">
                <div class="card-header bg-light border-bottom p-4">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> التقييمات (15)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="p2p-review mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>أحمد محمد</strong>
                            <span class="text-warning"><i class="fas fa-star"></i> 5.0</span>
                        </div>
                        <small class="text-muted d-block mb-2">قبل يومين</small>
                        <p class="mb-0 small">تاجر ممتاز وموثوق جداً. سريع جداً في التسليم!</p>
                    </div>

                    <hr>

                    <div class="p2p-review mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>فاطمة علي</strong>
                            <span class="text-warning"><i class="fas fa-star"></i> 4.5</span>
                        </div>
                        <small class="text-muted d-block mb-2">قبل أسبوع</small>
                        <p class="mb-0 small">جيد جداً. سريع والسعر عادل.</p>
                    </div>

                    <hr>

                    <button class="btn btn-sm btn-outline-primary w-100" disabled>
                        <i class="fas fa-eye"></i> عرض جميع التقييمات
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script-lib')
<link rel="stylesheet" href="{{ asset('assets/global/css/p2p-marketplace.css') }}">
<style>
.p2p-breadcrumb .breadcrumb-item a {
    color: #0d6efd;
    text-decoration: none;
}

.p2p-breadcrumb .breadcrumb-item a:hover {
    text-decoration: underline;
}

.p2p-deal-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.p2p-deal-section {
    border-radius: 12px;
    overflow: hidden;
}

.p2p-deal-section .card-header {
    background-color: #f8f9fa;
    padding: 20px !important;
}

.p2p-deal-section .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.p2p-deal-section .card-body {
    padding: 25px !important;
}

.p2p-payment-method {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #333;
    font-weight: 500;
}

.p2p-payment-method:hover {
    border-color: #0d6efd;
    background: #e3f2fd;
}

.p2p-payment-method i {
    font-size: 20px;
    color: #0d6efd;
}

.p2p-seller-stats {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #0d6efd;
    display: block;
}

.stat-label {
    font-size: 12px;
    color: #999;
    margin-top: 5px;
}

.p2p-seller-badges {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
}

.p2p-seller-badges .badge {
    padding: 8px 12px;
    font-weight: 500;
}

.p2p-review {
    padding: 0;
}

.p2p-review strong {
    color: #333;
}

.p2p-review .text-warning {
    color: #ffc107 !important;
}

/* Disabled Button Styles */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed !important;
}

.btn-lg {
    padding: 12px 20px;
    font-weight: 600;
    border-radius: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .row {
        margin: 0;
    }

    .col-lg-8, .col-lg-4 {
        margin-bottom: 20px;
    }

    .p2p-deal-section.sticky-top {
        position: static !important;
    }

    .p2p-seller-stats {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .p2p-deal-section .card-body {
        padding: 15px !important;
    }

    .p2p-deal-section .card-header {
        padding: 15px !important;
    }
}

@media (max-width: 480px) {
    .p2p-deal-section .card-body {
        padding: 12px !important;
    }

    .p2p-seller-stats {
        padding: 15px;
    }

    .p2p-payment-method {
        padding: 12px;
        gap: 10px;
    }

    .btn-lg {
        padding: 10px 15px;
        font-size: 14px;
    }
}
</style>
@endpush

@push('script')
<script src="{{ asset('assets/global/js/p2p-marketplace.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDealDetails();
});

function loadDealDetails() {
    // Get deal ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const dealId = urlParams.get('id') || 1;

    const deal = p2pMarketplaceData.sellOffers[dealId - 1];
    if (!deal) return;

    // Update Deal Header
    document.getElementById('dealTitle').textContent = `شراء ${deal.asset}`;
    document.getElementById('dealSeller').textContent = `من: ${deal.seller}`;
    document.getElementById('dealAvatar').src = deal.avatar;
    document.getElementById('dealStatus').textContent = deal.status === 'active' ? 'نشط' : 'معطل';
    document.getElementById('dealStatus').className = `badge ${deal.status === 'active' ? 'bg-success' : 'bg-danger'}`;

    // Asset Info
    document.getElementById('assetType').textContent = deal.asset;
    document.getElementById('assetAmount').textContent = `${deal.amount} ${deal.asset}`;
    document.getElementById('unitPrice').textContent = formatCurrency(deal.price);
    document.getElementById('totalPrice').textContent = formatCurrency(deal.price * deal.amount);

    // Seller Info
    document.getElementById('sellerName').textContent = deal.seller;
    document.getElementById('sellerAvatar').src = deal.avatar;

    // Trading Terms
    document.getElementById('minAmount').textContent = formatCurrency(deal.minOrder);
    document.getElementById('maxAmount').textContent = formatCurrency(deal.maxOrder);
    document.getElementById('tradingTime').textContent = deal.tradingTime;
    document.getElementById('deliveryTime').textContent = deal.deliveryTime;

    // Payment Methods
    const paymentContainer = document.getElementById('paymentMethods');
    paymentContainer.innerHTML = deal.paymentMethods.map(method => `
        <div class="col-md-6 mb-3">
            <div class="p2p-payment-method">
                <i class="fas fa-check-circle"></i>
                <span>${method}</span>
            </div>
        </div>
    `).join('');
}

function buyNow() {
    showDisabledNotification('لا يمكن إتمام عملية الشراء. هذه صفحة عرض توضيحي.');
}

function contactSeller() {
    showDisabledNotification('لا يمكن الاتصال بالبائع. هذه صفحة عرض توضيحي.');
}

function reportDeal() {
    showDisabledNotification('لا يمكن الإبلاغ عن المشكلة. هذه صفحة عرض توضيحي.');
}
</script>
@endpush

<div class="row g-4" id="my-offers-container">
    <!-- My Offers will be loaded here dynamically -->
</div>

<!-- No Offers Message -->
<div class="alert alert-info d-none" id="no-my-offers">
    <i class="fas fa-info-circle"></i> لم تقم بإنشاء أي عروض بعد
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadMyOffers();
    });

    function loadMyOffers() {
        const myOffers = p2pMarketplaceData.myOffers;
        const container = document.getElementById('my-offers-container');
        
        if (myOffers.length === 0) {
            document.getElementById('no-my-offers').classList.remove('d-none');
            return;
        }

        container.innerHTML = myOffers.map(offer => `
            <div class="col-lg-6">
                <div class="card p2p-my-offer-card border-0 shadow-sm">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">عرض ${offer.type === 'sell' ? 'بيع' : 'شراء'}</h5>
                                <small class="text-muted">#${offer.id}</small>
                            </div>
                            <span class="badge bg-${offer.status === 'active' ? 'success' : offer.status === 'paused' ? 'warning' : 'secondary'}">${getStatusText(offer.status)}</span>
                        </div>

                        <!-- Details -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">السعر</small>
                                <strong>${offer.price} ${offer.currency}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">المبلغ</small>
                                <strong>${offer.amount} ${offer.assetType}</strong>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="row mb-3">
                            <div class="col-4">
                                <small class="text-muted d-block">عدد التداولات</small>
                                <strong>${offer.trades}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">المعدل</small>
                                <strong>${offer.completionRate}%</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">الوقت</small>
                                <strong>${offer.tradingTime}م</strong>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-edit"></i> تحرير
                            </button>
                            <button class="btn btn-sm btn-outline-danger" disabled>
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function getStatusText(status) {
        const statusMap = {
            'active': 'نشط',
            'paused': 'موقوف',
            'completed': 'مكتمل'
        };
        return statusMap[status] || status;
    }
</script>

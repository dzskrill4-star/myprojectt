<div class="row g-4" id="buy-offers-container">
    <!-- Offers will be loaded here dynamically -->
</div>

<!-- No Offers Message -->
<div class="alert alert-info d-none" id="no-buy-offers">
    <i class="fas fa-info-circle"></i> لا توجد عروض بيع حالياً
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadBuyOffers();
    });

    function loadBuyOffers() {
        const offers = p2pMarketplaceData.sellOffers;
        const container = document.getElementById('buy-offers-container');
        
        if (offers.length === 0) {
            document.getElementById('no-buy-offers').classList.remove('d-none');
            return;
        }

        container.innerHTML = offers.map(offer => `
            <div class="col-lg-6 col-xl-4">
                <div class="card p2p-offer-card h-100 border-0 shadow-sm">
                    <!-- Seller Info -->
                    <div class="d-flex align-items-center mb-3">
                        <img src="${offer.seller.avatar}" alt="${offer.seller.name}" class="rounded-circle me-3" width="45">
                        <div>
                            <h6 class="mb-0"><strong>${offer.seller.name}</strong></h6>
                            <small class="text-muted">
                                <i class="fas fa-star text-warning"></i> ${offer.seller.rating} 
                                (${offer.seller.trades} تداول)
                            </small>
                        </div>
                        <span class="badge bg-success ms-auto">${offer.seller.verified ? 'موثق' : 'غير موثق'}</span>
                    </div>

                    <!-- Offer Details -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">السعر</span>
                            <span class="fw-bold text-dark">${offer.price} ${offer.currency}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">المبلغ المتاح</span>
                            <span class="fw-bold text-dark">${offer.amount} ${offer.assetType}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">الحد الأدنى</span>
                            <span class="text-dark">${offer.minAmount} ${offer.assetType}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">الحد الأقصى</span>
                            <span class="text-dark">${offer.maxAmount} ${offer.assetType}</span>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">طرق الدفع:</small>
                        <div class="d-flex flex-wrap gap-2">
                            ${offer.paymentMethods.map(method => `
                                <span class="badge bg-light text-dark">${method}</span>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Trading Time -->
                    <div class="mb-3">
                        <small class="text-muted">وقت التداول: ${offer.tradingTime} دقيقة</small>
                    </div>

                    <!-- Action Button -->
                    <button class="btn btn-primary w-100 buy-offer-btn" data-offer-id="${offer.id}" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">
                        شراء الآن
                    </button>
                </div>
            </div>
        `).join('');

        // Add click handlers
        document.querySelectorAll('.buy-offer-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                showOrderDetailsModal(this.dataset.offerId);
            });
        });
    }
</script>

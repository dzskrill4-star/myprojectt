<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">تفاصيل الطلب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-lock"></i> هذه الميزة في مرحلة تجريبية - التداول غير مفعّل حالياً
                </div>

                <div id="orderDetailsContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" disabled id="proceedBtn">
                    المتابعة
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showOrderDetailsModal(offerId) {
        const allOffers = [
            ...p2pMarketplaceData.sellOffers,
            ...p2pMarketplaceData.buyOffers
        ];
        
        const offer = allOffers.find(o => o.id === offerId);
        
        if (!offer) return;

        const content = `
            <div class="p2p-order-details">
                <!-- Partner Info -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="${offer.type === 'buy' ? offer.seller.avatar : offer.buyer.avatar}" 
                                 class="rounded-circle me-3" width="60">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><strong>${offer.type === 'buy' ? offer.seller.name : offer.buyer.name}</strong></h6>
                                <small class="text-muted d-block">
                                    <i class="fas fa-star text-warning"></i> 
                                    ${offer.type === 'buy' ? offer.seller.rating : offer.buyer.rating} 
                                    (${offer.type === 'buy' ? offer.seller.trades : offer.buyer.trades} تداول)
                                </small>
                            </div>
                            <span class="badge bg-success">${offer.type === 'buy' ? offer.seller.verified : offer.buyer.verified ? 'موثق' : 'غير موثق'}</span>
                        </div>
                    </div>
                </div>

                <!-- Offer Details -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">تفاصيل العرض</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">السعر</small>
                                <strong class="fs-5">${offer.price} ${offer.currency}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">المبلغ المتاح</small>
                                <strong class="fs-5">${offer.amount} ${offer.assetType}</strong>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">الحد الأدنى للطلب</small>
                                <strong>${offer.minAmount} ${offer.assetType}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">الحد الأقصى للطلب</small>
                                <strong>${offer.maxAmount} ${offer.assetType}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">طرق الدفع المقبولة</h6>
                        <div class="d-flex flex-wrap gap-2">
                            ${offer.paymentMethods.map(method => `
                                <span class="badge bg-primary">${method}</span>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <!-- Amount Input (Disabled) -->
                <div class="mb-4">
                    <label for="orderAmount" class="form-label fw-bold">كمية الشراء</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="orderAmount" 
                               placeholder="أدخل الكمية" 
                               min="${offer.minAmount}" 
                               max="${offer.maxAmount}"
                               disabled>
                        <span class="input-group-text">${offer.assetType}</span>
                    </div>
                    <small class="text-muted d-block mt-2">
                        من ${offer.minAmount} إلى ${offer.maxAmount} ${offer.assetType}
                    </small>
                </div>

                <!-- Trading Info -->
                <div class="alert alert-warning">
                    <i class="fas fa-hourglass-half"></i> 
                    <strong>وقت التداول:</strong> ${offer.tradingTime} دقيقة للإتمام
                </div>

                <!-- Warning -->
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>تنبيه:</strong> هذا الطلب تجريبي فقط - لا يمكن إتمام التداول حالياً
                </div>
            </div>
        `;

        document.getElementById('orderDetailsContent').innerHTML = content;
    }
</script>

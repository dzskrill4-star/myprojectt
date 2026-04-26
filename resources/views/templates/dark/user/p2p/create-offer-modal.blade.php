<div class="modal fade" id="createOfferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">إنشاء عرض جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i> هذه الميزة قريباً - جاري تفعيلها
                </div>

                <form id="createOfferForm">
                    <!-- Offer Type Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">نوع العرض</label>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offerType" id="sellType" value="sell" checked disabled>
                                    <label class="form-check-label" for="sellType">
                                        <strong>بيع</strong>
                                        <small class="d-block text-muted">بيع الأصول الخاصة بك</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offerType" id="buyType" value="buy" disabled>
                                    <label class="form-check-label" for="buyType">
                                        <strong>شراء</strong>
                                        <small class="d-block text-muted">شراء الأصول من الآخرين</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Type -->
                    <div class="mb-4">
                        <label for="assetType" class="form-label fw-bold">نوع الأصل</label>
                        <select class="form-select" id="assetType" disabled>
                            <option selected>اختر نوع الأصل</option>
                            <option value="USDT">USDT</option>
                            <option value="BTC">BTC</option>
                            <option value="ETH">ETH</option>
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <label for="price" class="form-label fw-bold">السعر</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="price" placeholder="أدخل السعر" disabled>
                            <select class="form-select" style="max-width: 150px;" disabled>
                                <option selected>USD</option>
                                <option>SAR</option>
                                <option>AED</option>
                            </select>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="minAmount" class="form-label fw-bold">الحد الأدنى</label>
                            <input type="number" class="form-control" id="minAmount" placeholder="0.00" disabled>
                        </div>
                        <div class="col-6">
                            <label for="maxAmount" class="form-label fw-bold">الحد الأقصى</label>
                            <input type="number" class="form-control" id="maxAmount" placeholder="0.00" disabled>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">طرق الدفع</label>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input payment-method" type="checkbox" id="bankTransfer" value="تحويل بنكي" disabled>
                                    <label class="form-check-label" for="bankTransfer">تحويل بنكي</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input payment-method" type="checkbox" id="walletTransfer" value="محفظة رقمية" disabled>
                                    <label class="form-check-label" for="walletTransfer">محفظة رقمية</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input payment-method" type="checkbox" id="paymentGateway" value="بوابة دفع" disabled>
                                    <label class="form-check-label" for="paymentGateway">بوابة دفع</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trading Time -->
                    <div class="mb-4">
                        <label for="tradingTime" class="form-label fw-bold">وقت التداول (بالدقائق)</label>
                        <input type="number" class="form-control" id="tradingTime" placeholder="30" disabled>
                    </div>

                    <!-- Terms -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" disabled>
                            <label class="form-check-label" for="agreeTerms">
                                أوافق على شروط التداول P2P
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100" disabled>
                        إنشاء العرض
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

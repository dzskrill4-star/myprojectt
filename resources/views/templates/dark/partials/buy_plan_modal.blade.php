
<!-- Modal -->
<div class="modal fade" id="buyPlanModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content section-bg">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Buy Mining Plan')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="modal-body">
                <form action="{{ route('user.plan.order') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input name="plan_id" type="hidden">
                    <div class="row gy-4">
                        <div class="text-center">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span>
                                        <strong>@lang('Title')</strong>
                                    </span>
                                    <span class="plan-title"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span>
                                        <strong>@lang('Price')</strong>
                                    </span>
                                    <div>
                                        <span class="plan-price"></span> <span>{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-sm-12">
                            <label for="paymentMethod">@lang('Payment System')</label>
                            <input type="hidden" name="payment_method" id="paymentMethod" required>
                            
                            <div class="payment-methods-wrapper">
                                <!-- Direct Payment Button (USDT) - First Option -->
                                <div class="payment-method-card active" 
                                     data-value="{{ Status::GATEWAY }}" 
                                     data-disabled="false">
                                    <div class="payment-method-content">
                                        <div class="payment-method-icon">
                                            <i class="las la-credit-card"></i>
                                        </div>
                                        <div class="payment-method-info">
                                            <h6 class="payment-method-title">@lang('Pay with USDT')</h6>
                                            <p class="payment-method-balance">@lang('Pay with USDT')</p>
                                        </div>
                                        <div class="payment-method-check">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Earning Wallet Button -->
                                <div class="payment-method-card {{ auth()->user()->profit_wallet < 0 ? 'disabled' : '' }}" 
                                     data-value="{{ Status::PROFIT }}" 
                                     data-disabled="{{ auth()->user()->profit_wallet < 0 ? 'true' : 'false' }}">
                                    <div class="payment-method-content">
                                        <div class="payment-method-icon">
                                            <i class="las la-wallet"></i>
                                        </div>
                                        <div class="payment-method-info">
                                            <h6 class="payment-method-title">@lang('Earning Wallet')</h6>
                                            <p class="payment-method-balance">{{ showAmount(auth()->user()->profit_wallet) }}</p>
                                        </div>
                                        <div class="payment-method-check">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deposit Wallet Button -->
                                <div class="payment-method-card {{ auth()->user()->balance < 0 ? 'disabled' : '' }}" 
                                     data-value="{{ Status::BALANCE }}" 
                                     data-disabled="{{ auth()->user()->balance < 0 ? 'true' : 'false' }}">
                                    <div class="payment-method-content">
                                        <div class="payment-method-icon">
                                            <i class="las la-university"></i>
                                        </div>
                                        <div class="payment-method-info">
                                            <h6 class="payment-method-title">@lang('Deposit Wallet')</h6>
                                            <p class="payment-method-balance">{{ showAmount(auth()->user()->balance) }}</p>
                                        </div>
                                        <div class="payment-method-check">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <button class="btn btn--base w-100" type="submit">@lang('Buy Now')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        'use strict';
        (function($) {
            $(document).on('click', '.buy-plan', function() {
                var modal = $('#buyPlanModal');
                modal.find('input[name=plan_id]').val($(this).data('id'));
                modal.find('.plan-title').text($(this).data('title'));
                modal.find('.plan-price').text($(this).data('price'));
                
                // Select USDT (Pay with USDT) by default
                modal.find('.payment-method-card').removeClass('active');
                var usdtCard = modal.find('.payment-method-card[data-value="{{ Status::GATEWAY }}"]');
                usdtCard.addClass('active');
                modal.find('#paymentMethod').val(usdtCard.data('value'));
                
                modal.modal('show');
            });

            // Handle payment method card click
            $(document).on('click', '.payment-method-card', function() {
                if ($(this).data('disabled') === 'true' || $(this).hasClass('disabled')) {
                    return false;
                }
                
                // Remove active class from all cards
                $('.payment-method-card').removeClass('active');
                
                // Add active class to clicked card
                $(this).addClass('active');
                
                // Set the hidden input value
                $('#paymentMethod').val($(this).data('value'));
            });
        })(jQuery);
    </script>
@endpush

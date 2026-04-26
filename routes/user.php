<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', function (Request $request) {
            return view('shared.csrf_handoff', [
                'action' => $request->url(),
                'returnUrl' => url()->previous(),
            ]);
        })->middleware('auth')->withoutMiddleware('guest')->name('logout');
        Route::post('logout', 'logout')->middleware('auth')->withoutMiddleware('guest');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', function (Request $request, string $type) {
            return view('shared.csrf_handoff', [
                'action' => $request->url(),
                'returnUrl' => url()->previous(),
            ]);
        })->name('send.verify.code');
        Route::post('resend-verify/{type}', 'sendVerifyCode');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('orders', 'orders')->name('orders');

                //wallet
                Route::get('wallets', 'wallets')->name('wallets');
                Route::post('wallet/transfer/{id}', 'transferToProfitWallet')->name('wallet.transfer');

                // referral
                Route::get('my-referrals', 'referral')->name('referral');
                Route::get('referral-bonus-logs', 'referralLog')->name('referral.log');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('my-profile', 'profile')->name('profile.setting');
                Route::post('my-profile', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            //Buy Plan
            Route::controller('OrderPlanController')->group(function () {
                Route::post('plan/order', 'orderPlan')->name('plan.order');
                Route::get('mining-tracks', 'miningTracks')->name('plans.purchased');
            });

            //Badge
            Route::controller('BadgeController')->group(function () {
                Route::get('achievements', 'badge')->name('badge');
            });

            // P2P Marketplace
            Route::controller('P2PController')->prefix('p2p')->name('p2p.')->group(function () {
                Route::get('coming-soon', 'comingSoon')->name('coming-soon');

                Route::middleware('p2p.access')->group(function () {
                    Route::get('marketplace', 'marketplace')->name('marketplace');
                    Route::get('buy', 'buyOrders')->name('buy');
                    Route::get('sell', 'sellOrders')->name('sell');
                    Route::get('sellers-buyers', 'sellersBuyers')->name('sellers-buyers');
                    Route::get('deal/{id}', 'dealDetails')->name('deal-details');
                });
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('my-withdrawals', 'withdrawLog')->name('.history');
            });
        });

        // Payment
        Route::controller('Gateway\PaymentController')->group(function () {

            Route::prefix('deposit')->name('deposit.')->group(function () {
                Route::get('/', 'deposit')->name('index');
                Route::post('insert', 'submitDeposit')->name('insert');
                Route::get('confirm', 'depositConfirm')->name('confirm');
                Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
                Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            });

            Route::prefix('payment')->group(function () {
                Route::any('/payment/{id?}', 'payment')->name('payment');
                Route::post('submit-payment/{id}', 'submitPayment')->name('payment.submit');
            });
        });
    });
});

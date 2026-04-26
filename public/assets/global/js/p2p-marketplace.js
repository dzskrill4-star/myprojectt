// P2P Marketplace Dummy Data
const p2pMarketplaceData = {
    // Sell Offers (عروض البيع - ما يبيعه الآخرون)
    sellOffers: [
        {
            id: 'SO-001',
            type: 'sell',
            seller: {
                id: 1,
                name: 'أحمد علي',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ahmed',
                rating: 4.8,
                trades: 156,
                verified: true
            },
            assetType: 'USDT',
            amount: 5000,
            price: 3.75,
            currency: 'SAR',
            minAmount: 100,
            maxAmount: 5000,
            paymentMethods: ['تحويل بنكي', 'محفظة رقمية'],
            tradingTime: 15,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'SO-002',
            type: 'sell',
            seller: {
                id: 2,
                name: 'فاطمة محمد',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Fatima',
                rating: 4.9,
                trades: 234,
                verified: true
            },
            assetType: 'BTC',
            amount: 0.5,
            price: 150000,
            currency: 'SAR',
            minAmount: 0.01,
            maxAmount: 0.5,
            paymentMethods: ['تحويل بنكي', 'بوابة دفع'],
            tradingTime: 30,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'SO-003',
            type: 'sell',
            seller: {
                id: 3,
                name: 'سارة خالد',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah',
                rating: 4.7,
                trades: 89,
                verified: true
            },
            assetType: 'ETH',
            amount: 2.5,
            price: 12500,
            currency: 'SAR',
            minAmount: 0.1,
            maxAmount: 2.5,
            paymentMethods: ['محفظة رقمية'],
            tradingTime: 20,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'SO-004',
            type: 'sell',
            seller: {
                id: 4,
                name: 'محمود حسن',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Mahmoud',
                rating: 4.6,
                trades: 112,
                verified: true
            },
            assetType: 'USDT',
            amount: 10000,
            price: 3.77,
            currency: 'SAR',
            minAmount: 500,
            maxAmount: 10000,
            paymentMethods: ['تحويل بنكي', 'محفظة رقمية', 'بوابة دفع'],
            tradingTime: 25,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'SO-005',
            type: 'sell',
            seller: {
                id: 5,
                name: 'ليلى إبراهيم',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Layla',
                rating: 4.5,
                trades: 67,
                verified: false
            },
            assetType: 'USDT',
            amount: 3000,
            price: 3.72,
            currency: 'SAR',
            minAmount: 100,
            maxAmount: 3000,
            paymentMethods: ['محفظة رقمية'],
            tradingTime: 40,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'SO-006',
            type: 'sell',
            seller: {
                id: 6,
                name: 'عمر ناصر',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Omar',
                rating: 4.9,
                trades: 198,
                verified: true
            },
            assetType: 'BTC',
            amount: 1.2,
            price: 148000,
            currency: 'SAR',
            minAmount: 0.05,
            maxAmount: 1.2,
            paymentMethods: ['تحويل بنكي'],
            tradingTime: 30,
            createdAt: new Date(),
            status: 'active'
        }
    ],

    // Buy Offers (عروض الشراء - ما يريده الآخرون)
    buyOffers: [
        {
            id: 'BO-001',
            type: 'buy',
            buyer: {
                id: 10,
                name: 'نور عبدالله',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Noor',
                rating: 4.8,
                trades: 145,
                verified: true
            },
            assetType: 'USDT',
            amount: 8000,
            price: 3.76,
            currency: 'SAR',
            minAmount: 500,
            maxAmount: 8000,
            paymentMethods: ['تحويل بنكي', 'محفظة رقمية'],
            tradingTime: 20,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'BO-002',
            type: 'buy',
            buyer: {
                id: 11,
                name: 'دينا محمود',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Dina',
                rating: 4.7,
                trades: 78,
                verified: true
            },
            assetType: 'ETH',
            amount: 3.0,
            price: 12400,
            currency: 'SAR',
            minAmount: 0.2,
            maxAmount: 3.0,
            paymentMethods: ['بوابة دفع'],
            tradingTime: 35,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'BO-003',
            type: 'buy',
            buyer: {
                id: 12,
                name: 'خالد الشهري',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Khaled',
                rating: 4.9,
                trades: 203,
                verified: true
            },
            assetType: 'USDT',
            amount: 15000,
            price: 3.74,
            currency: 'SAR',
            minAmount: 1000,
            maxAmount: 15000,
            paymentMethods: ['تحويل بنكي'],
            tradingTime: 15,
            createdAt: new Date(),
            status: 'active'
        },
        {
            id: 'BO-004',
            type: 'buy',
            buyer: {
                id: 13,
                name: 'مريم سالم',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Maryam',
                rating: 4.6,
                trades: 95,
                verified: false
            },
            assetType: 'BTC',
            amount: 0.8,
            price: 149500,
            currency: 'SAR',
            minAmount: 0.1,
            maxAmount: 0.8,
            paymentMethods: ['محفظة رقمية', 'بوابة دفع'],
            tradingTime: 45,
            createdAt: new Date(),
            status: 'active'
        }
    ],

    // My Offers (عروضي)
    myOffers: [
        {
            id: 'MY-001',
            type: 'sell',
            assetType: 'USDT',
            amount: 2000,
            price: 3.75,
            currency: 'SAR',
            status: 'active',
            trades: 12,
            completionRate: 98,
            tradingTime: 20,
            createdAt: new Date('2026-01-10')
        },
        {
            id: 'MY-002',
            type: 'buy',
            assetType: 'ETH',
            amount: 1.0,
            price: 12500,
            currency: 'SAR',
            status: 'paused',
            trades: 5,
            completionRate: 100,
            tradingTime: 30,
            createdAt: new Date('2026-01-05')
        }
    ],

    // Transaction History (سجل التداولات)
    history: [
        {
            id: 'TXN-001',
            type: 'buy',
            assetType: 'USDT',
            amount: 500,
            price: 3.75,
            currency: 'SAR',
            partner: {
                id: 1,
                name: 'أحمد علي',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ahmed'
            },
            date: '2026-01-14',
            status: 'completed'
        },
        {
            id: 'TXN-002',
            type: 'sell',
            assetType: 'USDT',
            amount: 1000,
            price: 3.76,
            currency: 'SAR',
            partner: {
                id: 10,
                name: 'نور عبدالله',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Noor'
            },
            date: '2026-01-12',
            status: 'completed'
        },
        {
            id: 'TXN-003',
            type: 'buy',
            assetType: 'BTC',
            amount: 0.05,
            price: 149000,
            currency: 'SAR',
            partner: {
                id: 2,
                name: 'فاطمة محمد',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Fatima'
            },
            date: '2026-01-10',
            status: 'completed'
        },
        {
            id: 'TXN-004',
            type: 'sell',
            assetType: 'ETH',
            amount: 0.5,
            price: 12400,
            currency: 'SAR',
            partner: {
                id: 11,
                name: 'دينا محمود',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Dina'
            },
            date: '2026-01-08',
            status: 'completed'
        },
        {
            id: 'TXN-005',
            type: 'buy',
            assetType: 'USDT',
            amount: 2000,
            price: 3.74,
            currency: 'SAR',
            partner: {
                id: 12,
                name: 'خالد الشهري',
                avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Khaled'
            },
            date: '2026-01-05',
            status: 'completed'
        }
    ]
};

// Helper Functions
function formatCurrency(value, currency = 'SAR') {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: currency
    }).format(value);
}

function getOfferById(offerId) {
    const allOffers = [
        ...p2pMarketplaceData.sellOffers,
        ...p2pMarketplaceData.buyOffers
    ];
    return allOffers.find(o => o.id === offerId);
}

// Disable all form elements on page load
document.addEventListener('DOMContentLoaded', function() {
    disableAllFormElements();
});

function disableAllFormElements() {
    const allInputs = document.querySelectorAll('input, select, textarea, button[type="submit"]');
    allInputs.forEach(input => {
        if (input.type !== 'radio' || !input.checked) {
            input.disabled = true;
        }
    });
}

// Show notification for disabled features
function showDisabledNotification() {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-warning alert-dismissible fade show';
    alertDiv.innerHTML = `
        <i class="fas fa-lock"></i>
        <strong>تنبيه:</strong> هذه الميزة قيد التطوير - لن تتمكن من إتمام العملية حالياً
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

@extends('templates.dark.layouts.app')

@section('panel')
<div class="p2p-container mt-4">
    <!-- Header -->
    <div class="p2p-page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="p2p-page-title mb-0">
                    <i class="fas fa-users"></i> البائعون والمشترون
                </h1>
                <p class="text-muted mt-2">قائمة شاملة بجميع البائعين والمشترين النشطين</p>
            </div>
            <div class="col-md-6">
                <div class="p2p-search-box">
                    <input type="text" id="sellers-search" class="form-control" placeholder="ابحث عن بائع أو مشتري...">
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="p2p-filter-tabs mb-4">
        <button class="p2p-filter-btn active" data-filter="all">
            <i class="fas fa-layer-group"></i> الكل
        </button>
        <button class="p2p-filter-btn" data-filter="sellers">
            <i class="fas fa-user-tie"></i> البائعون
        </button>
        <button class="p2p-filter-btn" data-filter="buyers">
            <i class="fas fa-user-clock"></i> المشترون
        </button>
        <button class="p2p-filter-btn" data-filter="verified">
            <i class="fas fa-check-circle"></i> موثقون
        </button>
    </div>

    <!-- Sellers/Buyers Grid -->
    <div class="p2p-users-grid" id="sellers-buyers-container">
        <!-- يتم تحميل البيانات هنا ديناميكياً -->
    </div>

    <!-- Pagination -->
    <nav class="p2p-pagination mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link" href="#">السابق</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">التالي</a></li>
        </ul>
    </nav>
</div>

<!-- User Profile Modal -->
<div class="modal fade" id="userProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p2p-modal-content">
            <div class="modal-header p2p-modal-header">
                <h5 class="modal-title">ملف المستخدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userProfileContent">
                <!-- يتم تحميل بيانات المستخدم هنا -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script-lib')
<link rel="stylesheet" href="{{ asset('assets/global/css/p2p-marketplace.css') }}">
<style>
.p2p-page-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0066cc 100%);
    padding: 30px;
    border-radius: 12px;
    color: white;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
}

.p2p-page-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.p2p-page-header .text-muted {
    color: rgba(255, 255, 255, 0.85) !important;
}

.p2p-search-box {
    position: relative;
}

.p2p-search-box input {
    border-radius: 8px;
    border: none;
    padding: 12px 15px 12px 40px;
    font-size: 14px;
}

.p2p-search-box::before {
    content: "\f002";
    font-family: 'FontAwesome';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.p2p-filter-tabs {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.p2p-filter-btn {
    background: #f0f2f5;
    border: 2px solid transparent;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    color: #333;
}

.p2p-filter-btn:hover {
    background: #e4e6eb;
}

.p2p-filter-btn.active {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.p2p-users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.p2p-user-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e9ecef;
}

.p2p-user-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}

.p2p-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 15px;
    border: 3px solid #0d6efd;
    object-fit: cover;
}

.p2p-user-name {
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 5px;
    color: #333;
}

.p2p-user-type {
    display: inline-block;
    background: #e3f2fd;
    color: #0d6efd;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin: 0 auto 15px;
}

.p2p-user-card:hover .p2p-user-type {
    background: #0d6efd;
    color: white;
}

.p2p-user-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 15px;
    font-size: 13px;
    color: #28a745;
}

.p2p-user-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 15px;
    text-align: center;
}

.p2p-stat {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
}

.p2p-stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #0d6efd;
    display: block;
}

.p2p-stat-label {
    font-size: 11px;
    color: #666;
    margin-top: 3px;
}

.p2p-user-rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 15px;
    font-size: 13px;
    color: #ffc107;
}

.p2p-user-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.p2p-user-actions button {
    padding: 8px 12px;
    border-radius: 6px;
    border: none;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.p2p-user-actions .btn-view {
    background: #0d6efd;
    color: white;
}

.p2p-user-actions .btn-view:hover {
    background: #0066cc;
}

.p2p-user-actions .btn-contact {
    background: #f0f2f5;
    color: #333;
}

.p2p-user-actions .btn-contact:hover {
    background: #e4e6eb;
}

.p2p-pagination {
    margin-top: 40px;
    margin-bottom: 20px;
}

.p2p-modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.p2p-modal-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0066cc 100%);
    color: white;
    border: none;
    border-radius: 12px 12px 0 0;
}

.p2p-modal-header .btn-close {
    filter: brightness(0) invert(1);
}

/* Empty State */
.p2p-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.p2p-empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.p2p-empty-state-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

/* Responsive */
@media (max-width: 768px) {
    .p2p-users-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .p2p-page-header {
        padding: 20px;
    }

    .p2p-page-title {
        font-size: 22px;
    }

    .p2p-search-box {
        margin-top: 15px;
    }

    .p2p-user-card {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .p2p-users-grid {
        grid-template-columns: 1fr;
    }

    .p2p-filter-tabs {
        gap: 5px;
    }

    .p2p-filter-btn {
        padding: 8px 12px;
        font-size: 12px;
    }
}
</style>
@endpush

@push('script')
<script src="{{ asset('assets/global/js/p2p-marketplace.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSellersBuyersList();
    setupFilters();
    setupSearch();
});

function loadSellersBuyersList() {
    const container = document.getElementById('sellers-buyers-container');
    const allUsers = getAllUsers();

    if (allUsers.length === 0) {
        container.innerHTML = `
            <div class="p2p-empty-state" style="grid-column: 1 / -1;">
                <div class="p2p-empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="p2p-empty-state-title">لم يتم العثور على مستخدمين</div>
                <p>حاول تغيير معايير البحث</p>
            </div>
        `;
        return;
    }

    container.innerHTML = allUsers.map(user => `
        <div class="p2p-user-card" data-user-id="${user.id}" data-user-type="${user.type}">
            <img src="${user.avatar}" alt="${user.name}" class="p2p-user-avatar">
            
            <div class="p2p-user-name">${user.name}</div>
            
            <div style="text-align: center;">
                <span class="p2p-user-type">
                    ${user.type === 'seller' ? '<i class="fas fa-user-tie"></i> بائع' : '<i class="fas fa-user-clock"></i> مشتري'}
                </span>
            </div>

            ${user.verified ? `
                <div class="p2p-user-badge">
                    <i class="fas fa-check-circle"></i> موثق
                </div>
            ` : ''}

            <div class="p2p-user-stats">
                <div class="p2p-stat">
                    <span class="p2p-stat-value">${user.trades}</span>
                    <span class="p2p-stat-label">عملية</span>
                </div>
                <div class="p2p-stat">
                    <span class="p2p-stat-value">${user.completion}</span>
                    <span class="p2p-stat-label">إتمام</span>
                </div>
            </div>

            <div class="p2p-user-rating">
                ${generateStars(user.rating)}
                <span>(${user.rating})</span>
            </div>

            <div class="p2p-user-actions">
                <button class="btn-view" onclick="showUserProfile(${user.id})">
                    <i class="fas fa-eye"></i> عرض
                </button>
                <button class="btn-contact" onclick="contactUser(${user.id})">
                    <i class="fas fa-message"></i> تواصل
                </button>
            </div>
        </div>
    `).join('');
}

function setupFilters() {
    document.querySelectorAll('.p2p-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.p2p-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            filterUsers(filter);
        });
    });
}

function filterUsers(filter) {
    const cards = document.querySelectorAll('.p2p-user-card');
    cards.forEach(card => {
        if (filter === 'all') {
            card.style.display = '';
        } else if (filter === 'sellers') {
            card.style.display = card.getAttribute('data-user-type') === 'seller' ? '' : 'none';
        } else if (filter === 'buyers') {
            card.style.display = card.getAttribute('data-user-type') === 'buyer' ? '' : 'none';
        } else if (filter === 'verified') {
            card.style.display = card.querySelector('.p2p-user-badge') ? '' : 'none';
        }
    });
}

function setupSearch() {
    const searchInput = document.getElementById('sellers-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.p2p-user-card');
        
        cards.forEach(card => {
            const name = card.querySelector('.p2p-user-name').textContent.toLowerCase();
            card.style.display = name.includes(query) ? '' : 'none';
        });
    });
}

function generateStars(rating) {
    let stars = '';
    for (let i = 0; i < 5; i++) {
        stars += `<i class="fas fa-star" style="color: ${i < Math.floor(rating) ? '#ffc107' : '#ddd'}"></i>`;
    }
    return stars;
}

function showUserProfile(userId) {
    const user = p2pMarketplaceData.allUsers.find(u => u.id === userId);
    if (!user) return;

    const content = `
        <div style="text-align: center;">
            <img src="${user.avatar}" alt="${user.name}" class="rounded-circle" style="width: 120px; height: 120px; margin-bottom: 20px; border: 3px solid #0d6efd;">
            
            <h4>${user.name}</h4>
            <p class="text-muted">${user.type === 'seller' ? 'بائع' : 'مشتري'}</p>

            <div class="row mt-4 mb-4">
                <div class="col">
                    <strong>${user.trades}</strong><br>
                    <small class="text-muted">عملية</small>
                </div>
                <div class="col">
                    <strong>${user.completion}%</strong><br>
                    <small class="text-muted">إتمام</small>
                </div>
                <div class="col">
                    <strong>${user.rating}</strong><br>
                    <small class="text-muted">تقييم</small>
                </div>
            </div>

            <button class="btn btn-primary w-100" onclick="contactUser(${userId})">
                <i class="fas fa-message"></i> إرسال رسالة
            </button>
        </div>
    `;

    document.getElementById('userProfileContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('userProfileModal')).show();
}

function contactUser(userId) {
    showDisabledNotification('اتصل بالمستخدم من خلال لوحة الرسائل');
}

function getAllUsers() {
    if (!p2pMarketplaceData.allUsers) {
        return p2pMarketplaceData.sellOffers.map(offer => ({
            id: offer.id,
            name: offer.seller,
            avatar: offer.avatar,
            type: 'seller',
            verified: offer.verified,
            trades: offer.trades,
            completion: offer.completion,
            rating: offer.rating
        })).concat(
            p2pMarketplaceData.buyOffers.map(offer => ({
                id: 100 + offer.id,
                name: offer.buyer,
                avatar: offer.avatar,
                type: 'buyer',
                verified: true,
                trades: Math.floor(Math.random() * 500) + 50,
                completion: Math.floor(Math.random() * 30) + 95,
                rating: (Math.random() * 1 + 4.5).toFixed(1)
            }))
        );
    }
    return p2pMarketplaceData.allUsers;
}
</script>
@endpush

@extends('templates.dark.layouts.app')

@section('panel')
<div class="p2p-coming-soon-container">
    <div class="p2p-coming-soon-wrapper">
        <!-- Background Animation -->
        <div class="coming-soon-bg">
            <div class="animation-circle circle-1"></div>
            <div class="animation-circle circle-2"></div>
            <div class="animation-circle circle-3"></div>
        </div>

        <!-- Content -->
        <div class="coming-soon-content">
            <!-- Icon -->
            <div class="coming-soon-icon">
                <i class="fas fa-rocket"></i>
            </div>

            <!-- Title -->
            <h1 class="coming-soon-title">
                قيد التطوير
            </h1>

            <!-- Subtitle -->
            <p class="coming-soon-subtitle">
                هذه الميزة تحت التطوير والتحسين
            </p>

            <!-- Description -->
            <p class="coming-soon-description">
                نعمل بجهد لإحضار تجربة أفضل وأكثر احترافية. سيكون كل شيء جاهزاً قريباً جداً!
            </p>

            <!-- Progress Bar -->
            <div class="coming-soon-progress-wrapper">
                <div class="progress" style="height: 8px; border-radius: 10px; background: #e9ecef;">
                    <div class="progress-bar" role="progressbar" style="width: 72%; background: linear-gradient(90deg, #0d6efd 0%, #0066cc 100%); border-radius: 10px;"></div>
                </div>
                <p class="text-muted small mt-2">72% اكتمال</p>
            </div>

            <!-- Timeline -->
            <div class="coming-soon-timeline mt-5">
                <div class="timeline-item completed">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>تصميم الواجهة</h6>
                        <p>✓ مكتمل</p>
                    </div>
                </div>

                <div class="timeline-item completed">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>تطوير الأساسيات</h6>
                        <p>✓ مكتمل</p>
                    </div>
                </div>

                <div class="timeline-item in-progress">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>نظام الدفع</h6>
                        <p>⏳ جاري العمل</p>
                    </div>
                </div>

                <div class="timeline-item pending">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>الاختبار والجودة</h6>
                        <p>⏳ قادم</p>
                    </div>
                </div>

                <div class="timeline-item pending">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>الإطلاق الرسمي</h6>
                        <p>🎯 قريباً جداً</p>
                    </div>
                </div>
            </div>

            <!-- Notification Signup -->
            <div class="coming-soon-notification mt-5">
                <h6 class="mb-3">هل تريد أن تُخبرنا عندما تكون جاهزة؟</h6>
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="أدخل بريدك الإلكتروني" disabled>
                    <button class="btn btn-primary" type="button" disabled>
                        <i class="fas fa-bell"></i> إشعرني
                    </button>
                </div>
            </div>

            <!-- Features Coming -->
            <div class="coming-soon-features mt-5">
                <h6 class="mb-3">المميزات القادمة:</h6>
                <div class="features-grid">
                    <div class="feature-item">
                        <i class="fas fa-credit-card"></i>
                        <span>طرق دفع متعددة</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>محفظة آمنة</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bell"></i>
                        <span>إشعارات فورية</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>تحليلات وإحصائيات</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-headset"></i>
                        <span>دعم العملاء 24/7</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-mobile-alt"></i>
                        <span>تطبيق الهاتف الذكي</span>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="coming-soon-action mt-5">
                @p2pAccess
                    <a href="{{ route('user.p2p.marketplace') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left"></i> دخول P2P
                    </a>
                @else
                    <a href="javascript:void(0)" class="btn btn-secondary btn-lg" onclick="notify('info', 'هذه المنطقة قيد التشغيل حالياً'); return false;">
                        <i class="fas fa-info-circle"></i> هذه المنطقة قيد التشغيل
                    </a>
                @endp2pAccess
            </div>

            <!-- Social Links -->
            <div class="coming-soon-social mt-4">
                <p class="text-muted small">تابعنا للحصول على آخر الأخبار:</p>
                <div class="social-links">
                    <a href="#" class="social-icon" onclick="showDisabledNotification('وسائل التواصل معطلة')">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon" onclick="showDisabledNotification('وسائل التواصل معطلة')">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="social-icon" onclick="showDisabledNotification('وسائل التواصل معطلة')">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-icon" onclick="showDisabledNotification('وسائل التواصل معطلة')">
                        <i class="fab fa-telegram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-lib')
<style>
.p2p-coming-soon-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    overflow: hidden;
    position: relative;
}

.p2p-coming-soon-wrapper {
    width: 100%;
    max-width: 600px;
    position: relative;
    z-index: 10;
}

/* Background Animation */
.coming-soon-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    overflow: hidden;
}

.animation-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 6s infinite ease-in-out;
}

.animation-circle.circle-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    right: -150px;
    animation-delay: 0s;
}

.animation-circle.circle-2 {
    width: 200px;
    height: 200px;
    bottom: -100px;
    left: -100px;
    animation-delay: 2s;
}

.animation-circle.circle-3 {
    width: 150px;
    height: 150px;
    top: 50%;
    right: 10%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Content Styles */
.coming-soon-content {
    background: white;
    border-radius: 20px;
    padding: 50px 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
}

.coming-soon-icon {
    font-size: 80px;
    color: #0d6efd;
    margin-bottom: 30px;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.coming-soon-title {
    font-size: 48px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #0d6efd 0%, #0066cc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.coming-soon-subtitle {
    font-size: 18px;
    color: #666;
    margin-bottom: 15px;
    font-weight: 500;
}

.coming-soon-description {
    font-size: 15px;
    color: #999;
    margin-bottom: 30px;
    line-height: 1.6;
}

.coming-soon-progress-wrapper {
    margin: 30px 0;
}

/* Timeline Styles */
.coming-soon-timeline {
    text-align: left;
    margin: 40px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.timeline-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 3px solid #e9ecef;
    flex-shrink: 0;
    margin-top: 2px;
    position: relative;
    background: white;
}

.timeline-item.completed .timeline-marker {
    border-color: #28a745;
    background: #28a745;
}

.timeline-item.in-progress .timeline-marker {
    border-color: #ffc107;
    background: #ffc107;
    animation: pulse 2s infinite;
}

.timeline-item.pending .timeline-marker {
    border-color: #dee2e6;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(255, 193, 7, 0);
    }
}

.timeline-content h6 {
    margin: 0;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.timeline-content p {
    margin: 5px 0 0;
    font-size: 12px;
    color: #999;
}

/* Notification Signup */
.coming-soon-notification {
    background: #e3f2fd;
    padding: 20px;
    border-radius: 12px;
}

.coming-soon-notification h6 {
    color: #333;
    font-weight: 600;
}

.coming-soon-notification .input-group {
    display: flex;
    gap: 8px;
}

.coming-soon-notification .form-control {
    border-radius: 8px;
    border: 1px solid #0d6efd;
}

.coming-soon-notification .btn {
    border-radius: 8px;
    white-space: nowrap;
}

/* Features Grid */
.coming-soon-features h6 {
    color: #333;
    font-weight: 600;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
}

.feature-item {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    padding: 15px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-align: center;
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.feature-item i {
    font-size: 24px;
    color: #0d6efd;
}

.feature-item span {
    font-size: 13px;
    color: #333;
    font-weight: 500;
}

/* Action Button */
.coming-soon-action {
    margin: 30px 0;
}

.coming-soon-action .btn-lg {
    padding: 15px 40px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
}

/* Social Links */
.coming-soon-social {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #e9ecef;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 15px;
}

.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d6efd;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 18px;
}

.social-icon:hover {
    background: #0d6efd;
    color: white;
    transform: scale(1.1);
}

/* Responsive */
@media (max-width: 768px) {
    .coming-soon-content {
        padding: 40px 25px;
    }

    .coming-soon-title {
        font-size: 36px;
    }

    .coming-soon-subtitle {
        font-size: 16px;
    }

    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .feature-item {
        padding: 12px;
    }

    .feature-item i {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .p2p-coming-soon-container {
        padding: 15px;
    }

    .coming-soon-content {
        padding: 30px 20px;
        border-radius: 15px;
    }

    .coming-soon-icon {
        font-size: 60px;
        margin-bottom: 20px;
    }

    .coming-soon-title {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .coming-soon-subtitle {
        font-size: 15px;
    }

    .coming-soon-timeline {
        padding: 15px;
        margin: 30px 0;
    }

    .timeline-item {
        gap: 12px;
        margin-bottom: 15px;
    }

    .coming-soon-notification .input-group {
        flex-direction: column;
    }

    .coming-soon-notification .btn {
        width: 100%;
    }

    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .feature-item {
        padding: 10px;
        font-size: 12px;
    }

    .feature-item i {
        font-size: 18px;
    }

    .coming-soon-social {
        margin-top: 20px;
        padding-top: 20px;
    }

    .social-links {
        gap: 15px;
    }

    .social-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
}
</style>
@endpush

@push('script')
<script src="{{ asset('assets/global/js/p2p-marketplace.js') }}"></script>
<script>
// Auto-disable all form elements
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, button:not(.social-icon), .btn');
    inputs.forEach(el => {
        if (el.type !== 'email' && el.classList.contains('form-control')) {
            el.disabled = true;
        }
    });
});
</script>
@endpush

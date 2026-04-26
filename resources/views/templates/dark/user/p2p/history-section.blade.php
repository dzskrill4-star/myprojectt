<div class="table-responsive">
    <table class="table table-hover" id="history-table">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>النوع</th>
                <th>المبلغ</th>
                <th>السعر</th>
                <th>الشريك</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="history-body">
            <!-- Will be loaded dynamically -->
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadHistoryData();
    });

    function loadHistoryData() {
        const history = p2pMarketplaceData.history;
        const tbody = document.getElementById('history-body');

        if (history.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">لا يوجد سجل تداولات</td></tr>';
            return;
        }

        tbody.innerHTML = history.map(item => `
            <tr>
                <td>
                    <strong>#${item.id}</strong>
                </td>
                <td>
                    <span class="badge bg-${item.type === 'buy' ? 'primary' : 'success'}">
                        ${item.type === 'buy' ? 'شراء' : 'بيع'}
                    </span>
                </td>
                <td>
                    <strong>${item.amount} ${item.assetType}</strong>
                </td>
                <td>
                    ${item.price} ${item.currency}
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${item.partner.avatar}" alt="${item.partner.name}" class="rounded-circle me-2" width="35">
                        ${item.partner.name}
                    </div>
                </td>
                <td>
                    <small class="text-muted">${formatDate(item.date)}</small>
                </td>
                <td>
                    <span class="badge bg-${getStatusColor(item.status)}">
                        ${getStatusText(item.status)}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" disabled>
                        <i class="fas fa-info-circle"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function getStatusColor(status) {
        const colors = {
            'completed': 'success',
            'cancelled': 'danger',
            'pending': 'warning'
        };
        return colors[status] || 'secondary';
    }

    function getStatusText(status) {
        const statusMap = {
            'completed': 'مكتمل',
            'cancelled': 'ملغي',
            'pending': 'قيد الانتظار'
        };
        return statusMap[status] || status;
    }
</script>

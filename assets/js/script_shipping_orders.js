document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.shipping-table form');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const status = form.querySelector('select[name="order_status"]');
            const carrier = form.querySelector('select[name="carrier_name"]');
            const tracking = form.querySelector('input[name="tracking_number"]');

            if (status && status.value === 'completed') {
                const okComplete = confirm('Bạn chắc chắn muốn chuyển đơn này sang Hoàn thành? Sau khi hoàn thành, thông tin vận chuyển sẽ bị khóa.');

                if (!okComplete) {
                    e.preventDefault();
                    return;
                }
            }

            if (!carrier || !carrier.value.trim()) {
                e.preventDefault();
                alert('Vui lòng chọn đơn vị vận chuyển.');
                carrier.focus();
                return;
            }

            if (!tracking || !tracking.value.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập mã vận đơn.');
                tracking.focus();
                return;
            }

            const ok = confirm('Cập nhật thông tin vận chuyển cho đơn hàng này?');

            if (!ok) {
                e.preventDefault();
            }
        });
    });
});
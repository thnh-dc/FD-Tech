document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form[id^="shipping-form-"]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const formId = form.getAttribute('id');

            const status = document.querySelector(`select[name="order_status"][form="${formId}"]`);
            const carrier = document.querySelector(`select[name="carrier_name"][form="${formId}"]`);
            const tracking = document.querySelector(`input[name="tracking_number"][form="${formId}"]`);

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
                if (carrier) {
                    carrier.focus();
                }
                return;
            }

            if (!tracking || !tracking.value.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập mã vận đơn.');
                if (tracking) {
                    tracking.focus();
                }
                return;
            }

            const ok = confirm('Cập nhật thông tin vận chuyển cho đơn hàng này?');

            if (!ok) {
                e.preventDefault();
            }
        });
    });
});
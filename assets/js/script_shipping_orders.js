document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form[id^="shipping-form-"]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const formId = form.getAttribute('id');
            const status = document.querySelector(`select[name="order_status"][form="${formId}"]`);

            if (!status || !status.value.trim()) {
                e.preventDefault();
                alert('Vui lòng chọn trạng thái vận chuyển.');
                return;
            }

            let message = 'Cập nhật trạng thái vận chuyển cho đơn hàng này?';

            if (status.value === 'preparing') {
                message = 'Xác nhận chuyển đơn hàng về trạng thái Đang chuẩn bị?';
            } else if (status.value === 'shipped') {
                message = 'Xác nhận chuyển đơn hàng sang trạng thái Đang giao hàng?';
            } else if (status.value === 'delivered') {
                message = 'Xác nhận đơn vị vận chuyển đã giao hàng thành công? Đơn hàng sẽ chờ admin xác nhận hoàn thành ở trang quản lí đơn hàng.';
            }

            const ok = confirm(message);

            if (!ok) {
                e.preventDefault();
            }
        });
    });
});
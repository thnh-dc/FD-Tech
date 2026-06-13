function showAdminNotification(message, type = 'error') {
    const oldNoti = document.getElementById('noti-container');
    if (oldNoti) oldNoti.remove();

    const container = document.createElement('div');
    container.id = 'noti-container';
    container.className = 'noti-container';

    const box = document.createElement('div');
    box.className = 'noti-box ' + type;

    const icon = document.createElement('div');
    icon.className = 'noti-icon';
    icon.textContent = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'i');

    const content = document.createElement('div');
    content.className = 'noti-content';
    content.textContent = message;

    box.appendChild(icon);
    box.appendChild(content);
    container.appendChild(box);
    document.body.appendChild(container);

    setTimeout(function () {
        container.classList.add('noti-fade-out');

        setTimeout(function () {
            container.remove();
        }, 500);
    }, 4000);
}

document.addEventListener('DOMContentLoaded', function () {
    const btnUpdateOrderStatus = document.getElementById('btnUpdateOrderStatus');

    if (btnUpdateOrderStatus) {
        btnUpdateOrderStatus.addEventListener('click', function () {
            const orderId = this.dataset.id;
            const statusSelect = document.getElementById('orderStatusSelect');

            if (!statusSelect) {
                showAdminNotification('Không tìm thấy ô chọn trạng thái đơn hàng.', 'error');
                return;
            }

            const status = statusSelect.value;

            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded' 
                },
                body: 'id=' + encodeURIComponent(orderId) + '&status=' + encodeURIComponent(status)
            })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (data.success) {
                    showAdminNotification(data.message, 'success');

                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    showAdminNotification(data.message || 'Lỗi cập nhật trạng thái đơn hàng.', 'error');
                }
            })
            .catch(function (err) {
                showAdminNotification('Có lỗi xảy ra khi cập nhật đơn hàng.', 'error');
                console.error(err);
            });
        });
    }

    const btnConfirmCompleteOrder = document.getElementById('btnConfirmCompleteOrder');

    if (btnConfirmCompleteOrder) {
        btnConfirmCompleteOrder.addEventListener('click', function () {
            const orderId = this.dataset.id;

            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded' 
                },
                body: 'id=' + encodeURIComponent(orderId) + '&status=completed'
            })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (data.success) {
                    showAdminNotification(data.message || 'Đã xác nhận hoàn thành đơn hàng.', 'success');

                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    showAdminNotification(data.message || 'Không thể xác nhận hoàn thành đơn hàng.', 'error');
                }
            })
            .catch(function (err) {
                showAdminNotification('Có lỗi xảy ra khi xác nhận hoàn thành đơn hàng.', 'error');
                console.error(err);
            });
        });
    }
}); 
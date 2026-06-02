document.addEventListener('DOMContentLoaded', function () {

    function showNotification(message, type = 'error') {
        const oldNoti = document.getElementById('noti-container');
        if (oldNoti) oldNoti.remove();

        const container = document.createElement('div');
        container.id = 'noti-container';
        container.className = 'noti-container';

        const box = document.createElement('div');
        box.className = 'noti-box ' + type;

        const icon = document.createElement('div');
        icon.className = 'noti-icon';

        if (type === 'success') icon.textContent = '✓';
        else if (type === 'error') icon.textContent = '✕';
        else icon.textContent = 'i';

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
        }, 10000);
    }

    function closeActionMenus() {
        document.querySelectorAll('.action-menu').forEach(function (menu) {
            menu.style.display = 'none';
        });
    }

    document.querySelectorAll('.btn-action').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            closeActionMenus();

            const menu = this.nextElementSibling;

            if (menu) {
                menu.style.display = 'block';
            }
        });
    });

    document.querySelectorAll('.action-menu button').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            const status = this.getAttribute('data-status');
            const actionBox = this.closest('.action-buttons');

            if (!actionBox) {
                showNotification('Không tìm thấy vùng thao tác đơn hàng.', 'error');
                return;
            }

            const actionBtn = actionBox.querySelector('.btn-action');

            if (!actionBtn) {
                showNotification('Không tìm thấy mã đơn hàng.', 'error');
                return;
            }

            const orderId = actionBtn.dataset.id;

            fetch('/FD-Tech/admin/action_list_order/update_order_status.php', {
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
                    showNotification(data.message, 'success');

                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    showNotification(data.message || 'Lỗi cập nhật trạng thái đơn hàng.', 'error');
                }
            })
            .catch(function () {
                showNotification('Lỗi kết nối, không thể cập nhật đơn hàng.', 'error');
            });
        });
    });

    document.addEventListener('click', function () {
        closeActionMenus();
    });
});
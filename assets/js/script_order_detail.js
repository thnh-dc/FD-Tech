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

document.getElementById('btnUpdateOrderStatus').addEventListener('click', function () {
    const orderId = this.dataset.id;
    const status = document.getElementById('orderStatusSelect').value;

    fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(orderId) + '&status=' + encodeURIComponent(status)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAdminNotification(data.message, 'success');

            setTimeout(function () {
                location.reload();
            }, 1200);
        } else {
            showAdminNotification(data.message || 'Lỗi cập nhật trạng thái đơn hàng.', 'error');
        }
    })
    .catch(err => {
        showAdminNotification('Có lỗi xảy ra khi cập nhật đơn hàng.', 'error');
        console.error(err);
    });
});
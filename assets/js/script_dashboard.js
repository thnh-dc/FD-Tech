document.addEventListener('DOMContentLoaded', function() {
    
    // Lấy tất cả các mục có menu con
    const submenuToggles = document.querySelectorAll('.submenu-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault(); // Ngăn hành vi nhảy trang của thẻ <a>

            // Tìm thẻ <ul> menu con nằm ngay phía dưới thẻ <a> vừa click
            const submenu = this.nextElementSibling;
            
            // Toggle class 'show' để CSS chuyển từ display: none sang block
            submenu.classList.toggle('show');

            // Tìm thẻ <li> cha để thêm class xoay mũi tên
            const parentLi = this.parentElement;
            parentLi.classList.toggle('rotate-arrow');
        });
    });

});
//render chart dashboard
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('revenueChart');

    if (!ctx) return; // tránh lỗi nếu không có canvas

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Doanh thu theo tháng',
                data: revenueData,
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + '₫';
                        }
                    }
                }
            }
        }
    });
});

//menu cập nhật tràng tháo list_order
document.querySelectorAll('.btn-action').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();

        // đóng tất cả menu khác
        document.querySelectorAll('.action-menu').forEach(m => m.style.display = 'none');

        const menu = this.nextElementSibling;
        menu.style.display = 'block';
    });
});

// click chọn trạng thái
document.querySelectorAll('.action-menu button').forEach(btn => {
    btn.addEventListener('click', function () {

        const status = this.getAttribute('data-status');
        const orderId = this.closest('td').querySelector('.btn-action').dataset.id;

        fetch('update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${orderId}&status=${status}`
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Cập nhật thành công!');
                location.reload();
            } else {
                alert('Lỗi cập nhật!');
            }
        });
    });
});

// click ngoài để đóng menu
document.addEventListener('click', () => {
    document.querySelectorAll('.action-menu').forEach(m => m.style.display = 'none');
});
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
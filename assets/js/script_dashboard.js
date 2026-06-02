document.addEventListener('DOMContentLoaded', function () {
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            if (submenu) {
                submenu.classList.toggle('show');
            }
        });
    });
    const ctx = document.getElementById('revenueChart');

    if (
        ctx &&
        typeof Chart !== 'undefined' &&
        typeof revenueLabels !== 'undefined' &&
        typeof revenueData !== 'undefined'
    ) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu theo tháng',
                    data: revenueData,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString('vi-VN') + '₫';
                            }
                        }
                    }
                }
            }
        });
    }
});
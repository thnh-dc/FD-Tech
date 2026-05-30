document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.member-tab-btn');
    const tabContents = document.querySelectorAll('.member-tab-content');

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const tab = this.dataset.tab;

            tabButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });

            tabContents.forEach(function (content) {
                content.classList.remove('active');
            });

            this.classList.add('active');

            const target = document.getElementById('member-tab-' + tab);

            if (target) {
                target.classList.add('active');
            }
        });
    });
});
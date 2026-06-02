document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.member-tab-btn');
    const tabContents = document.querySelectorAll('.member-tab-content');
    function activateTab(tab) {
        tabButtons.forEach(function (btn) {
            btn.classList.remove('active');
        });
        tabContents.forEach(function (content) {
            content.classList.remove('active');
        });
        const button = document.querySelector('.member-tab-btn[data-tab="' + tab + '"]');
        const target = document.getElementById('member-tab-' + tab);
        if (button) button.classList.add('active');
        if (target) target.classList.add('active');
    }
    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activateTab(this.dataset.tab);
        });
    });
    if (window.location.hash === '#member-tab-history' || new URLSearchParams(window.location.search).has('fdp_page')) {
        activateTab('history');
    }
});
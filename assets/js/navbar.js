const toggle = document.querySelector('.category-toggle');
const dropdown = document.querySelector('.category-dropdown');

if (toggle && dropdown) {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropdown.classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.category-toggle') && !e.target.closest('.category-dropdown')) {
            dropdown.classList.remove('active');
        }
    });
}
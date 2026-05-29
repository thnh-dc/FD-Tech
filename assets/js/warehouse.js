/**
 * Xử lý tương tác động trang Kiểm kê kho hàng - FD Tech Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    // Tự động gửi dữ liệu form lọc khi người dùng thay đổi kiểu sắp xếp (Thay thế cho onchange inline cũ)
    const selectSort = document.querySelector('.select-sort-control');
    
    if (selectSort) {
        selectSort.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    }

    // Trải nghiệm người dùng cao cấp (UX): 
    // Tự động focus vào ô tìm kiếm và đưa con trỏ chuột về cuối chữ khi vừa load trang
    const searchInput = document.querySelector('.input-search-field');
    if (searchInput) {
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.focus();
        searchInput.value = val;
    }
});
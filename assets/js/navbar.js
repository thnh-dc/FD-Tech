// assets/js/navbar.js
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('menuToggle');
    const dropdown = document.getElementById('categoryDropdown');

    if (btn && dropdown) {
        // Log để kiểm tra trong Console (F12) xem code có chạy tới đây không
        console.log("Menu JS đã sẵn sàng!");

        btn.addEventListener('click', function(e) {
            dropdown.classList.toggle('active');
            console.log("Đã bấm nút danh mục");
            e.stopPropagation();
        });

        document.addEventListener('click', function(e) {
            // Nếu click ra ngoài thì đóng menu
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    } else {
        console.error("Không tìm thấy ID menuToggle hoặc categoryDropdown!");
    }
});
function updateCartCount() {
    fetch('../user/get_cart_count.php') // File này trả về số lượng giỏ hàng
        .then(response => response.json())
        .then(data => {
            document.querySelector('.cart-icon .count').innerText = data.count;
        });
}
// Gọi hàm khi trang load
updateCartCount();
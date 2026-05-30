// Hàm ẩn popup khi người dùng bấm nút X hoặc vùng ngoài
function closePopup(event) {
    // Ngăn chặn sự kiện click bị lan truyền từ nút X vào tấm ảnh phía sau
    if (event) {
        event.stopPropagation();
    }
    const popup = document.getElementById("advPopup");
    if (popup) {
        popup.style.display = "none";
    }
}

// Hàm điều hướng cuộn màn hình xuống vùng Khuyến Mãi khi click vào ảnh
function dieuHuongKhuyenMai() {
    closePopup(); // Ẩn popup đi trước
    const target = document.getElementById("khuyen-mai");
    if (target) {
        target.scrollIntoView({ behavior: 'smooth' }); // Cuộn mượt mà
    }
}

// Bấm ra ngoài vùng ảnh (vùng nền đen mờ) cũng tự tắt popup
window.addEventListener('click', function(event) {
    const modal = document.getElementById("advPopup");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});
document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. HIỆU ỨNG ZOOM ẢNH KHI RÊ CHUỘT
    // ==========================================
    const zoomContainer = document.getElementById('zoomContainer');
    const zoomImage = document.getElementById('zoomImage');

    if (zoomContainer && zoomImage) {
        zoomContainer.addEventListener('mousemove', function(e) {
            const rect = zoomContainer.getBoundingClientRect();
            // Tính toán vị trí chuột chính xác trong khung
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;

            // Chuyển tâm zoom đến vị trí chuột, phóng to ảnh 2 lần
            zoomImage.style.transformOrigin = `${x}% ${y}%`;
            zoomImage.style.transform = 'scale(2)'; 
        });

        // Trả về bình thường khi chuột rời đi
        zoomContainer.addEventListener('mouseleave', function() {
            zoomImage.style.transformOrigin = 'center';
            zoomImage.style.transform = 'scale(1)';
        });
    }

    // ==========================================
    // 2. NÚT TĂNG GIẢM SỐ LƯỢNG (TRANG CHI TIẾT)
    // ==========================================
    const btnMinus = document.getElementById('btnMinus');
    const btnPlus = document.getElementById('btnPlus');
    const qtyInput = document.getElementById('qtyInput');

    if (btnMinus && btnPlus && qtyInput) {
        btnMinus.addEventListener('click', () => {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue > parseInt(qtyInput.min)) {
                qtyInput.value = currentValue - 1;
            }
        });
        
        btnPlus.addEventListener('click', () => {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue < parseInt(qtyInput.max)) {
                qtyInput.value = currentValue + 1;
            }
        });
    }

    // ==========================================
    // 3. TÙY CHỌN NÂNG CAO: AJAX LỌC DANH SÁCH SẢN PHẨM
    // ==========================================
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        // Lắng nghe sự kiện 'change' trên form (khi user click vào Radio/Checkbox)
        filterForm.addEventListener('change', function(e) {
            e.preventDefault();
            
            // Đọc dữ liệu form
            const formData = new FormData(filterForm);
            const searchParams = new URLSearchParams(formData).toString();
            
            // 1. Thay đổi URL trình duyệt (để share link được)
            window.history.pushState({}, '', `product_list.php?${searchParams}`);

            // 2. Fetch API gửi Request ngầm
            fetch(`product_list.php?${searchParams}`)
                .then(response => response.text())
                .then(html => {
                    // 3. Lọc ra DOM của phần hiển thị Grid và ghi đè
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('ajaxProductGrid').innerHTML;
                    
                    document.getElementById('ajaxProductGrid').innerHTML = newGrid;
                })
                .catch(err => console.error('Lỗi tải dữ liệu AJAX:', err));
        });

        // Ghi đè sự kiện submit để Form Search cũng chạy AJAX
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Kích hoạt sự kiện change để gọi hàm Fetch ở trên
            filterForm.dispatchEvent(new Event('change'));
        });
    }
});
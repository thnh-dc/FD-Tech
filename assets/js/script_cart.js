document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Lấy tất cả các nút Xóa
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Lấy ID sản phẩm từ thuộc tính data-id
            const itemId = this.getAttribute('data-id');
            const row = this.closest('.cart-item'); // Tìm thẻ <tr> chứa nút này

            // Xác nhận trước khi xóa
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                
                // --- PHẦN 1: XỬ LÝ GIAO DIỆN (FRONTEND) ---
                row.remove(); // Xóa dòng <tr> khỏi bảng ngay lập tức
                updateTotal(); // Tính lại tổng tiền
                checkEmptyCart(); // Kiểm tra nếu hết hàng thì hiện giỏ hàng trống


                /*// --- PHẦN 2: GỬI YÊU CẦU XÓA LÊN SERVER (BACKEND) ---
                // Sử dụng Fetch API để gọi file PHP xử lý xóa ngầm
                fetch(`../user/delete_cart_item.php?id=${itemId}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status !== 'success') {
                        alert('Có lỗi xảy ra khi xóa trong cơ sở dữ liệu!');
                        location.reload(); // Load lại trang nếu lỗi để đồng bộ dữ liệu
                    }
                })
                .catch(error => console.error('Lỗi:', error));
            }
        });
    });
*/
    // Hàm tính toán lại tổng tiền
    function updateTotal() {
        let newTotal = 0;
        const allItems = document.querySelectorAll('.cart-item');

        allItems.forEach(item => {
            // Lấy giá và số lượng, ép kiểu về số
            const price = parseFloat(item.querySelector('.item-price').innerText);
            const quantity = parseInt(item.querySelector('.item-quantity').innerText);
            
            newTotal += (price * quantity);
        });

        // Cập nhật lại số tiền hiển thị trên web
        const totalElement = document.querySelector('.price-highlight');
        if (totalElement) {
            // Định dạng lại số có dấu chấm (VD: 100.000)
            totalElement.innerText = new Intl.NumberFormat('vi-VN').format(newTotal) + '₫';
        }
    }

    // Hàm kiểm tra nếu xóa hết sạch sản phẩm thì load lại trang để hiện giao diện "Giỏ hàng trống"
    function checkEmptyCart() {
        const currentItems = document.querySelectorAll('.cart-item');
        if (currentItems.length === 0) {
            location.reload(); 
        }
    }

});
/**
 * Xử lý tương tác động Lập phiếu nhập kho - FD Tech Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('import-items-wrapper');
    const btnAdd = document.getElementById('btn-add-row');
    const grandTotalElement = document.getElementById('grand-total');

    // Hàm định dạng tiền tệ VND
    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    // Hàm tính toán lại tổng số tiền của toàn bộ phiếu nhập
    function calculateTotal() {
        let grandTotal = 0;
        const rows = wrapper.querySelectorAll('.import-item-row');

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            grandTotal += subtotal;
            
            // Cập nhật thành tiền từng dòng linh kiện
            row.querySelector('.row-subtotal').textContent = formatVND(subtotal);
        });

        // Cập nhật tổng số tiền cuối cùng ở khối summary
        grandTotalElement.textContent = formatVND(grandTotal);
    }

    // Xử lý sự kiện khi thay đổi linh kiện (Chọn option sản phẩm)
    wrapper.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const originalPrice = selectedOption.getAttribute('data-price');
            
            if (originalPrice) {
                const row = e.target.closest('.import-item-row');
                // Tính giá nhập gợi ý thông minh = 75% giá bán gốc lẻ
                const suggestedPrice = Math.round(parseFloat(originalPrice) * 0.75);
                row.querySelector('.price-input').value = suggestedPrice;
            }
            calculateTotal();
        }
    });

    // Lắng nghe hành vi thay đổi số lượng hoặc đơn giá của admin
    wrapper.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateTotal();
        }
    });

    // Lắng nghe hành vi xóa dòng sản phẩm
    wrapper.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-remove-row');
        if (btnDelete) {
            const rows = wrapper.querySelectorAll('.import-item-row');
            if (rows.length > 1) {
                btnDelete.closest('.import-item-row').remove();
                calculateTotal();
            } else {
                alert('Mỗi chứng từ nhập kho phải chứa ít nhất 1 sản phẩm hàng hóa!');
            }
        }
    });

    // Xử lý sự kiện bấm nút "Thêm linh kiện mới"
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            const firstRow = wrapper.querySelector('.import-item-row');
            // Nhân bản dòng nhập kho đầu tiên
            const newRow = firstRow.cloneNode(true);
            
            // Reset toàn bộ các ô nhập dữ liệu của dòng mới nhân bản
            newRow.querySelector('.product-select').selectedIndex = 0;
            newRow.querySelector('.qty-input').value = 1;
            newRow.querySelector('.price-input').value = '';
            newRow.querySelector('.row-subtotal').textContent = '0₫';
            
            wrapper.appendChild(newRow);
            calculateTotal();
        });
    }
});
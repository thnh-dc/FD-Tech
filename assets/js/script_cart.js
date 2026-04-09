document.addEventListener("DOMContentLoaded", () => {
    
    // Hàm định dạng số tiền sang chuẩn VNĐ
    const formatCurrency = (number) => {
        return new Intl.NumberFormat('vi-VN').format(number) + '₫';
    };

    // Hàm cập nhật tổng tiền toàn bộ giỏ hàng
    const updateCartTotal = () => {
        const cartItems = document.querySelectorAll(".cart-item");
        let grandTotal = 0;

        cartItems.forEach(item => {
            const priceElement = item.querySelector(".item-price");
            const qtyInput = item.querySelector(".qty-input");
            const subtotalElement = item.querySelector(".item-subtotal");

            if(priceElement && qtyInput && subtotalElement) {
                // Lấy giá trị (data-price được gắn sẵn ở HTML để tính toán cho chuẩn)
                const price = parseInt(priceElement.getAttribute("data-price"));
                const quantity = parseInt(qtyInput.value);

                // Tính toán Thành tiền (Subtotal) của từng dòng
                const subtotal = price * quantity;
                subtotalElement.textContent = formatCurrency(subtotal);

                // Cộng dồn vào Tổng thanh toán (Grand Total)
                grandTotal += subtotal;
            }
        });

        // Cập nhật lên UI tổng thanh toán
        const cartTotalElement = document.getElementById("cart-total");
        if(cartTotalElement) {
            cartTotalElement.textContent = formatCurrency(grandTotal);
        }
    };

    // Lắng nghe sự kiện click trên các nút +/-
    const cartTable = document.getElementById("cart-table");
    
    if (cartTable) {
        cartTable.addEventListener("click", (e) => {
            if (e.target.classList.contains("btn-minus") || e.target.classList.contains("btn-plus")) {
                const qtyInput = e.target.parentElement.querySelector(".qty-input");
                let currentValue = parseInt(qtyInput.value);

                if (e.target.classList.contains("btn-minus") && currentValue > 1) {
                    qtyInput.value = currentValue - 1;
                } else if (e.target.classList.contains("btn-plus")) {
                    qtyInput.value = currentValue + 1;
                }

                // Gọi hàm cập nhật tiền sau khi thay đổi số lượng
                updateCartTotal();
            }
        });

        // Lắng nghe sự kiện người dùng tự gõ số lượng vào ô input
        cartTable.addEventListener("input", (e) => {
            if (e.target.classList.contains("qty-input")) {
                if (e.target.value < 1 || isNaN(e.target.value)) {
                    e.target.value = 1; // Ràng buộc số lượng ít nhất là 1
                }
                updateCartTotal();
            }
        });
    }
});
/**
 * Xử lý tương tác động trang Chỉnh sửa sản phẩm - FD Tech Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    // 1. Kiểm soát ẩn/hiện trường nhập giá bán Flash Sale
    const flashSaleCheckbox = document.getElementById('flash-sale-checkbox');
    const flashSalePriceGroup = document.getElementById('flash-sale-price-group');
    
    if (flashSaleCheckbox && flashSalePriceGroup) {
        flashSaleCheckbox.addEventListener('change', function() {
            if (this.checked) {
                flashSalePriceGroup.style.display = 'block';
            } else {
                flashSalePriceGroup.style.display = 'none';
                const inputDiscount = flashSalePriceGroup.querySelector('input');
                if (inputDiscount) inputDiscount.value = '';
            }
        });
    }

    // 2. Thêm và xóa dòng thông số kỹ thuật động (Giữ nguyên cấu trúc thẻ và thuộc tính style)
    const specsWrapper = document.getElementById('specs-wrapper');
    const addSpecBtn = document.getElementById('add-spec-btn');

    if (addSpecBtn && specsWrapper) {
        addSpecBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'spec-item';
            div.style.display = 'flex';
            div.style.gap = '10px';
            div.style.marginBottom = '10px';
            div.innerHTML = `
                <input type="text" name="spec_names[]" class="form-control" placeholder="Tên thông số" style="flex: 1;">
                <input type="text" name="spec_values[]" class="form-control" placeholder="Giá trị" style="flex: 2;">
                <button type="button" class="btn btn-danger remove-spec-btn" style="background: #ef4444; color: #fff; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer;">Xóa</button>
            `;
            specsWrapper.appendChild(div);
        });
    }

    // Lắng nghe sự kiện click nút xóa thông số kỹ thuật (Bằng cơ chế ủy quyền sự kiện)
    if (specsWrapper) {
        specsWrapper.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-spec-btn')) {
                const specRow = e.target.parentElement;
                if (specRow) {
                    specRow.remove();
                }
            }
        });
    }
});
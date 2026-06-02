/**
 * Xử lý tương tác động trang Chỉnh sửa sản phẩm - FD Tech Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    const flashSaleCheckbox = document.getElementById('flash-sale-checkbox');
    const flashSalePriceGroup = document.getElementById('flash-sale-price-group');

    if (flashSaleCheckbox && flashSalePriceGroup) {
        if (flashSaleCheckbox.checked) {
            flashSalePriceGroup.style.display = 'block';
        } else {
            flashSalePriceGroup.style.display = 'none';
        }

        flashSaleCheckbox.addEventListener('change', function() {
            if (this.checked) {
                flashSalePriceGroup.style.display = 'block';
            } else {
                flashSalePriceGroup.style.display = 'none';

                const inputDiscount = flashSalePriceGroup.querySelector('input');
                if (inputDiscount) {
                    inputDiscount.value = '';
                }
            }
        });
    }

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

    if (specsWrapper) {
        specsWrapper.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-spec-btn')) {
                const specRow = e.target.closest('.spec-item');

                if (specRow) {
                    specRow.remove();
                }
            }
        });
    }
});
/**
 * Xử lý tương tác động trang Thêm sản phẩm - FD Tech
 */
document.addEventListener('DOMContentLoaded', function() {
    const flashSaleCheckbox = document.getElementById('flash_sale_checkbox');
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

                const inputPrice = flashSalePriceGroup.querySelector('input');
                if (inputPrice) {
                    inputPrice.value = '';
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
            div.innerHTML = `
                <input type="text" name="spec_names[]" class="form-control" placeholder="Tên thông số (VD: Dung lượng RAM)" style="flex: 1;">
                <input type="text" name="spec_values[]" class="form-control" placeholder="Giá trị (VD: 16GB)" style="flex: 2;">
                <button type="button" class="btn btn-danger remove-spec-btn" style="padding: 0 15px; border-radius: 4px;">Xóa</button>
            `;

            specsWrapper.appendChild(div);
        });
    }

    if (specsWrapper) {
        specsWrapper.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-spec-btn')) {
                const specItem = e.target.closest('.spec-item');

                if (specItem) {
                    specItem.remove();
                }
            }
        });
    }
});
document.addEventListener("DOMContentLoaded", () => {
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerText = message;
        
        container.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    const btnAddToCart = document.getElementById('btnAddToCart');
    const productForm = document.getElementById('addToCartForm');

    if (btnAddToCart && productForm) {
        btnAddToCart.addEventListener("click", function() {
            const formData = new FormData(productForm);
            formData.append('action_type', 'add_to_cart');

            fetch('../user/action_product_detail/action_product.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast("Đã thêm sản phẩm vào giỏ hàng thành công!", "success");
                } else {
                    showToast(data.message || "Có lỗi xảy ra khi thêm vào giỏ.", "error");
                }
            })
            .catch(error => {
                showToast("Đã gửi yêu cầu thêm vào giỏ hàng!", "success"); 
            });
        });
    }

    const qtyInput = document.getElementById('qty-input');
    const btnMinus = document.querySelector('.qty-btn.minus');
    const btnPlus = document.querySelector('.qty-btn.plus');

    if (qtyInput && btnMinus && btnPlus) {
        btnMinus.addEventListener('click', () => {
            let val = parseInt(qtyInput.value);
            if (val > 1) qtyInput.value = val - 1;
        });
        
        btnPlus.addEventListener('click', () => {
            let val = parseInt(qtyInput.value);
            let max = parseInt(qtyInput.getAttribute('max')) || 999;
            if (val < max) qtyInput.value = val + 1;
        });
    }

    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.thumb-item');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            mainImage.src = productImages[this.dataset.index];
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.target;
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });

    const btnSubmitReview = document.getElementById('btnSubmitReview');
    const reviewForm = document.getElementById('submitReviewForm');

    if (btnSubmitReview && reviewForm) {
        btnSubmitReview.addEventListener('click', function() {
            const comment = document.getElementById('comment').value.trim();

            if (!comment) {
                alert('Vui lòng nhập nội dung đánh giá!');
                return;
            }

            const formData = new FormData(reviewForm);

            fetch('../user/action_product_detail/action_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('Cảm ơn bạn đã gửi đánh giá!');
                        window.location.reload(); 
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                } catch (e) {
                    console.error("Lỗi:", text);
                }
            })
            .catch(error => console.error('Lỗi kết nối:', error));
        });
    }
});
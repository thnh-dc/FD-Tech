document.addEventListener("DOMContentLoaded", function() {
    
    // Xử lý đổi ảnh Gallery ở trang Chi tiết
    const thumbItems = document.querySelectorAll('.thumb-item');
    const mainImg = document.getElementById('mainImgView');

    if (thumbItems.length > 0 && mainImg) {
        thumbItems.forEach(item => {
            item.addEventListener('click', function() {
                // Xóa viền xanh ở ảnh cũ
                document.querySelector('.thumb-item.active').classList.remove('active');
                // Thêm viền xanh vào ảnh vừa click
                this.classList.add('active');
                // Đổi ảnh to
                const newSrc = this.querySelector('img').src;
                mainImg.src = newSrc;
            });
        });
    }

    // Bắt sự kiện 2 nút Mua (Mô phỏng)
    const btnAddCart = document.querySelector('.btn-add-cart');
    const btnBuyNow = document.querySelector('.btn-buy-now');

    if(btnAddCart) {
        btnAddCart.addEventListener('click', () => {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
        });
    }

    if(btnBuyNow) {
        btnBuyNow.addEventListener('click', () => {
            alert('Đang chuyển đến trang Thanh Toán...');
        });
    }
});
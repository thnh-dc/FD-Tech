document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. DARK MODE ---
    const btnDarkmode = document.getElementById('btnDarkmode');
    if(btnDarkmode) {
        if(localStorage.getItem('fd_theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            btnDarkmode.innerText = '☀️ Sáng';
        }
        btnDarkmode.addEventListener('click', () => {
            if(document.documentElement.getAttribute('data-theme') === 'dark') {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('fd_theme', 'light');
                btnDarkmode.innerText = '🌙 Tối';
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('fd_theme', 'dark');
                btnDarkmode.innerText = '☀️ Sáng';
            }
        });
    }

    // --- 2. HỆ THỐNG GIỎ HÀNG MINI (LOCALSTORAGE) ---
    let cart = JSON.parse(localStorage.getItem('fd_cart')) || [];
    const cartBadge = document.getElementById('cartBadge');
    const cartItems = document.getElementById('cartItems');

    function updateCartUI() {
        let totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
        if(cartBadge) cartBadge.innerText = totalQty;

        if(cartItems) {
            if(cart.length === 0) {
                cartItems.innerHTML = '<div style="text-align:center; padding:20px; color:var(--text-muted);">Giỏ hàng trống</div>';
            } else {
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <img src="${item.img}" alt="img">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${item.price.toLocaleString('vi-VN')} ₫ x ${item.qty}</div>
                        </div>
                    </div>
                `).join('');
            }
        }
    }
    updateCartUI();

    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'toast-msg show';
        toast.innerHTML = `✅ ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    // --- 3. XỬ LÝ TRANG CHI TIẾT (ZOOM, TABS, THÊM GIỎ) ---
    // Đổi ảnh
    const thumbs = document.querySelectorAll('.thumb-item');
    const mainImg = document.getElementById('mainImgView');
    if (thumbs.length > 0 && mainImg) {
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                document.querySelector('.thumb-item.active').classList.remove('active');
                this.classList.add('active');
                mainImg.src = this.querySelector('img').src;
            });
        });
    }

    // Hiệu ứng Zoom Kính lúp
    const imgBox = document.getElementById('imgBoxZoom');
    if(imgBox && mainImg) {
        imgBox.addEventListener('mousemove', function(e) {
            const rect = imgBox.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            mainImg.style.transformOrigin = `${x}% ${y}%`;
            mainImg.style.transform = 'scale(2)';
        });
        imgBox.addEventListener('mouseleave', function() {
            mainImg.style.transformOrigin = 'center';
            mainImg.style.transform = 'scale(1)';
        });
    }

    // Số lượng & Thêm giỏ hàng
    const btnMinus = document.getElementById('btnMinus');
    const btnPlus = document.getElementById('btnPlus');
    const qtyInput = document.getElementById('qtyInput');
    const btnAddToCart = document.getElementById('btnAddToCart');

    if (btnMinus && btnPlus && qtyInput) {
        btnMinus.addEventListener('click', () => { if(qtyInput.value > 1) qtyInput.value--; });
        btnPlus.addEventListener('click', () => { if(qtyInput.value < 10) qtyInput.value++; });
    }

    if (btnAddToCart) {
        btnAddToCart.addEventListener('click', () => {
            let id = document.getElementById('pd_id').value;
            let name = document.getElementById('pd_name').value;
            let price = parseInt(document.getElementById('pd_price').value);
            let img = document.getElementById('pd_img').value;
            let qty = parseInt(qtyInput.value);

            let existingItem = cart.find(item => item.id === id);
            if(existingItem) existingItem.qty += qty;
            else cart.push({ id, name, price, img, qty });

            localStorage.setItem('fd_cart', JSON.stringify(cart));
            updateCartUI();
            showToast(`Đã thêm ${qty} sản phẩm vào giỏ!`);
        });
    }

    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    if(tabBtns.length > 0) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.getAttribute('data-target')).classList.add('active');
            });
        });
    }

    // --- 4. XỬ LÝ TRANG DANH SÁCH (LỌC, SẮP XẾP, MODAL) ---
    // Đếm ngược Flash Sale
    const timerH = document.getElementById('hour');
    if (timerH) {
        let time = 7200 + 15 * 60 + 30; // 2h 15m 30s
        setInterval(() => {
            if(time <= 0) return;
            time--;
            document.getElementById('hour').innerText = String(Math.floor(time / 3600)).padStart(2, '0');
            document.getElementById('minute').innerText = String(Math.floor((time % 3600) / 60)).padStart(2, '0');
            document.getElementById('second').innerText = String(time % 60).padStart(2, '0');
        }, 1000);
    }

    // Sắp xếp sản phẩm
    const sortSelect = document.getElementById('sortSelect');
    const productGrid = document.getElementById('productGrid');
    if (sortSelect && productGrid) {
        sortSelect.addEventListener('change', function() {
            let cards = Array.from(productGrid.querySelectorAll('.fpt-card'));
            let val = this.value;
            cards.sort((a, b) => {
                let priceA = parseInt(a.getAttribute('data-price'));
                let priceB = parseInt(b.getAttribute('data-price'));
                if(val === 'asc') return priceA - priceB;
                if(val === 'desc') return priceB - priceA;
                return 0;
            });
            productGrid.innerHTML = '';
            cards.forEach(card => productGrid.appendChild(card));
        });
    }

    // Lọc theo Category
    const filterBtns = document.querySelectorAll('.filter-btn');
    if(filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                let cat = this.getAttribute('data-cat');
                document.querySelectorAll('.fpt-card').forEach(card => {
                    if(cat === 'all' || card.getAttribute('data-cat') === cat) card.style.display = 'flex';
                    else card.style.display = 'none';
                });
            });
        });
    }

    // Modal Xem Nhanh
    const modal = document.getElementById('quickViewModal');
    if(modal) {
        document.querySelectorAll('.btn-quickview').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const card = this.closest('.fpt-card');
                document.getElementById('modalImg').src = card.getAttribute('data-img');
                document.getElementById('modalName').innerText = card.getAttribute('data-name');
                document.getElementById('modalPrice').innerText = parseInt(card.getAttribute('data-price')).toLocaleString('vi-VN') + ' ₫';
                document.getElementById('modalDesc').innerHTML = card.getAttribute('data-desc');
                modal.classList.add('active');
            });
        });
        document.querySelector('.btn-close').addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.remove('active'); });
    }
});
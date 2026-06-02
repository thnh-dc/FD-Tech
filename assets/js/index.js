// Hàm ẩn popup khi người dùng bấm nút X hoặc vùng ngoài
function closePopup(event) {
    if (event) {
        event.stopPropagation();
    }
    const popup = document.getElementById("advPopup");
    if (popup) {
        popup.style.display = "none";
    }
}

// Bấm ra ngoài vùng ảnh (vùng nền đen mờ) cũng tự tắt popup
window.addEventListener('click', function(event) {
    const modal = document.getElementById("advPopup");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

document.addEventListener("DOMContentLoaded", function() {
    // Gọi API lấy toàn bộ dữ liệu banner/popup đang hoạt động (status = 1)
    fetch('../admin/api_images.php?action=list&client=1')
    .then(response => response.json())
    .then(data => {
        
        // 1. XỬ LÝ ĐỔI ẢNH POPUP ĐỘNG
        const popups = data.filter(item => item.type === 'popup');
        const advPopupElement = document.getElementById('advPopup');
        
        if (popups.length > 0 && advPopupElement) {
            const activePopup = popups[0]; 
            const popupImg = advPopupElement.querySelector('.adv-popup-img');
            
            if (popupImg) {
                popupImg.src = activePopup.image_url;
                popupImg.onclick = function() {
                    if(activePopup.link_to) window.location.href = activePopup.link_to;
                };
            }
        } else {
            if (advPopupElement) advPopupElement.style.display = 'none';
        }

        // 2. XỬ LÝ ĐỔI ẢNH BANNER ĐỘNG & TẠO HIỆU ỨNG DI CHUYỂN
        const banners = data.filter(item => item.type === 'banner');
        const bannerTrack = document.getElementById('bannerTrack');
        
        if (banners.length > 0 && bannerTrack) {
            let bannerHTML = '';
            banners.forEach(banner => {
                bannerHTML += `
                    <div class="banner-slide">
                        ${banner.link_to ? `<a href="${banner.link_to}">` : ''}
                            <img src="${banner.image_url}">
                        ${banner.link_to ? `</a>` : ''}
                    </div>
                `;
            });
            // Ghi đè toàn bộ cụm slide cũ bằng danh sách banner động từ Database
            bannerTrack.innerHTML = bannerHTML;

            // KÍCH HOẠT HIỆU ỨNG TỰ ĐỘNG DI CHUYỂN (SLIDER RUNNER)
            initBannerSlider(banners.length);
        }
    })
    .catch(err => console.error("Lỗi tải banner/popup động:", err));
});

// HÀM XỬ LÝ CHẠY SLIDE BANNER TỰ ĐỘNG
function initBannerSlider(totalSlides) {
    const track = document.getElementById('bannerTrack');
    if (!track || totalSlides <= 1) return; // Nếu chỉ có 1 ảnh thì không cần chạy

    let currentIndex = 0;

    // Thiết lập thời gian tự động chuyển slide (3000ms = 3 giây)
    setInterval(() => {
        currentIndex++;
        if (currentIndex >= totalSlides) {
            currentIndex = 0; // Quay về tấm ảnh đầu tiên nếu đi hết danh sách
        }
        
        // Di chuyển thanh track sang trái dựa trên tỷ lệ % của ảnh hiện tại
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        track.style.transition = 'transform 0.5s ease-in-out'; // Tạo hiệu ứng lướt mượt mà
    }, 3000); 
}
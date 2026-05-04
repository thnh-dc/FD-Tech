// assets/js/banner.js
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('bannerTrack');
    const slides = document.querySelectorAll('.banner-slide');
    let index = 0;

    function moveSlide() {
        if (!track) return; // Kiểm tra an toàn nếu không tìm thấy phần tử
        index++;
        if (index >= slides.length) index = 0;
        track.style.transform = `translateX(-${index * 100}%)`;
    }

    // Tự động chuyển mỗi 3 giây
    setInterval(moveSlide, 3000);
});
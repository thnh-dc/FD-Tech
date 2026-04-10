document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById('deliveryModal');
    const btnOpen = document.getElementById('btn-open-delivery');
    const btnClose = document.querySelector('.modal-close');
    const btnConfirm = document.getElementById('btn-confirm-delivery');
    const displayBox = document.getElementById('display-delivery');
    const modalContent = document.getElementById('modal-content-box');

     document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById('deliveryModal');
            const btnOpen = document.getElementById('btn-open-delivery');
            const btnClose = document.getElementById('btn-close-modal');
            const btnConfirm = document.getElementById('btn-confirm-delivery');
            const displayBox = document.getElementById('display-delivery');
            const modalContent = document.getElementById('modal-content-box');

            // 1. Click vào khung Giao hàng -> Mở Modal
            if (btnOpen) {
                btnOpen.addEventListener('click', () => {
                    modal.style.display = 'flex';
                });
            }

            // 2. Click dấu X -> Đóng Modal
            if (btnClose) {
                btnClose.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            }

            // 3. Click ra ngoài viền đen -> Đóng Modal
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Ngăn việc click vào bên trong modal bị đóng
            if (modalContent) {
                modalContent.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            // 4. Click nút Xác nhận thông tin
            if (btnConfirm) {
                btnConfirm.addEventListener('click', () => {
                    const name = document.getElementById('input-fullname').value;
                    const phone = document.getElementById('input-phone').value;
                    const address = document.getElementById('input-address').value;

                    // Kiểm tra xem đã nhập đủ chưa
                    if(name.trim() !== '' && phone.trim() !== '' && address.trim() !== '') {
                        // In thông tin ra ngoài màn hình chính
                        displayBox.innerHTML = `
                            <p style="color: var(--text-dark); margin-bottom: 5px;"><strong>Người nhận:</strong> ${name} - ${phone}</p>
                            <p style="color: var(--text-dark);"><strong>Giao đến:</strong> ${address}</p>
                        `;
                        // Ẩn modal đi
                        modal.style.display = 'none';
                    } else {
                        alert("Vui lòng nhập đầy đủ Họ tên, Số điện thoại và Địa chỉ!");
                    }
                });
            }
        });
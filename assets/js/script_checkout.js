document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById('deliveryModal');
    const btnOpen = document.getElementById('btn-open-delivery');
    const btnClose = document.querySelector('.modal-close');
    const btnConfirm = document.getElementById('btn-confirm-delivery');
    const displayBox = document.getElementById('display-delivery');
    const modalContent = document.getElementById('modal-content-box');

    // Chỉ chạy nếu đang ở trang checkout (có tồn tại modal)
    if (modal && btnOpen) {
        
        // 1. Mở Modal
        btnOpen.addEventListener('click', () => {
            modal.style.display = 'flex';
        });

        // 2. Đóng Modal khi bấm dấu X
        if (btnClose) {
            btnClose.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        // 3. Đóng Modal khi click ra ngoài vùng xám
        window.addEventListener('click', (e) => {
            if(e.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Ngăn việc click vào bên trong modal bị đóng
        if (modalContent) {
            modalContent.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // 4. Xử lý khi bấm nút "Xác nhận thông tin"
        const confirmButton = btnConfirm || modal.querySelector('.btn-primary');
        
        if (confirmButton) {
            confirmButton.addEventListener('click', () => {
                const name = document.getElementById('input-fullname').value;
                const phone = document.getElementById('input-phone').value;
                const address = document.getElementById('input-address').value;

                if(name.trim() !== '' && phone.trim() !== '' && address.trim() !== '') {
                    displayBox.innerHTML = `
                        <p style="color: var(--text-dark); margin-bottom: 5px;"><strong>Người nhận:</strong> ${name} - ${phone}</p>
                        <p style="color: var(--text-dark);"><strong>Giao đến:</strong> ${address}</p>
                    `;
                    modal.style.display = 'none'; // Đóng modal
                } else {
                    alert("Vui lòng nhập đầy đủ Họ tên, Số điện thoại và Địa chỉ!");
                }
            });
        }
    }
});
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý sự kiện gửi form cập nhật thông tin vận chuyển
    document.addEventListener("submit", function (e) {
        if (e.target && e.target.classList.contains("shipping-submit-form")) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector(".btn-submit-shipping");
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            fetch("action_list_order/update_shipping_info.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Cập nhật thông tin vận chuyển thành công!");
                    // Tìm phần tử Timeline thuộc đơn hàng này để cập nhật trực quan nếu cần
                    location.reload(); 
                } else {
                    alert("Lỗi: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Đã xảy ra lỗi hệ thống khi kết nối kết quả!");
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    });
});
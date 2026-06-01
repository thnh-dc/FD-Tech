-- 1. CẬP NHẬT THÊM CỘT BẢO HÀNH VÀO BẢNG DANH MỤC
-- Tự động cấu hình thời gian bảo hành mặc định là 12 tháng cho mỗi nhóm sản phẩm
ALTER TABLE categories 
ADD COLUMN warranty_months INT DEFAULT 12 AFTER slug;

-- 2. TẠO BẢNG QUẢN LÝ YÊU CẦU HẬU MÃI (ĐỔI TRẢ & BẢO HÀNH)
-- Lưu trữ tập trung toàn bộ hồ sơ, dữ liệu, hình ảnh và video minh chứng từ khách hàng
CREATE TABLE IF NOT EXISTS order_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    request_type ENUM('return', 'warranty') NOT NULL,
    reason VARCHAR(255) NOT NULL,
    description TEXT,
    images TEXT NOT NULL,             -- Chuỗi lưu đường dẫn nhiều ảnh (cách nhau bằng dấu phẩy)
    video VARCHAR(255) DEFAULT NULL,   -- Đường dẫn lưu 1 video thực tế chứng minh lỗi
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Thiết lập các ràng buộc khóa ngoại để đồng bộ dữ liệu
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
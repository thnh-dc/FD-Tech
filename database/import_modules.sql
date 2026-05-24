-- 1. BẢNG NHÀ CUNG CẤP
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên nhà cung cấp',
    phone VARCHAR(20) NULL COMMENT 'Số điện thoại',
    address TEXT NULL COMMENT 'Địa chỉ công ty',
    email VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG ĐƠN NHẬP HÀNG (THÔNG TIN CHUNG)
CREATE TABLE IF NOT EXISTS import_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    admin_id INT NOT NULL COMMENT 'ID của Admin thực hiện nhập kho',
    total_amount DECIMAL(15, 2) DEFAULT 0 COMMENT 'Tổng tiền toàn bộ đơn nhập',
    status VARCHAR(50) DEFAULT 'draft' COMMENT 'draft: Nháp, completed: Đã nhập kho, cancelled: Đã hủy',
    note TEXT NULL COMMENT 'Ghi chú phiếu nhập',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG CHI TIẾT ĐƠN NHẬP HÀNG (DANH SÁCH SẢN PHẨM NHẬP)
CREATE TABLE IF NOT EXISTS import_order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL COMMENT 'Số lượng nhập',
    import_price DECIMAL(15, 2) NOT NULL COMMENT 'Giá vốn mua vào',
    FOREIGN KEY (import_order_id) REFERENCES import_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
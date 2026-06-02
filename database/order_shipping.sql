-- ==========================================
-- BẢNG QUẢN LÝ VẬN CHUYỂN ĐƠN HÀNG
-- FD TECH
-- ==========================================

CREATE TABLE order_shipping (
    id INT AUTO_INCREMENT PRIMARY KEY,

    order_id INT NOT NULL,

    carrier_name VARCHAR(100) NOT NULL COMMENT 'GHN, GHTK, Viettel Post...',

    tracking_number VARCHAR(100) NOT NULL COMMENT 'Mã vận đơn',

    shipping_cost DECIMAL(10,2) DEFAULT 0 COMMENT 'Phí vận chuyển',

    estimated_delivery DATE NULL COMMENT 'Ngày giao dự kiến',

    notes TEXT NULL COMMENT 'Ghi chú vận chuyển',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_shipping_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    UNIQUE KEY uk_tracking_number (tracking_number),

    INDEX idx_order_id (order_id),

    INDEX idx_carrier (carrier_name)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
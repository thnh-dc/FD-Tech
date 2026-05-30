-- ==========================================================================
-- FD TECH SYSTEM - LOGISTICS & SHIPPING SUBSYSTEM
-- Author: Admin FD Tech
-- Date: 30/05/2026
-- Description: Khởi tạo bảng lưu trữ thông tin vận đơn bên thứ 3
-- ==========================================================================

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `order_shipping` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL COMMENT 'Mã liên kết bảng orders',
  `carrier_name` VARCHAR(100) NOT NULL COMMENT 'Đơn vị vận chuyển (GHTK, GHN, Viettel Post...)',
  `tracking_number` VARCHAR(100) NOT NULL COMMENT 'Mã vận đơn tracking bưu cục',
  `shipping_cost` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Phí vận chuyển thực tế hệ thống chịu',
  `estimated_delivery` DATE NULL COMMENT 'Ngày dự kiến giao tới tay khách hàng',
  `notes` TEXT NULL COMMENT 'Ghi chú lộ trình bưu cục di chuyển',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
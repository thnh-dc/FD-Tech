CREATE TABLE fd_member_tiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tier_key VARCHAR(50) UNIQUE NOT NULL,
    tier_name VARCHAR(100) NOT NULL,
    min_period_points INT NOT NULL DEFAULT 0,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    free_shipping TINYINT(1) DEFAULT 0,
    description TEXT,
    sort_order INT DEFAULT 0
);

INSERT INTO fd_member_tiers 
(tier_key, tier_name, min_period_points, discount_percent, free_shipping, description, sort_order)
VALUES
('bronze', 'Đồng', 0, 0, 0, 'Hạng thành viên cơ bản của FD Tech.', 1),
('silver', 'Bạc', 500, 2, 0, 'Giảm 2% cho đơn hàng.', 2),
('gold', 'Vàng', 1500, 5, 0, 'Giảm 5% cho đơn hàng.', 3),
('diamond', 'Kim cương', 3000, 8, 1, 'Giảm 8% cho đơn hàng và hỗ trợ miễn phí vận chuyển.', 4);

CREATE TABLE fd_point_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT NULL,
    type ENUM('earn', 'redeem', 'refund', 'adjust') NOT NULL,
    points INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);
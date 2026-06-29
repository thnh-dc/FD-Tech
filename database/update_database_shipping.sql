ALTER TABLE order_shipping
ADD COLUMN shipping_status ENUM('preparing','shipping','delivered') NOT NULL DEFAULT 'preparing' AFTER order_id,
ADD COLUMN delivered_at DATETIME NULL AFTER estimated_delivery;
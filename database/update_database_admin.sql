ALTER TABLE users ADD COLUMN admin_pin VARCHAR(255) DEFAULT NULL AFTER role;
INSERT INTO users (
    username, 
    full_name, 
    password, 
    email, 
    phone, 
    role, 
    admin_pin, 
    status
) 
VALUES (
    'admin', 
    'Quản trị viên', 
    '$2y$10$/OErnS/ifT0rIDHpxx581Ovbc76RBLiTQ0tiWhC8GgH5/9nY7P8VS',  -- Hash của 'admin123'
    'admin@gmail.com', 
    '19001000', 
    'admin', 
    '$2y$10$evRk/NDcKBw/6BndbY0PJuLSGUtnuiIxtRXCFnNivMAol2aKnHfV6',  -- Hash của '888888'
    'active'
);
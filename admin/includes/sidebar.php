<link rel="stylesheet" href="/FD-Tech/assets/css/style_sidebar.css">

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>FD TECH</h2>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active' : '' ?>">
            <a href="/FD-Tech/admin/admin_dashboard.php">
                <i class="fa-solid fa-chart-pie"></i> Thống kê
            </a>
        </li>

        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'list_order.php') ? 'active' : '' ?>">
            <a href="/FD-Tech/admin/list_order.php">
                <i class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng
            </a>
        </li>

        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage_requests.php') ? 'active' : '' ?>">
            <a href="/FD-Tech/admin/manage_requests.php">
                <i class="fa-solid fa-clipboard-question"></i> Quản lý yêu cầu
            </a>
        </li>

        <?php
            $user_pages = ['chat_list.php', 'chat_detail.php', 'list_users.php', 'user_detail.php'];
            $is_user_active = in_array(basename($_SERVER['PHP_SELF']), $user_pages);
        ?>
        <li class="menu-item has-submenu">
            <a href="#" class="submenu-toggle">
                <i class="fa-solid fa-users"></i> Quản lý người dùng
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </a>

            <ul class="submenu <?= $is_user_active ? 'show' : '' ?>">
                <li>
                    <a href="/FD-Tech/admin/chat_list.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'chat_list.php' || basename($_SERVER['PHP_SELF']) == 'chat_detail.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-comments"></i> Tin nhắn khách hàng
                    </a>
                </li>
                <li>
                    <a href="#" class="<?= (basename($_SERVER['PHP_SELF']) == '#' || basename($_SERVER['PHP_SELF']) == '#') ? 'active-sub' : '' ?>">
                        <i class="fa-regular fa-comment"></i> Đánh giá khách hàng
                    </a>
                </li>
                <li>
                    <a href="/FD-Tech/admin/list_users.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'list_users.php' || basename($_SERVER['PHP_SELF']) == 'user_detail.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-user-gear"></i> Tài khoản người dùng
                    </a>
                </li>
            </ul>
        </li>

        <?php 
            $product_pages = ['add.php', 'edit.php', 'list_products.php', 'manage_warehouse.php'];
            $is_product_active = in_array(basename($_SERVER['PHP_SELF']), $product_pages);
        ?>
        <li class="menu-item has-submenu">
            <a href="#" class="submenu-toggle">
                <i class="fa-solid fa-box-open"></i> Danh mục sản phẩm
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </a>
            <ul class="submenu <?= $is_product_active ? 'show' : '' ?>">
                <li>
                    <a href="/FD-Tech/admin/add.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                    </a>
                </li>
                <li>
                    <a href="/FD-Tech/admin/list_products.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'list_products.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-list"></i> Quản lý sản phẩm
                    </a>
                </li>
                <li>
                    <a href="/FD-Tech/admin/manage_warehouse.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage_warehouse.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-warehouse"></i> Quản lý kho hàng
                    </a>
                </li>
            </ul>
        </li>

        <?php 
            $import_pages = ['add_import.php', 'list_imports.php', 'view_import.php'];
            $is_import_active = in_array(basename($_SERVER['PHP_SELF']), $import_pages);
        ?>
        <li class="menu-item has-submenu">
            <a href="#" class="submenu-toggle">
                <i class="fa-solid fa-boxes-packing"></i> Quản lý nhập hàng
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </a>
            <ul class="submenu <?= $is_import_active ? 'show' : '' ?>">
                <li>
                    <a href="/FD-Tech/admin/add_import.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add_import.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-plus"></i> Tạo phiếu nhập
                    </a>
                </li>
                <li>
                    <a href="/FD-Tech/admin/list_imports.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'list_imports.php') ? 'active-sub' : '' ?>">
                        <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử nhập kho
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage_images.php') ? 'active' : '' ?>">
            <a href="/FD-Tech/admin/manage_images.php"><i class="fa-solid fa-images"></i> Quản lí banner, popup</a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="/FD-Tech/auth/logout.php" class="btn btn-danger logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
        </a>
    </div>
</aside>
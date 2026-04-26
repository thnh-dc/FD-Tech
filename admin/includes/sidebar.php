<link rel="stylesheet" href="../assets/css/style_sidebar.css">

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>FD TECH</h2>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active' : '' ?>">
            <a href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
        </li>

        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'list_order.php') ? 'active' : '' ?>">
            <a href="list_order.php"><i class="fa-solid fa-cart-shopping"></i> Danh sách đơn hàng</a>
        </li>

        <?php 
            $product_pages = ['add.php', 'update.php', 'delete.php'];
            $is_product_active = in_array(basename($_SERVER['PHP_SELF']), $product_pages);
        ?>
        <li class="menu-item has-submenu <?= $is_product_active ? 'rotate-arrow' : '' ?>">
            <a href="#" class="submenu-toggle">
                <i class="fa-solid fa-box-open"></i> Danh mục sản phẩm
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </a>
            <ul class="submenu <?= $is_product_active ? 'show' : '' ?>">
                <li><a href="add.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add.php') ? 'active-sub' : '' ?>"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</a></li>
                <li><a href="list_product.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'delete.php') ? 'active-sub' : '' ?>"><i class="fa-solid fa-trash"></i> Quản lí sản phẩm</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="btn btn-danger logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
        </a>
    </div>
</aside>
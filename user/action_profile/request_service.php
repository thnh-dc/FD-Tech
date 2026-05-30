<?php
// FILE: D:\xam\htdocs\FD-Tech\user\action_profile\request_service.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kết nối database
require_once '../../config/database.php';

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    header("Location: ../../auth/login.php");
    exit();
}

// =========================================================================
// 🔥 KHỐI XỬ LÝ LƯU DATABASE (Giữ nguyên logic của bạn)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int) ($_POST['order_id'] ?? 0);
    $product_id = (int) ($_POST['product_id'] ?? 0);
    $request_type = trim($_POST['request_type'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($order_id <= 0 || $product_id <= 0 || empty($request_type) || empty($reason)) {
        die("Dữ liệu gửi lên không hợp lệ.");
    }

    $upload_dir = '../../upload/evidences/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_images = [];
    $uploaded_video = null;

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $val) {
            $file_name = time() . '_img_' . basename($_FILES['images']['name'][$key]);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                $uploaded_images[] = 'uploads/evidences/' . $file_name;
            }
        }
    }

    if (!empty($_FILES['video']['name'])) {
        $file_name = time() . '_vid_' . basename($_FILES['video']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
            $uploaded_video = 'uploads/evidences/' . $file_name;
        }
    }

    $images_string = implode(',', $uploaded_images);

    try {
        $stmtInsert = $pdo->prepare("
            INSERT INTO order_requests (order_id, user_id, product_id, request_type, reason, description, images, video)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtInsert->execute([$order_id, $user_id, $product_id, $request_type, $reason, $description, $images_string, $uploaded_video]);

        echo "<script>
            alert('Gửi yêu cầu hậu mãi thành công!');
            window.location.href = '../profile.php?action=orders'; 
        </script>";
        exit();
    } catch (Exception $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}

// =========================================================================
// 🖥️ KHỐI TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ (ĐÃ CẬP NHẬT TÍNH TỪ UPDATED_AT)
// =========================================================================
$order_id = (int) ($_GET['order_id'] ?? 0);

// Thay vì o.created_at, dùng IF(status='completed', updated_at, created_at) để tính số ngày đã qua công bằng cho khách
$stmtOrder = $pdo->prepare("
    SELECT *, 
           DATEDIFF(NOW(), IF(status = 'completed', updated_at, created_at)) AS days_passed 
    FROM orders 
    WHERE id = ? AND user_id = ?
");
$stmtOrder->execute([$order_id, $user_id]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("<h3 style='color:#ff0019; text-align:center; padding:50px;'>Đơn hàng không tồn tại hoặc mã truy cập sai!</h3>");
}

$stmtItems = $pdo->prepare("
    SELECT oi.*, c.warranty_months 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE oi.order_id = ?
");
$stmtItems->execute([$order_id]);
$orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

$can_return = ($order['days_passed'] <= 7);

// =========================================================================
// Bạn hãy kiểm tra lại đường dẫn chính xác của file header và sidebar nhé!
require_once '../../includes/header.php';
?>

<style>
    .service-request-container {
        background: #FFFFFF;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 6px 6px rgba(1, 0, 0, 0.5);
        margin: 10px auto;
        max-width: 100%;
    }

    .service-header-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0B2A4A;
        padding-bottom: 12px;
    }

    .service-request-container h2 {
        color: #0B2A4A;
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }

    .btn-back-link {
        color: #6C757D;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }

    .btn-back-link:hover {
        color: #0B2A4A;
    }

    .service-request-container .order-meta {
        margin-top: 10px;
        font-size: 14px;
        color: #6C757D;
    }

    .service-request-container .order-meta strong {
        color: #0B2A4A;
    }

    /* Tabs tinh chỉnh hợp với layout nhỏ gọn bên trong Profile */
    .service-tabs {
        display: flex;
        border-bottom: 2px solid #E5E7EB;
        margin: 20px 0;
    }

    .service-tab-btn {
        flex: 1;
        padding: 12px;
        text-align: center;
        background: #E5E7EB;
        border: none;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        color: #6C757D;
        transition: all 0.3s;
    }

    .service-tab-btn.active {
        background: #0B2A4A;
        color: #FFFFFF;
    }

    .service-tab-content {
        display: none;
    }

    .service-tab-content.active {
        display: block;
    }

    /* Form Fields */
    .service-form-group {
        margin-bottom: 18px;
    }

    .service-form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 14px;
        color: #333333;
    }

    .service-form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
        color: #333333;
        background: #FFFFFF;
        resize: none; /* 🔥 Đã chặn tính năng kéo dãn tự do của khung điền mô tả */
    }

    .service-form-control:focus {
        outline: none;
        border-color: #23B5D3;
    }

    /* Buttons */
    .service-action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .service-btn-submit {
        background: #0B2A4A;
        color: #FFFFFF;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        flex: 3;
        transition: 0.3s;
        text-align: center;
    }

    .service-btn-submit:hover {
        opacity: 0.85;
    }

    #tab-warranty .service-btn-submit {
        background: #23B5D3;
    }

    .service-btn-cancel {
        background: #F8F9FA;
        color: #495057;
        border: 1px solid #CED4DA;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        flex: 1;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
    }

    .service-btn-cancel:hover {
        background: #E2E6EA;
        color: #212529;
    }

    .service-error-box {
        background: #FFF5F5;
        color: #ff0019;
        padding: 15px;
        border: 1px solid #FED7D7;
        border-radius: 4px;
        font-weight: 600;
        text-align: center;
        font-size: 14px;
    }
</style>

<div class="profile-layout-wrapper container">
    <div class="row">
        <?php if (file_exists('../includes/sidebar.php')) {
            include '../includes/sidebar.php';
        } ?>

        <div class="profile-main-content col-md-9 col-sm-12">
            <div class="service-request-container">

                <div class="service-header-box">
                    <h2>Trung Tâm Hỗ Trợ Đơn Hàng #<?= $order_id ?></h2>
                    <a href="../profile.php?action=orders" class="btn-back-link">&#10229; Trở lại</a>
                </div>
                
                <div class="order-meta">
                    (Ngày nhận hàng: <?= date('d/m/Y', strtotime($order['status'] === 'completed' ? $order['updated_at'] : $order['created_at'])) ?>)
                </div>

                <div class="service-tabs">
                    <button class="service-tab-btn active" onclick="switchServiceTab(event, 'tab-return')">1. Yêu Cầu Hoàn Hàng</button>
                    <button class="service-tab-btn" onclick="switchServiceTab(event, 'tab-warranty')">2. Yêu Cầu Bảo Hành</button>
                </div>

                <div id="tab-return" class="service-tab-content active">
                    <?php if (!$can_return): ?>
                        <div class="service-error-box" style="margin-bottom: 20px;">
                            Rất tiếc, đơn hàng đã vượt quá giới hạn 7 ngày đổi trả hoàn tiền tính từ lúc bạn nhận hàng.
                        </div>
                        <div style="text-align: center;">
                            <a href="../profile.php?action=orders" class="service-btn-cancel" style="display: inline-block; width: auto; padding: 10px 30px;">Quay lại danh sách đơn hàng</a>
                        </div>
                    <?php else: ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="order_id" value="<?= $order_id ?>">
                            <input type="hidden" name="request_type" value="return">

                            <div class="service-form-group">
                                <label>Sản phẩm cần hoàn trả:</label>
                                <select class="service-form-control" name="product_id" required>
                                    <?php foreach ($orderItems as $item): ?>
                                        <option value="<?= $item['product_id'] ?>">
                                            <?= htmlspecialchars($item['product_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="service-form-group">
                                <label>Lý do hoàn hàng:</label>
                                <select class="service-form-control" name="reason" required>
                                    <option value="">-- Chọn lý do thích hợp --</option>
                                    <option value="Sản phẩm lỗi bể vỡ / móp méo khi vận chuyển">Sản phẩm lỗi bể vỡ / móp méo khi vận chuyển</option>
                                    <option value="Giao sai sản phẩm / sai màu sắc so với đơn đặt">Giao sai sản phẩm / sai màu sắc so với đơn đặt</option>
                                    <option value="Sản phẩm không chạy, lỗi kỹ thuật ngay khi mở hộp">Sản phẩm không chạy, lỗi kỹ thuật ngay khi mở hộp</option>
                                </select>
                            </div>

                            <div class="service-form-group">
                                <label>Mô tả chi tiết lỗi:</label>
                                <textarea class="service-form-control" name="description" rows="5"
                                    placeholder="Vui lòng mô tả cụ thể trạng thái hư hỏng..." required></textarea>
                            </div>

                            <div class="service-form-group">
                                <label>Hình ảnh minh chứng (Hỗ trợ chọn nhiều ảnh):</label>
                                <input type="file" name="images[]" accept="image/*" multiple required>
                            </div>

                            <div class="service-form-group">
                                <label>Video minh chứng lỗi (Tối đa 1 video):</label>
                                <input type="file" name="video" accept="video/*">
                            </div>

                            <div class="service-action-buttons">
                                <a href="../profile.php?action=orders" class="service-btn-cancel">Hủy bỏ</a>
                                <button type="submit" class="service-btn-submit">Gửi Yêu Cầu Hoàn Hàng</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <div id="tab-warranty" class="service-tab-content">
                    <form action="" method="POST" enctype="multipart/form-data" id="warrantyForm">
                        <input type="hidden" name="order_id" value="<?= $order_id ?>">
                        <input type="hidden" name="request_type" value="warranty">

                        <div class="service-form-group">
                            <label>Sản phẩm cần gửi bảo hành:</label>
                            <select class="service-form-control" name="product_id" id="warranty_product"
                                onchange="checkWarrantyDeadline()" required>
                                <option value="">-- Chọn sản phẩm cần kiểm định --</option>
                                <?php foreach ($orderItems as $item):
                                    $months = $item['warranty_months'] ?? 12;
                                    $is_expired = ($order['days_passed'] > ($months * 30));
                                    ?>
                                    <option value="<?= $item['product_id'] ?>"
                                        data-expired="<?= $is_expired ? 'true' : 'false' ?>" data-months="<?= $months ?>">
                                        <?= htmlspecialchars($item['product_name']) ?> (Hạn BH: <?= $months ?> Tháng)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="warranty_fields_area">
                            <div class="service-form-group">
                                <label>Tình trạng lỗi thiết bị phần cứng:</label>
                                <select class="service-form-control" name="reason" required>
                                    <option value="">-- Chọn hiện tượng hư hỏng --</option>
                                    <option value="Mất nguồn / Thiết bị hoàn toàn không vào điện">Mất nguồn / Thiết bị hoàn toàn không vào điện</option>
                                    <option value="Thiết bị mất kết nối / Chập chờn tín hiệu liên tục">Thiết bị mất kết nối / Chập chờn tín hiệu liên tục</option>
                                    <option value="Lỗi linh kiện phần cứng bên trong từ nhà sản xuất">Lỗi linh kiện phần cứng bên trong từ nhà sản xuất</option>
                                </select>
                            </div>

                            <div class="service-form-group">
                                <label>Mô tả chi tiết quá trình phát sinh lỗi:</label>
                                <textarea class="service-form-control" name="description" rows="5"
                                    placeholder="Mô tả quá trình vận hành phát sinh sự cố..." required></textarea>
                            </div>

                            <div class="service-form-group">
                                <label>Hình ảnh trạng thái thiết bị hiện tại:</label>
                                <input type="file" name="images[]" accept="image/*" multiple required>
                            </div>

                            <div class="service-form-group">
                                <label>Video ghi nhận lỗi kỹ thuật (Nếu có):</label>
                                <input type="file" name="video" accept="video/*">
                            </div>

                            <div class="service-action-buttons">
                                <a href="../profile.php?action=orders" class="service-btn-cancel">Hủy bỏ</a>
                                <button type="submit" class="service-btn-submit">Gửi Yêu Cầu Bảo Hành</button>
                            </div>
                        </div>

                        <div id="warranty_error_area" class="service-error-box" style="display:none; margin-bottom: 20px;"></div>
                        <div id="warranty_error_btn" style="display:none; text-align: center;">
                            <a href="../profile.php?action=orders" class="service-btn-cancel" style="display: inline-block; width: auto; padding: 10px 30px;">Quay lại danh sách đơn hàng</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function switchServiceTab(evt, tabId) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("service-tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
        tablinks = document.getElementsByClassName("service-tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
        document.getElementById(tabId).style.display = "block";
        evt.currentTarget.className += " active";
    }

    function checkWarrantyDeadline() {
        var select = document.getElementById('warranty_product');
        var selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) return;

        var isExpired = selectedOption.getAttribute('data-expired');
        var months = selectedOption.getAttribute('data-months');
        var fieldsArea = document.getElementById('warranty_fields_area');
        var errorArea = document.getElementById('warranty_error_area');
        var errorBtn = document.getElementById('warranty_error_btn');

        if (isExpired === 'true') {
            fieldsArea.style.display = 'none';
            errorArea.style.display = 'block';
            errorBtn.style.display = 'block';
            errorArea.innerText = 'Sản phẩm kỹ thuật này đã vượt quá thời hạn bảo hành (' + months + ' tháng kể từ thời điểm nhận hàng).';
        } else {
            fieldsArea.style.display = 'block';
            errorArea.style.display = 'none';
            errorBtn.style.display = 'none';
        }
    }
    checkWarrantyDeadline();
</script>

<?php
// 3. NHÚNG FOOTER DÙNG CHUNG CỦA TRANG
require_once '../../includes/footer.php';
?>
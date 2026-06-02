<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once __DIR__ . '/check_admin.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

function send_order_request_email($to_email, $username, $request_id, $request_type, $status, $reject_reason = '') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fdtechshop@gmail.com'; 
        $mail->Password   = 'nbji avpn bnef drwd'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('fdtechshop@gmail.com', 'FD Tech Support');
        $mail->addAddress($to_email); 
        $mail->isHTML(true);

        $typeName = ($request_type === 'warranty') ? 'BẢO HÀNH' : 'ĐỔI TRẢ / HOÀN TIỀN';
        $mail->Subject = "[FD TECH] Kết quả xử lý yêu cầu $typeName #REQ-$request_id";

        if ($status === 'approved') {
            $status_html = "<span style='color: #166534; font-weight: bold;'>ĐÃ ĐƯỢC PHÊ DUYỆT</span>";
            $content_html = "
                <p style='color: #333; font-size: 14px; line-height: 1.6;'>
                    Xin chào <b>{$username}</b>,<br><br>
                    Yêu cầu <b>{$typeName}</b> của bạn (<b>#REQ-{$request_id}</b>) đã được bộ phận kỹ thuật kiểm tra và <b>chấp thuận</b> thành công.
                </p>
                <div style='background-color: #dcfce7; border-left: 4px solid #166534; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                    <h4 style='margin: 0 0 8px 0; color: #166534; font-size: 14px;'> BƯỚC TIẾP THEO DÀNH CHO BẠN:</h4>
                    <p style='margin: 0; font-size: 13px; color: #166534; line-height: 1.5;'>
                        Nhân viên chăm sóc khách hàng của FD TECH sẽ chủ động liên hệ trực tiếp đến số điện thoại đăng ký tài khoản của bạn trong vòng 24 giờ tới để hướng dẫn chi tiết thủ tục bàn giao, nhận máy hoặc hoàn trả tiền mặt.
                    </p>
                </div>
            ";
        } else {
            $status_html = "<span style='color: #991b1b; font-weight: bold;'>BỊ TỪ CHỐI</span>";
            $content_html = "
                <p style='color: #333; font-size: 14px; line-height: 1.6;'>
                    Xin chào <b>{$username}</b>,<br><br>
                    Chúng tôi rất tiếc phải thông báo yêu cầu <b>{$typeName}</b> của bạn (<b>#REQ-{$request_id}</b>) <b>chưa được phê duyệt</b> do chưa đáp ứng đủ điều kiện chính sách hậu mãi của cửa hàng.
                </p>
                <div style='background-color: #fee2e2; border-left: 4px solid #991b1b; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                    <h4 style='margin: 0 0 8px 0; color: #991b1b; font-size: 14px;'> LÝ DO TỪ CHỐI:</h4>
                    <p style='margin: 0; font-size: 13px; color: #991b1b; font-style: italic; line-height: 1.5;'>
                        \"" . htmlspecialchars($reject_reason, ENT_QUOTES, 'UTF-8') . "\"
                    </p>
                </div>
                <p style='color: #666; font-size: 13px; line-height: 1.5;'>
                    Nếu có bất kỳ thắc mắc hoặc cần khiếu nại lại thông tin, vui lòng phản hồi trực tiếp qua hotline hỗ trợ kỹ thuật của FD TECH để gặp tổng đài viên - 19001000.
                </p>
            ";
        }

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 25px; border: 1px solid #eee; max-width: 550px; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background: #fff;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h2 style='color: #2563eb; margin: 0 0 5px 0; font-size: 22px; font-weight: bold; letter-spacing: 1px;'>FD TECH SHOP</h2>
                    <span style='font-size: 12px; color: #999; text-align: center; display: block;'>DỊCH VỤ CHĂM SÓC KHÁCH HÀNG</span>
                </div>
                <hr style='border: 0; border-top: 1px solid #eee; margin-bottom: 20px;'>
                <div style='margin-bottom: 15px; font-size: 14px;'><b>Trạng thái hồ sơ:</b> {$status_html}</div>
                {$content_html}
                <p style='color: #888; font-size: 12px; border-top: 1px dashed #eee; padding-top: 15px; line-height: 1.5; margin-top: 25px; text-align: center;'>
                    Đây là hộp thư thông báo tự động. Vui lòng không phản hồi trực tiếp vào địa chỉ email này.
                </p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $req_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
    $action_type = $_POST['action_type']; 
    $reason = trim($_POST['reject_reason'] ?? '');

    if ($req_id <= 0 || !in_array($action_type, ['approve', 'reject'])) {
        $_SESSION['flash_msg'] = 'Dữ liệu không hợp lệ!';
        header("Location: manage_requests.php");
        exit();
    }

    $stmtCheck = $pdo->prepare("
        SELECT r.*, u.email, u.username 
        FROM order_requests r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.id = ? LIMIT 1
    ");
    $stmtCheck->execute([$req_id]);
    $current_req = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($current_req && $current_req['status'] === 'pending') {
        $new_status = ($action_type === 'approve') ? 'approved' : 'rejected';
        
        $stmtUpdate = $pdo->prepare("UPDATE order_requests SET status = ? WHERE id = ?");
        $stmtUpdate->execute([$new_status, $req_id]);

        send_order_request_email($current_req['email'], $current_req['username'], $current_req['id'], $current_req['request_type'], $new_status, $reason);

        $_SESSION['flash_msg'] = "Đã xử lý hồ sơ yêu cầu #" . $req_id . " thành công!";
    }
    header("Location: manage_requests.php");
    exit();
}

$filter_status = $_GET['status'] ?? 'all';
$sql = "SELECT r.*, o.id as order_id, u.username, p.name as product_name 
        FROM order_requests r
        JOIN orders o ON r.order_id = o.id
        JOIN users u ON r.user_id = u.id
        JOIN products p ON r.product_id = p.id";

if (in_array($filter_status, ['pending', 'approved', 'rejected'])) {
    $sql .= " WHERE r.status = :status";
}
$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
if (in_array($filter_status, ['pending', 'approved', 'rejected'])) {
    $stmt->execute(['status' => $filter_status]);
} else {
    $stmt->execute();
}
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$page_title = 'Quản lý yêu cầu';
$page_icon = 'fa-solid fa-clipboard-question';

$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_manage.css">';

include 'includes/header.php';
?>

<div class="product-wrapper">
    <div class="product-card">
        
        <div class="product-header">
            <form method="GET" class="search-form" id="filterForm">
                <select name="status" class="request-filter-select" onchange="location = 'manage_requests.php?status=' + this.value;">
                    <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                    <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="approved" <?= $filter_status === 'approved' ? 'selected' : '' ?>>Đã phê duyệt</option>
                    <option value="rejected" <?= $filter_status === 'rejected' ? 'selected' : '' ?>>Đã từ chối</option>
                </select>
            </form>

            <h3>Hồ sơ yêu cầu bảo hành & Đổi trả</h3>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã REQ</th>
                    <th>Khách hàng</th>
                    <th>Loại yêu cầu</th>
                    <th>Sản phẩm bảo trì</th>
                    <th>Lý do tóm tắt</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($requests) > 0): ?>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td class="req-code">#REQ-<?= $req['id'] ?></td>
                            <td><strong><?= htmlspecialchars($req['username']) ?></strong></td>
                            <td>
                                <span style="font-weight: 600; color: <?= ($req['request_type'] === 'warranty') ? '#2563eb' : '#e67e22' ?>;">
                                    <?= ($req['request_type'] === 'warranty') ? 'Bảo hành' : 'Đổi trả' ?>
                                </span>
                            </td>
                            <td class="product-cell">
                                <?= htmlspecialchars($req['product_name']) ?>
                            </td>
                            <td><?= htmlspecialchars($req['reason']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                            <td>
                                <?php if ($req['status'] === 'pending'): ?>
                                    <span class="stock-badge status-pending">Chờ xử lý</span>
                                <?php elseif ($req['status'] === 'approved'): ?>
                                    <span class="stock-badge status-approved">Đã duyệt</span>
                                <?php else: ?>
                                    <span class="stock-badge status-rejected">Từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <button type="button" class="btn-action btn-view" title="Xem chi tiết hồ sơ" onclick="openViewModal(<?= htmlspecialchars(json_encode($req)) ?>)">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-box">
                                <i class="fa-solid fa-box-open"></i>
                                <h3>Không tìm thấy yêu cầu nào</h3>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<div id="requestModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h3>Chi tiết hồ sơ sự cố khách hàng</h3>
        
        <div class="modal-grid-info">
            <div><b>Mã hóa đơn gốc:</b> #<span id="mdOrder"></span></div>
            <div><b>Khách hàng:</b> <span id="mdUser"></span></div>
            <div class="full-width"><b>Thiết bị bảo trì:</b> <span id="mdProduct" class="product-highlight"></span></div>
            <div class="full-width"><b>Sự cố tóm tắt:</b> <span id="mdReason"></span></div>
        </div>

        <div class="modal-desc-section">
            <b>Nội dung mô tả chi tiết:</b>
            <div id="mdDescription" class="modal-desc-box"></div>
        </div>

        <div class="modal-evidence-section">
            <b>Hình ảnh & Video bằng chứng đính kèm:</b>
            <div id="imageGrid" class="image-evidence-grid"></div>
            <div id="videoContainer" class="video-evidence-container"></div>
        </div>

        <div id="adminActionSection">
            <form id="requestForm" method="POST" action="manage_requests.php">
                <input type="hidden" name="request_id" id="formRequestId">
                <input type="hidden" name="action_type" id="formActionType">
                
                <div id="rejectReasonWrapper" class="form-group" style="display: none; flex-direction: column; margin-bottom: 15px;">
                    <label class="reject-reason-label">Lý do từ chối đơn hàng (Nội dung này sẽ gửi Mail trực tiếp đến khách):</label>
                    <textarea name="reject_reason" id="txtRejectReason" class="reject-reason-textarea" placeholder="Nhập lý do chi tiết từ chối yêu cầu bảo trì này..."></textarea>
                </div>

                <div class="modal-footer-actions">
                    <button type="button" class="btn-submit-reject" id="btnRejectSubmit" onclick="triggerReject()">
                        <i class="fa-solid fa-ban"></i> Từ chối (Mail phản hồi)
                    </button>
                    <button type="button" class="btn-submit-approve" id="btnApproveSubmit" onclick="submitApprove()">
                        <i class="fa-solid fa-circle-check"></i> Phê duyệt chấp thuận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</main>
</div>
<script src="../assets/js/script_dashboard.js"></script>

<script>
    <?php if (isset($_SESSION['flash_msg'])): ?>
        alert('<?= htmlspecialchars($_SESSION['flash_msg'], ENT_QUOTES, 'UTF-8'); ?>');
        <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    const modal = document.getElementById('requestModal');

    function extractFileName(path) {
        if (!path) return '';
        let parts = path.split(/[/\\]/);
        return parts[parts.length - 1].trim();
    }

    function openViewModal(data) {
        document.getElementById('formRequestId').value = data.id;
        document.getElementById('mdOrder').innerText = data.order_id;
        document.getElementById('mdUser').innerText = data.username;
        document.getElementById('mdProduct').innerText = data.product_name;
        document.getElementById('mdReason').innerText = data.reason;
        document.getElementById('mdDescription').innerText = data.description ? data.description : "Không có mô tả chi tiết thêm.";
        
        resetRejectMode();

        const imgGrid = document.getElementById('imageGrid');
        imgGrid.innerHTML = '';
        if (data.images && data.images.trim() !== '') {
            const arrImg = data.images.split(',');
            let hasValidImg = false;

            arrImg.forEach(imgSrc => {
                let fileName = extractFileName(imgSrc);
                if(fileName !== "") {
                    hasValidImg = true;
                    const img = document.createElement('img');
                    img.src = "../upload/evidences/" + fileName; 
                    img.className = "img-evidence-item";
                    img.onclick = () => window.open(img.src, '_blank');
                    imgGrid.appendChild(img);
                }
            });

            if(!hasValidImg) {
                imgGrid.innerHTML = '<span class="evidence-blank">Không đính kèm ảnh bằng chứng.</span>';
            }
        } else {
            imgGrid.innerHTML = '<span class="evidence-blank">Không đính kèm ảnh bằng chứng.</span>';
        }

        const videoContainer = document.getElementById('videoContainer');
        videoContainer.innerHTML = '';
        if (data.video && data.video.trim() !== '') {
            let videoName = extractFileName(data.video);
            if(videoName !== "") {
                const video = document.createElement('video');
                video.src = "../upload/evidences/" + videoName;
                video.className = "video-evidence-item";
                video.controls = true;
                videoContainer.appendChild(video);
            }
        }

        const actionSection = document.getElementById('adminActionSection');
        if (data.status !== 'pending') {
            actionSection.style.display = 'none';
        } else {
            actionSection.style.display = 'block';
        }

        modal.classList.add('show');
    }

    function closeModal() {
        modal.classList.remove('show');
    }

    function submitApprove() {
        if (confirm("Xác nhận thông qua và PHÊ DUYỆT đơn yêu cầu này? Hệ thống sẽ gửi Mail thông báo tự động.")) {
            document.getElementById('formActionType').value = 'approve';
            document.getElementById('requestForm').submit();
        }
    }

    function triggerReject() {
        const wrapper = document.getElementById('rejectReasonWrapper');
        const btnApprove = document.getElementById('btnApproveSubmit');
        const btnReject = document.getElementById('btnRejectSubmit');

        if (wrapper.style.display === 'none') {
            wrapper.style.display = 'flex';
            btnApprove.style.display = 'none';
            btnReject.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Xác nhận Gửi từ chối';
            document.getElementById('txtRejectReason').focus();
        } else {
            const reason = document.getElementById('txtRejectReason').value.trim();
            if (reason === '') {
                alert("Vui lòng điền lý do từ chối cụ thể để làm nội dung gửi Email cho khách!");
                return;
            }
            if (confirm("Xác nhận TỪ CHỐI xử lý đơn yêu cầu hậu mãi này?")) {
                document.getElementById('formActionType').value = 'reject';
                document.getElementById('requestForm').submit();
            }
        }
    }

    function resetRejectMode() {
        document.getElementById('rejectReasonWrapper').style.display = 'none';
        document.getElementById('btnApproveSubmit').style.display = 'inline-block';
        document.getElementById('btnRejectSubmit').innerHTML = '<i class="fa-solid fa-ban"></i> Từ chối (Mail phản hồi)';
        document.getElementById('txtRejectReason').value = '';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
</body>
</html>
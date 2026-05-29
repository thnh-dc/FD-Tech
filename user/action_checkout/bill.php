<?php
// FILE: ../user/handle/bill.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Nạp các thư viện theo đường dẫn lùi cấp của bạn
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';
require_once __DIR__ . '/../../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../libs/PHPMailer/SMTP.php';

// Khai báo Namespace CHỮ HOA ĐÚNG CHUẨN để PHP nhận diện
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =========================================================================
// 2. HÀM 1: SINH CHUỖI DỮ LIỆU PDF HÓA ĐƠN
// =========================================================================
function generateBillPDF($order_id, $pdo) {
    // 1. Lấy thông tin đơn hàng
    $stmt = $pdo->prepare("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) return false;

    $order_date = $order['created_at']; 

    // 2. Lấy danh sách sản phẩm thuộc đơn hàng
    $stmtDetails = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmtDetails->execute([$order_id]);
    $items = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

    $pttt = ($order['payment_method'] == 'cod') ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng (Đã thanh toán)';

    // Định danh ID đơn hàng sạch sẽ trước khi đưa vào HTML
    $clean_order_id = htmlspecialchars($order['id']);

    // 3. Cấu trúc HTML/CSS
    $html = '
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @page { margin: 25px 30px; }
            body { 
                font-family: "DejaVu Sans", sans-serif; 
                font-size: 11px; 
                color: #333333; 
                line-height: 1.6; 
            }
            .header-table { width: 100%; margin-bottom: 15px; }
            .store-logo { font-size: 24px; font-weight: 800; color: #0B2A4A; letter-spacing: -0.5px; }
            .store-logo span { color: #23B5D3; }
            .store-sub { font-size: 9.5px; color: #6C757D; margin-top: 3px; }
            .bill-title { text-align: right; font-size: 18px; font-weight: bold; color: #0B2A4A; letter-spacing: 1px; }
            .barcode-box { text-align: right; margin-top: 5px; color: #333333; }
            .barcode-line { font-size: 13px; letter-spacing: -0.5px; font-weight: 300; color: #0B2A4A; }
            .barcode-text { font-size: 8.5px; color: #6C757D; margin-top: 2px; }
            .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
            .meta-table td { vertical-align: top; width: 50%; }
            .meta-box-left { padding-right: 15px; }
            .meta-box-right { padding-left: 15px; border-left: 1px solid #ccc; }
            .section-title { font-size: 9.5px; font-weight: bold; color: #6C757D; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
            .info-text { font-size: 11px; color: #333333; margin-bottom: 4px; }
            .info-text strong { color: #0B2A4A; }
            .product-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            .product-table th { 
                background-color: #F4F6F9; 
                color: #0B2A4A; 
                font-weight: bold; 
                border-bottom: 2px solid #0B2A4A; 
                padding: 10px 12px; 
                text-align: left; 
                font-size: 10px; 
                text-transform: uppercase;
            }
            .product-table td { padding: 10px 12px; border-bottom: 1px solid #F4F6F9; color: #333333; font-size: 11px; vertical-align: middle; }
            .product-table tr:last-child td { border-bottom: 2px solid #0B2A4A; }
            .date-tag { display: block; font-size: 9px; color: #6C757D; margin-top: 4px; }
            .date-tag strong { color: #ff0019; } 
            .date-tag .refund-date { color: #23B5D3; } 
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .footer-section { width: 100%; }
            .summary-table { width: 45%; margin-left: 55%; border-collapse: collapse; }
            .summary-table td { padding: 5px 8px; font-size: 11px; color: #333333; }
            .total-row { background-color: #F4F6F9; }
            .total-row td { 
                font-size: 13px; 
                font-weight: bold; 
                color: #0B2A4A; 
                padding: 8px 8px; 
                border-top: 1px solid #ccc; 
                border-bottom: 1px solid #ccc;
            }
            .total-price { color: #23B5D3 !important; font-size: 14px !important; }
            .warranty-divider { border-top: 1px dashed #ccc; margin: 20px 0 12px 0; }
            .warranty-note { text-align: center; font-size: 9.5px; color: #6C757D; line-height: 1.5; }
            .thanks-box { text-align: center; margin-top: 15px; }
            .thanks-text { font-weight: bold; color: #0B2A4A; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
            .sub-thanks { font-size: 9px; color: #6C757D; margin-top: 4px; }
        </style>
    </head>
    <body>
        <table class="header-table">
            <tr>
                <td>
                    <div class="store-logo">FD-TECH<span>.</span></div>
                    <div class="store-sub">Hệ thống phân phối linh kiện máy tính & Công nghệ cao cấp</div>
                    <div class="store-sub">Địa chỉ: Thôn Đa, Di Trạch, Hoài Đức, Hà Nội | Hotline: 1900 8888</div>
                </td>
                <td style="vertical-align: top;">
                    <div class="bill-title">HÓA ĐƠN BÁN HÀNG</div>
                    <div class="barcode-box">
                        <div class="barcode-line">||||||||||||||||||||||||||||||</div>
                        <div class="barcode-text">SON00000'.$clean_order_id.'</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-box-left">
                        <div class="section-title">Hóa đơn gửi đến</div>
                        <div class="info-text">Khách hàng: <strong>'.htmlspecialchars($order['username']).'</strong></div>
                        <div class="info-text">Địa chỉ nhận hàng:</div>
                        <div class="info-text" style="color:#6C757D; font-size: 10.5px; line-height: 1.4;">'.htmlspecialchars($order['shipping_address'] ?? 'Chưa cập nhật địa chỉ').'</div>
                    </div>
                </td>
                <td>
                    <div class="meta-box-right">
                        <div class="section-title">Thông tin giao dịch</div>
                        <div class="info-text">Ngày bán: <strong>'.date('d/m/Y - H:i', strtotime($order_date)).'</strong></div>
                        <div class="info-text">N/V: <strong>Phần mềm Bán Hàng</strong></div>
                        <div class="info-text">Phương thức: <span style="color: #23B5D3; font-weight: bold;">'.$order['payment_method'].'</span></div>
                        <div class="info-text" style="font-size: 9.5px; color: #6C757D;">('.$pttt.')</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="product-table">
            <thead>
                <tr>
                    <th style="width: 48%;">Thông tin sản phẩm / Dịch vụ hậu mãi</th>
                    <th class="text-center" style="width: 8%;">SL</th>
                    <th class="text-right" style="width: 14%;">ĐG</th>
                    <th class="text-center" style="width: 8%;">CK</th>
                    <th class="text-right" style="width: 22%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>';
            $total_items_quantity = 0;
            foreach ($items as $item) {
                $total_items_quantity += $item['quantity'];
                $subtotal = $item['price'] * $item['quantity'];
                
                $refund_expiry = date('d/m/Y', strtotime($order_date . ' + 7 days'));

                $productNameLower = mb_strtolower($item['product_name'], 'UTF-8');
                if (strpos($productNameLower, 'chuột') !== false || strpos($productNameLower, 'bàn phím') !== false || strpos($productNameLower, 'tai nghe') !== false) {
                    $warranty_months = 12;
                } elseif (strpos($productNameLower, 'cpu') !== false || strpos($productNameLower, 'vga') !== false || strpos($productNameLower, 'mainboard') !== false || strpos($productNameLower, 'nguồn') !== false || strpos($productNameLower, 'ssd') !== false || strpos($productNameLower, 'ram') !== false) {
                    $warranty_months = 36;
                } else {
                    $warranty_months = 24;
                }
                
                $warranty_expiry = date('d/m/Y', strtotime($order_date . " + $warranty_months months"));

                $html .= '<tr>';
                $html .= '  <td>';
                $html .= '      <span style="font-weight: bold; color: #0B2A4A; display:block;">'.htmlspecialchars($item['product_name']).'</span>';
                $html .= '      <span class="date-tag">• Hạn hoàn hàng/đổi trả: <span class="refund-date"><strong>'.$refund_expiry.'</strong></span> (7 ngày)</span>';
                $html .= '      <span class="date-tag">• Hạn bảo hành đến ngày: <strong>'.$warranty_expiry.'</strong> ('.$warranty_months.' tháng)</span>';
                $html .= '  </td>';
                $html .= '  <td class="text-center" style="color: #333333;">'.$item['quantity'].'</td>';
                $html .= '  <td class="text-right">'.number_format($item['price'], 0, ',', '.').'</td>';
                $html .= '  <td class="text-center">0</td>';
                $html .= '  <td class="text-right" style="font-weight: bold; color: #0B2A4A;">'.number_format($subtotal, 0, ',', '.').'</td>';
                $html .= '</tr>';
            }
    $html .= '
            </tbody>
        </table>

        <div class="footer-section">
            <table class="summary-table">
                <tr>
                    <td>Tổng số lượng:</td>
                    <td class="text-right" style="font-weight: bold;">' . $total_items_quantity . '</td>
                </tr>
                <tr>
                    <td>Tổng tiền hàng:</td>
                    <td class="text-right">' . number_format($order['total_amount'], 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td>Chiết khấu:</td>
                    <td class="text-right">0</td>
                </tr>
                <tr>
                    <td>Phí giao hàng:</td>
                    <td class="text-right">0</td>
                </tr>
                <tr class="total-row">
                    <td>Khách phải trả:</td>
                    <td class="text-right total-price">' . number_format($order['total_amount'], 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td>Tiền khách đưa:</td>
                    <td class="text-right">' . number_format($order['total_amount'], 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td>Tiền trả lại:</td>
                    <td class="text-right">0</td>
                </tr>
            </table>
        </div>

        <div class="warranty-divider"></div>
        <div class="warranty-note">
            * <strong>Lưu ý hậu mãi:</strong> Lịch đổi trả hàng/hoàn tiền và thời gian bảo hành kỹ thuật được tính chính xác dựa trên thời điểm xuất kho sản phẩm.<br>
            Vui lòng giữ lại file hóa đơn điện tử này hoặc cung cấp mã đơn hàng khi liên hệ bảo hành tại FD-Tech Store.
        </div>
        <div class="warranty-divider" style="margin: 12px 0 20px 0;"></div>

        <div class="thanks-box">
            <div class="thanks-text">Cảm ơn và hẹn gặp lại quý khách!</div>
            <div class="sub-thanks">Hóa đơn điện tử được đồng bộ và xác thực bởi FD-Tech Store.</div>
        </div>
    </body>
    </html>';

    // Bắt buộc gọi đúng Class chữ hoa chính thống của thư viện
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A5', 'portrait');
    $dompdf->render();
    
    return $dompdf->output(); 
}

// =========================================================================
// 3. HÀM 2: CẤU HÌNH GỬI EMAIL ĐÍNH KÈM FILE PDF
// =========================================================================
function send_order_bill_email($to_email, $order_id, $pdo) {
    $pdf_content = generateBillPDF($order_id, $pdo);
    if (!$pdf_content) return false;

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

        $mail->setFrom('fdtechshop@gmail.com', 'FD-Tech Shop');
        $mail->addAddress($to_email); 
        $mail->isHTML(true);

        $mail->addStringAttachment($pdf_content, 'HoaDon_FDTech_' . $order_id . '.pdf');

        $mail->Subject = '[FD TECH] Xác nhận thanh toán & Hóa đơn đơn hàng #' . $order_id;
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 25px; border: 1px solid #e2e8f0; max-width: 500px; margin: 0 auto; border-radius: 12px; background-color: #fff;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h2 style='color: #0B2A4A; margin: 0; font-size: 22px; font-weight: bold;'>THANH TOÁN THÀNH CÔNG</h2>
                    <p style='color: #6C757D; margin: 5px 0 0 0; font-size: 14px;'>Đơn hàng #{$order_id} của bạn đã hoàn tất</p>
                </div>
                <div style='border-top: 1px solid #F4F6F9; padding-top: 15px; margin-bottom: 15px; color: #333333; font-size: 14px; line-height: 1.6;'>
                    <p>Xin chào,</p>
                    <p>Hệ thống công nghệ <strong>FD-Tech Shop</strong> xác nhận đã tiếp nhận khoản thanh toán cho đơn hàng số <strong>#{$order_id}</strong>.</p>
                    <p>Hóa đơn điện tử thông minh đi kèm mốc thời hạn bảo hành & hoàn tiền cụ thể cho từng sản phẩm đã được đính kèm dưới dạng file PDF trong email.</p>
                </div>
                <div style='color: #6C757D; font-size: 12px; border-top: 1px solid #F4F6F9; padding-top: 15px; text-align: center; font-style: italic;'>
                    Cảm ơn và hẹn gặp lại quý khách!
                </div>
            </div>
        ";

        $mail->send();
        return true; 
    } catch (Exception $e) {
        return false; 
    }
}

// =========================================================================
// 4. ĐÓN NHẬN REQUEST XEM FILE TRỰC TIẾP
// =========================================================================
if (isset($_GET['id']) && !empty($_GET['id'])) {
    require_once __DIR__ . '/../../config/database.php'; 
    
    $user_id = $_SESSION['user_id'] ?? 0;
    $order_id = (int)$_GET['id'];

    if ($user_id <= 0 || $order_id <= 0) {
        die("Yêu cầu không hợp lệ!");
    }

    $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    if (!$stmt->fetch()) {
        die("Bạn không có quyền truy cập hóa đơn này!");
    }

    $pdf_content = generateBillPDF($order_id, $pdo);

    if ($pdf_content) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="HoaDon_FDTech_' . $order_id . '.pdf"');
        echo $pdf_content;
        exit;
    } else {
        die("Không thể hiển thị hóa đơn lúc này.");
    }
}
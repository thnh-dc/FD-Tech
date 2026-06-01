<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';
require_once __DIR__ . '/../../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../libs/PHPMailer/SMTP.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateBillPDF($order_id, $pdo) {
    $stmt = $pdo->prepare("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) return false;

    $order_date = $order['created_at']; 

    $stmtDetails = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmtDetails->execute([$order_id]);
    $items = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

    $pttt = ($order['payment_method'] == 'cod') ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng (Đã thanh toán)';
    $clean_order_id = htmlspecialchars($order['id']);

    $html = '
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @page { margin: 20px 25px; }
            body { 
                font-family: "DejaVu Sans", sans-serif; 
                font-size: 11px; 
                color: #2b2b2b; 
                line-height: 1.5; 
            }
            
            /* Cấu trúc phần Header */
            .header-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
            .header-table td { vertical-align: top; }
            .store-logo { font-size: 24px; font-weight: 800; color: #0B2A4A; letter-spacing: -0.5px; }
            .store-logo span { color: #23B5D3; }
            .store-sub { font-size: 9.5px; color: #6C757D; margin-top: 3px; line-height: 1.4; }
            .bill-title { text-align: right; font-size: 20px; font-weight: bold; color: #0B2A4A; letter-spacing: 0.5px; }
            .barcode-box { text-align: right; margin-top: 5px; }
            .barcode-line { font-size: 13px; letter-spacing: -0.5px; font-weight: 300; color: #0B2A4A; line-height: 1; }
            .barcode-text { font-size: 8.5px; color: #6C757D; margin-top: 2px; }
            
            /* Thông tin khách hàng */
            .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
            .meta-table td { vertical-align: top; width: 50%; }
            .meta-box-left { padding-right: 15px; }
            .meta-box-right { padding-left: 15px; border-left: 1px solid #e2e8f0; }
            .section-title { font-size: 10px; font-weight: bold; color: #0B2A4A; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
            .info-text { font-size: 11px; color: #333333; margin-bottom: 4px; }
            .info-text strong { color: #0B2A4A; }
            
            /* Bảng sản phẩm gọn gàng, không chứa tag ngày tháng */
            .product-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .product-table th { 
                color: #0B2A4A; 
                font-weight: bold; 
                border-top: 1.5px solid #0B2A4A;
                border-bottom: 1.5px solid #0B2A4A; 
                padding: 8px 6px; 
                text-align: left; 
                font-size: 10.5px; 
                text-transform: uppercase;
            }
            .product-table td { padding: 10px 6px; border-bottom: 1px solid #E2E8F0; color: #333333; font-size: 11px; }
            .product-table tr.last-row td { border-bottom: 1.5px solid #0B2A4A; }
            
            /* Khối hậu mãi nằm riêng biệt hoàn toàn dưới bảng */
            .service-container { width: 100%; margin-top: 15px; margin-bottom: 15px; background-color: #F8FAFC; padding: 10px; border-radius: 4px; }
            .service-title { font-size: 10.5px; font-weight: bold; color: #0B2A4A; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
            .service-item { margin-bottom: 8px; }
            .service-item-name { font-weight: bold; color: #333333; font-size: 11px; }
            .service-item-sub { font-size: 10px; color: #4A5568; margin-top: 2px; }
            .service-item-sub strong { color: #ff0019; }
            
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            
            /* Tổng tiền */
            .footer-layout { width: 100%; margin-top: 10px; }
            .summary-table { width: 45%; margin-left: 55%; border-collapse: collapse; }
            .summary-table td { padding: 4px 6px; font-size: 11px; color: #333333; }
            .total-row td { 
                font-size: 13px; 
                font-weight: bold; 
                color: #0B2A4A; 
                padding: 6px 6px; 
                border-top: 1px dashed #cbd5e1; 
                border-bottom: 1px dashed #cbd5e1;
            }
            .total-price { color: #23B5D3 !important; font-size: 14px !important; }
            
            .warranty-divider { border-top: 1px dashed #cbd5e1; margin: 15px 0 10px 0; }
            .warranty-note { text-align: center; font-size: 9.5px; color: #718096; line-height: 1.5; }
            .thanks-box { text-align: center; margin-top: 10px; }
            .thanks-text { font-weight: bold; color: #0B2A4A; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
            .sub-thanks { font-size: 9px; color: #a0aec0; margin-top: 3px; }
        </style>
    </head>
    <body>
        
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <div class="store-logo">FD-TECH</div>
                    <div class="store-sub">Hệ thống phân phối linh kiện máy tính & Công nghệ cao cấp</div>
                    <div class="store-sub">Địa chỉ: Quy Nhơn, Gia Lai | Hotline: 1900 1000</div>
                </td>
                <td style="width: 45%; vertical-align: top;">
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
                        <div class="info-text" style="color:#4A5568; font-size: 10.5px; line-height: 1.4;">'.htmlspecialchars($order['shipping_address'] ?? 'Chưa cập nhật địa chỉ').'</div>
                    </div>
                </td>
                <td>
                    <div class="meta-box-right">
                        <div class="section-title">Thông tin giao dịch</div>
                        <div class="info-text">Ngày bán: <strong>'.date('d/m/Y - H:i', strtotime($order_date)).'</strong></div>
                        <div class="info-text">N/V: <strong>FD Tech</strong></div>
                        <div class="info-text">Phương thức: <span style="color: #23B5D3; font-weight: bold;">'.strtoupper($order['payment_method']).'</span></div>
                        <div class="info-text" style="font-size: 9.5px; color: #6C757D;">('.$pttt.')</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="product-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Thông tin sản phẩm</th>
                    <th class="text-center" style="width: 8%;">SL</th>
                    <th class="text-right" style="width: 16%;">ĐG</th>
                    <th class="text-center" style="width: 6%;">CK</th>
                    <th class="text-right" style="width: 20%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>';
            
            $total_items_quantity = 0;
            $warranty_data_list = [];
            $count = count($items);

            for ($i = 0; $i < $count; $i++) {
                $item = $items[$i];
                $total_items_quantity += $item['quantity'];
                $subtotal = $item['price'] * $item['quantity'];
                
                $refund_expiry = date('d/m/Y', strtotime($order_date . ' + 7 days'));

                $productNameLower = mb_strtolower($item['product_name'], 'UTF-8');
                
                if (
                    strpos($productNameLower, 'chuột') !== false || 
                    strpos($productNameLower, 'bàn phím') !== false || 
                    strpos($productNameLower, 'tai nghe') !== false || 
                    strpos($productNameLower, 'loa') !== false
                ) {
                    $warranty_months = 6;
                } elseif (
                    strpos($productNameLower, 'laptop') !== false || 
                    strpos($productNameLower, 'màn hình') !== false || 
                    strpos($productNameLower, 'linh kiện') !== false
                ) {
                    $warranty_months = 18;
                } else {
                    $warranty_months = 12; 
                }
                
                $warranty_expiry = date('d/m/Y', strtotime($order_date . " + $warranty_months months"));

                $warranty_data_list[] = [
                    'name' => $item['product_name'],
                    'refund' => $refund_expiry,
                    'warranty' => $warranty_expiry,
                    'months' => $warranty_months
                ];

                $isLast = ($i === $count - 1) ? 'class="last-row"' : '';

                $html .= '<tr '.$isLast.'>';
                $html .= '  <td style="font-weight: bold; color: #0B2A4A;">'.htmlspecialchars($item['product_name']).'</td>';
                $html .= '  <td class="text-center">'.$item['quantity'].'</td>';
                $html .= '  <td class="text-right">'.number_format($item['price'], 0, ',', '.').'</td>';
                $html .= '  <td class="text-center">0</td>';
                $html .= '  <td class="text-right" style="font-weight: bold; color: #0B2A4A;">'.number_format($subtotal, 0, ',', '.').'</td>';
                $html .= '</tr>';
            }
    $html .= '
            </tbody>
        </table>

        <div class="service-container">
            <div class="service-title">Thông tin sản phẩm / Dịch vụ hậu mãi</div>';
            foreach ($warranty_data_list as $wData) {
                $html .= '<div class="service-item">';
                $html .= '  <div class="service-item-name">• '.htmlspecialchars($wData['name']).'</div>';
                $html .= '  <div class="service-item-sub">&nbsp;&nbsp;&nbsp;- Hạn hoàn hàng/đổi trả: <strong>'.$wData['refund'].'</strong> (7 ngày)</div>';
                $html .= '  <div class="service-item-sub">&nbsp;&nbsp;&nbsp;- Hạn bảo hành đến ngày: <strong>'.$wData['warranty'].'</strong> ('.$wData['months'].' tháng)</div>';
                $html .= '</div>';
            }
    $html .= '
        </div>

        <div class="footer-layout">
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
            * <strong>Lưu ý hậu mãi:</strong> Lịch đổi trả hàng/hoàn tiền và thời gian bảo hành kỹ thuật được tính chính xác từ lúc bạn xác nhận nhận hàng.<br>
            Vui lòng giữ lại file hóa đơn điện tử này hoặc cung cấp mã đơn hàng khi liên hệ bảo hành tại FD Tech.
        </div>
        <div class="warranty-divider" style="margin: 10px 0 15px 0;"></div>

        <div class="thanks-box">
            <div class="thanks-text">Cảm ơn và hẹn gặp lại quý khách!</div>
            <div class="sub-thanks">Hóa đơn điện tử được đồng bộ và xác thực bởi FD Tech.</div>
        </div>
    </body>
    </html>';

    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A5', 'portrait');
    $dompdf->render();
    
    return $dompdf->output(); 
}

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
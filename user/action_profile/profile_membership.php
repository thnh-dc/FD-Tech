<?php
if (!isset($user_id)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? 0;
}

require_once __DIR__ . '/../../includes/fd_member_helper.php';

$period = getCurrentFDMemberPeriod();

$point_balance = getUserFDPointBalance($pdo, $user_id);
$period_points = getUserPeriodFDp($pdo, $user_id);
$current_tier = getFDMemberTierByPoint($pdo, $period_points);
$next_tier = getNextFDMemberTier($pdo, $period_points);
$progress = getFDMemberProgress($period_points, $next_tier);
$tiers = getAllFDMemberTiers($pdo);
$histories = getUserFDPointHistory($pdo, $user_id, 30);

$current_tier_key = $current_tier['tier_key'];
$current_tier_class = getFDMemberClass($current_tier_key);
$current_tier_icon = getFDMemberIcon($current_tier_key);
?>

<div class="membership-wrapper">

    <div class="membership-hero">
        <div class="membership-hero-top">
            <div>
                <h2>FD Member</h2>
                <p>Hệ thống thành viên dành riêng cho khách hàng FD Tech</p>
            </div>

            <div class="current-rank-badge">
                <i class="fas <?= htmlspecialchars($current_tier_icon) ?>"></i>
                Hạng hiện tại: <?= htmlspecialchars($current_tier['tier_name']) ?>
            </div>
        </div>

        <div class="member-summary-grid">
            <div class="member-summary-card">
                <span>FDp tiêu dùng</span>
                <strong><?= number_format($point_balance, 0, ',', '.') ?> FDp</strong>
            </div>

            <div class="member-summary-card">
                <span>FDp tích lũy kỳ này</span>
                <strong><?= number_format($period_points, 0, ',', '.') ?> FDp</strong>
            </div>

            <div class="member-summary-card">
                <span>Kỳ xét hạng hiện tại</span>
                <strong style="font-size: 14px;">
                    <?= htmlspecialchars($period['label']) ?>
                </strong>
            </div>
        </div>

        <div class="member-progress-box">
            <div class="member-progress-info">
                <?php if ($next_tier): ?>
                    <span>
                        Còn 
                        <b><?= number_format($progress['remaining'], 0, ',', '.') ?> FDp</b>
                        để lên hạng 
                        <b><?= htmlspecialchars($next_tier['tier_name']) ?></b>
                    </span>
                    <span><?= $progress['percent'] ?>%</span>
                <?php else: ?>
                    <span>Bạn đã đạt hạng cao nhất của FD Member.</span>
                    <span>100%</span>
                <?php endif; ?>
            </div>

            <div class="member-progress-bar">
                <div class="member-progress-fill" style="width: <?= $progress['percent'] ?>%;"></div>
            </div>

            <p style="font-size: 12px; color: rgba(255,255,255,0.75); margin-top: 10px;">
                FDp tích lũy xét hạng sẽ được tính trong từng kỳ 6 tháng và tự làm mới sau ngày 
                <b><?= htmlspecialchars($period['reset_date']) ?></b>.
            </p>
        </div>
    </div>

    <div class="member-tier-section">
        <h3 class="member-section-title">
            <i class="fas fa-layer-group"></i>
            Phân cấp hạng thành viên
        </h3>

        <div class="member-tier-grid">
            <?php foreach ($tiers as $tier): ?>
                <?php
                    $tier_key = $tier['tier_key'];
                    $tier_icon = getFDMemberIcon($tier_key);
                    $tier_class = getFDMemberClass($tier_key);
                    $is_active = $tier_key === $current_tier_key;
                ?>

                <div class="member-tier-card <?= htmlspecialchars($tier_class) ?> <?= $is_active ? 'active' : '' ?>">
                    <div class="member-tier-icon">
                        <i class="fas <?= htmlspecialchars($tier_icon) ?>"></i>
                    </div>

                    <h3><?= htmlspecialchars($tier['tier_name']) ?></h3>

                    <p>
                        Từ 
                        <strong><?= number_format((int)$tier['min_period_points'], 0, ',', '.') ?> FDp</strong>
                        / kỳ
                    </p>

                    <p>
                        Giảm 
                        <strong><?= number_format((float)$tier['discount_percent'], 0, ',', '.') ?>%</strong>
                        đơn hàng
                    </p>

                    <p>
                        <?= ((int)$tier['free_shipping'] === 1) ? 'Miễn phí vận chuyển' : 'Chưa miễn phí vận chuyển' ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="member-tier-section">
        <div class="member-tabs">
            <button type="button" class="member-tab-btn active" data-tab="benefits">
                <i class="fas fa-gift"></i>
                Ưu đãi thành viên
            </button>

            <button type="button" class="member-tab-btn" data-tab="history">
                <i class="fas fa-clock-rotate-left"></i>
                Lịch sử FDp
            </button>
        </div>

        <div class="member-tab-content active" id="member-tab-benefits">
            <div class="member-benefit-list">
                <div class="member-benefit-item">
                    <i class="fas fa-percent"></i>
                    <div>
                        <h4>Giảm giá theo hạng</h4>
                        <p>
                            Hạng hiện tại của bạn được giảm 
                            <b><?= number_format((float)$current_tier['discount_percent'], 0, ',', '.') ?>%</b>
                            cho đơn hàng đủ điều kiện.
                        </p>
                    </div>
                </div>

                <div class="member-benefit-item">
                    <i class="fas fa-truck-fast"></i>
                    <div>
                        <h4>Ưu đãi vận chuyển</h4>
                        <p>
                            <?php if ((int)$current_tier['free_shipping'] === 1): ?>
                                Bạn đang được hỗ trợ miễn phí vận chuyển theo hạng hiện tại.
                            <?php else: ?>
                                Hạng hiện tại chưa có miễn phí vận chuyển. Hãy tích lũy thêm FDp để mở khóa ưu đãi này.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="member-benefit-item">
                    <i class="fas fa-coins"></i>
                    <div>
                        <h4>Tích FDp khi mua hàng</h4>
                        <p>
                            Khi đơn hàng hoàn thành, hệ thống tự động cộng FDp vào tài khoản của bạn.
                        </p>
                    </div>
                </div>

                <div class="member-benefit-item">
                    <i class="fas fa-rotate-left"></i>
                    <div>
                        <h4>Hoàn FDp khi hủy đơn</h4>
                        <p>
                            Nếu đơn hàng bị hủy, số FDp đã sử dụng cho đơn đó sẽ được hoàn lại.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="member-tab-content" id="member-tab-history">
            <?php if (empty($histories)): ?>
                <div class="member-history-empty">
                    <i class="fas fa-receipt"></i>
                    <p>Bạn chưa có lịch sử FDp nào.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="member-history-table">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Loại giao dịch</th>
                                <th>FDp</th>
                                <th>Đơn hàng</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($histories as $history): ?>
                                <?php
                                    $type = $history['type'];
                                    $points = (int)$history['points'];

                                    $point_class = $points >= 0 ? 'member-point-plus' : 'member-point-minus';
                                    $point_sign = $points > 0 ? '+' : '';

                                    if ($type === 'redeem' && $points > 0) {
                                        $point_class = 'member-point-minus';
                                        $point_sign = '-';
                                    }
                                ?>

                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($history['created_at'])) ?></td>

                                    <td><?= htmlspecialchars(getFDPointTypeText($type)) ?></td>

                                    <td>
                                        <span class="<?= $point_class ?>">
                                            <?= $point_sign . number_format(abs($points), 0, ',', '.') ?> FDp
                                        </span>
                                    </td>

                                    <td>
                                        <?= !empty($history['order_id']) ? '#FD-' . htmlspecialchars($history['order_id']) : '-' ?>
                                    </td>

                                    <td>
                                        <?= !empty($history['description']) ? htmlspecialchars($history['description']) : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="/FD-Tech/assets/js/script_membership.js"></script>
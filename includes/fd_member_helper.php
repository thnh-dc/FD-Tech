<?php
function getCurrentFDMemberPeriod()
{
    $year = (int)date('Y');
    $month = (int)date('n');

    if ($month <= 6) {
        return [
            'start' => $year . '-01-01 00:00:00',
            'end' => $year . '-06-30 23:59:59',
            'label' => '01/01/' . $year . ' - 30/06/' . $year,
            'reset_date' => '30/06/' . $year
        ];
    }

    return [
        'start' => $year . '-07-01 00:00:00',
        'end' => $year . '-12-31 23:59:59',
        'label' => '01/07/' . $year . ' - 31/12/' . $year,
        'reset_date' => '31/12/' . $year
    ];
}

function getUserPeriodFDp($pdo, $user_id)
{
    $period = getCurrentFDMemberPeriod();

    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(points), 0) AS total_period_points
        FROM fd_point_transactions
        WHERE user_id = ?
        AND type = 'earn'
        AND created_at BETWEEN ? AND ?
    ");

    $stmt->execute([
        $user_id,
        $period['start'],
        $period['end']
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int)($row['total_period_points'] ?? 0);
}

function getFDMemberTierByPoint($pdo, $period_points)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM fd_member_tiers
        WHERE min_period_points <= ?
        ORDER BY min_period_points DESC
        LIMIT 1
    ");

    $stmt->execute([(int)$period_points]);
    $tier = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tier) {
        return [
            'tier_key' => 'bronze',
            'tier_name' => 'Đồng',
            'min_period_points' => 0,
            'discount_percent' => 0,
            'free_shipping' => 0,
            'description' => 'Hạng thành viên cơ bản của FD Tech.',
            'sort_order' => 1
        ];
    }

    return $tier;
}

function getNextFDMemberTier($pdo, $period_points)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM fd_member_tiers
        WHERE min_period_points > ?
        ORDER BY min_period_points ASC
        LIMIT 1
    ");

    $stmt->execute([(int)$period_points]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getFDMemberIcon($tier_key)
{
    $icons = [
        'bronze' => 'fa-award',
        'silver' => 'fa-medal',
        'gold' => 'fa-crown',
        'diamond' => 'fa-gem'
    ];

    return $icons[$tier_key] ?? 'fa-award';
}

function getFDMemberClass($tier_key)
{
    $classes = [
        'bronze' => 'member-bronze',
        'silver' => 'member-silver',
        'gold' => 'member-gold',
        'diamond' => 'member-diamond'
    ];

    return $classes[$tier_key] ?? 'member-bronze';
}

function getFDMemberProgress($period_points, $next_tier)
{
    if (!$next_tier) {
        return [
            'percent' => 100,
            'remaining' => 0
        ];
    }

    $next_point = (int)$next_tier['min_period_points'];
    $period_points = (int)$period_points;

    if ($next_point <= 0) {
        return [
            'percent' => 0,
            'remaining' => 0
        ];
    }

    return [
        'percent' => min(100, round(($period_points / $next_point) * 100)),
        'remaining' => max($next_point - $period_points, 0)
    ];
}

function getUserFDPointBalance($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT point
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([(int)$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int)($user['point'] ?? 0);
}

function getUserFDPointHistory($pdo, $user_id, $limit = 30)
{
    $stmt = $pdo->prepare("
        SELECT 
            fpt.*,
            o.total_amount,
            o.status AS order_status
        FROM fd_point_transactions fpt
        LEFT JOIN orders o ON fpt.order_id = o.id
        WHERE fpt.user_id = ?
        ORDER BY fpt.created_at DESC
        LIMIT $limit
    ");

    $stmt->execute([(int)$user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllFDMemberTiers($pdo)
{
    $stmt = $pdo->query("
        SELECT *
        FROM fd_member_tiers
        ORDER BY sort_order ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFDPointTypeText($type)
{
    $map = [
        'earn' => 'Tích FDp',
        'redeem' => 'Dùng FDp',
        'refund' => 'Hoàn FDp',
        'adjust' => 'Điều chỉnh'
    ];

    return $map[$type] ?? 'Không xác định';
}
?>
<?php
/**
 * Migration: tạo bảng activity_logs + hero_slides.
 * Chạy 1 lần: http://localhost/acer_website/setup_features.php
 * Sau đó nên XÓA file này.
 */
require_once 'includes/db.php';

header('Content-Type: text/html; charset=utf-8');
echo "<pre style='font-family:Consolas,monospace;background:#1e1e1e;color:#dcdcdc;padding:20px;'>";

$conn = getConnection();

function step($conn, $label, $sql) {
    echo "→ $label ... ";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        echo "<span style='color:#f55'>LỖI</span>\n";
        foreach (sqlsrv_errors() as $err) {
            echo "   " . htmlspecialchars($err['message']) . "\n";
        }
        return false;
    }
    echo "<span style='color:#5f5'>OK</span>\n";
    return true;
}

// 1. activity_logs — ghi nhận thao tác admin/staff
step($conn, "Tạo bảng activity_logs",
    "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='activity_logs' AND xtype='U')
     CREATE TABLE activity_logs (
        id          INT IDENTITY(1,1) PRIMARY KEY,
        user_id     INT NULL,
        username    NVARCHAR(100) NULL,
        role        NVARCHAR(20)  NULL,
        action      NVARCHAR(50)  NOT NULL,
        entity      NVARCHAR(50)  NULL,
        entity_id   INT           NULL,
        details     NVARCHAR(500) NULL,
        ip_address  NVARCHAR(45)  NULL,
        user_agent  NVARCHAR(255) NULL,
        created_at  DATETIME      NOT NULL DEFAULT GETDATE()
     )"
);

// 2. hero_slides — quản lý slide hero từ admin
step($conn, "Tạo bảng hero_slides",
    "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='hero_slides' AND xtype='U')
     CREATE TABLE hero_slides (
        id              INT IDENTITY(1,1) PRIMARY KEY,
        badge_text      NVARCHAR(50)  NULL,
        title_line1     NVARCHAR(150) NOT NULL,
        title_line2     NVARCHAR(150) NULL,
        description     NVARCHAR(300) NULL,
        btn_text        NVARCHAR(100) NULL,
        btn_link        NVARCHAR(255) NULL,
        bg_gradient     NVARCHAR(300) NOT NULL,
        text_color      NVARCHAR(20)  NOT NULL DEFAULT '#ffffff',
        accent_color    NVARCHAR(20)  NULL,
        illustration    NVARCHAR(30)  NULL,
        image_url       NVARCHAR(500) NULL,
        sort_order      INT           NOT NULL DEFAULT 0,
        visible         BIT           NOT NULL DEFAULT 1,
        created_at      DATETIME      NOT NULL DEFAULT GETDATE()
     )"
);

// 3. Seed slide mẫu nếu trống
$check = sqlsrv_query($conn, "SELECT COUNT(*) AS c FROM hero_slides");
$row = $check ? sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC) : null;
if ($row && (int)$row['c'] === 0) {
    echo "→ Seed 3 slide mẫu ... ";
    $slides = [
        ['Mới nhất 2024', 'Predator Helios 300', 'Gaming không giới hạn', 'Intel i7 • RTX 3070 • 144Hz QHD',
         'Khám phá ngay →', 'products.php?cat=laptop-gaming',
         'linear-gradient(135deg,#1a3a0a 0%,#2e5c17 40%,#3d7a1f 70%,#6BBF59 100%)',
         '#ffffff', '#6BBF59', 'laptop', 1],
        ['Văn phòng', 'Swift 3 & Aspire 5', 'Làm việc hiệu quả mọi lúc', 'Mỏng nhẹ • Pin trâu • Giá tốt',
         'Xem dòng văn phòng →', 'products.php?cat=laptop-vanphong',
         'linear-gradient(135deg,#0C447C 0%,#185FA5 50%,#378ADD 100%)',
         '#ffffff', '#378ADD', 'monitor', 2],
        ['Ưu đãi đặc biệt', 'Phụ kiện chính hãng', 'Chuột • Bàn phím • Hub', 'Bảo hành 12 tháng • Giao hàng toàn quốc',
         'Xem phụ kiện →', 'products.php?cat=phu-kien',
         'linear-gradient(135deg,#5c1a0a 0%,#7a3f1f 40%,#c47a2a 80%,#f0b429 100%)',
         '#ffffff', '#f0b429', 'accessory', 3],
    ];
    $ok = true;
    foreach ($slides as $s) {
        $r = sqlsrv_query($conn,
            "INSERT INTO hero_slides
             (badge_text, title_line1, title_line2, description, btn_text, btn_link,
              bg_gradient, text_color, accent_color, illustration, sort_order)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            $s);
        if ($r === false) $ok = false;
    }
    echo $ok ? "<span style='color:#5f5'>OK (3 slide)</span>\n" : "<span style='color:#f55'>LỖI</span>\n";
}

echo "\n<span style='color:#5f5'>====================================</span>\n";
echo "<span style='color:#5f5'>HOÀN TẤT</span>\n";
echo "<span style='color:#5f5'>====================================</span>\n\n";

echo "<span style='color:#fc0'>⚠ XÓA file setup_features.php sau khi xong!</span>\n";
echo "\n<a href='admin/dashboard.php' style='color:#6BBF59;'>→ Vào dashboard</a>   <a href='admin/activity.php' style='color:#6BBF59;'>→ Lịch sử thao tác</a>\n";
echo "</pre>";

sqlsrv_close($conn);

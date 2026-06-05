<?php
/**
 * Tự động chạy migration thêm role/full_name/created_at vào bảng admins.
 * Chạy 1 lần: http://localhost/acer_website/setup_roles.php
 * Sau khi chạy xong nên XÓA file này.
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

// 1. role
step($conn, "Thêm cột 'role'",
    "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('admins') AND name = 'role')
       ALTER TABLE admins ADD role NVARCHAR(20) NOT NULL DEFAULT 'admin'"
);

// 2. full_name
step($conn, "Thêm cột 'full_name'",
    "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('admins') AND name = 'full_name')
       ALTER TABLE admins ADD full_name NVARCHAR(150) NULL"
);

// 3. created_at
step($conn, "Thêm cột 'created_at'",
    "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('admins') AND name = 'created_at')
       ALTER TABLE admins ADD created_at DATETIME NOT NULL DEFAULT GETDATE()"
);

// 4. Đảm bảo tài khoản admin có role = 'admin'
step($conn, "Set role='admin' cho username='admin'",
    "UPDATE admins SET role = 'admin' WHERE username = 'admin' AND (role IS NULL OR role = '')"
);

echo "\n<span style='color:#5f5'>====================================</span>\n";
echo "<span style='color:#5f5'>HOÀN TẤT MIGRATION</span>\n";
echo "<span style='color:#5f5'>====================================</span>\n\n";

// Hiện danh sách tài khoản hiện có
echo "Danh sách tài khoản hiện có:\n";
$r = sqlsrv_query($conn, "SELECT id, username, role, full_name FROM admins ORDER BY id");
if ($r) {
    printf("%-4s | %-20s | %-8s | %-30s\n", "ID", "Username", "Role", "Họ tên");
    echo str_repeat("-", 80) . "\n";
    while ($row = sqlsrv_fetch_array($r, SQLSRV_FETCH_ASSOC)) {
        printf("%-4s | %-20s | %-8s | %-30s\n",
            $row['id'],
            htmlspecialchars($row['username']),
            htmlspecialchars($row['role'] ?? '?'),
            htmlspecialchars($row['full_name'] ?? '—')
        );
    }
}

sqlsrv_close($conn);

echo "\n<span style='color:#fc0'>⚠ XÓA file setup_roles.php sau khi xong!</span>\n";
echo "\n<a href='admin/login.php' style='color:#6BBF59;'>→ Đến trang đăng nhập</a>\n";
echo "</pre>";

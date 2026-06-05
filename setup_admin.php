<?php
/**
 * Chạy file này 1 LẦN DUY NHẤT để tạo tài khoản admin.
 * Sau đó XÓA file này khỏi server!
 * Truy cập: http://localhost/acer_website/setup_admin.php
 */
require_once 'includes/db.php';

$username = 'admin';
$password = 'Admin@123'; // Đổi mật khẩu tại đây trước khi chạy

$hash = password_hash($password, PASSWORD_BCRYPT);
$conn = getConnection();

$sql  = "INSERT INTO admins (username, password_hash) VALUES (?, ?)";
$stmt = sqlsrv_query($conn, $sql, [$username, $hash]);

if ($stmt) {
    echo "<p style='font-family:sans-serif;color:green'>✓ Tạo tài khoản admin thành công!<br>";
    echo "Username: <b>$username</b><br>";
    echo "Password: <b>$password</b><br><br>";
    echo "<b style='color:red'>Hãy xóa file setup_admin.php ngay bây giờ!</b></p>";
} else {
    echo "<p style='font-family:sans-serif;color:red'>Lỗi: " . print_r(sqlsrv_errors(), true) . "</p>";
}
sqlsrv_close($conn);
?>

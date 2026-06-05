<?php
/**
 * Migration: thêm cột image_url vào bảng hero_slides
 * Chạy một lần tại: http://localhost/acer_website/setup_slide_image.php
 */
require_once 'includes/db.php';
$conn = getConnection();

$log = [];

// Kiểm tra cột đã tồn tại chưa
$check = sqlsrv_query($conn,
    "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_NAME='hero_slides' AND COLUMN_NAME='image_url'");
$exists = $check && ($r = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC)) && $r['c'] > 0;

if ($exists) {
    $log[] = ['ok', 'Cột <b>image_url</b> đã tồn tại — không cần thêm.'];
} else {
    $stmt = sqlsrv_query($conn, "ALTER TABLE hero_slides ADD image_url NVARCHAR(500) NULL");
    if ($stmt !== false) {
        $log[] = ['ok', 'Đã thêm cột <b>image_url</b> vào bảng <b>hero_slides</b>.'];
    } else {
        $errs = sqlsrv_errors();
        $log[] = ['err', 'Lỗi: ' . ($errs ? $errs[0]['message'] : 'Unknown error')];
    }
}

// Tạo thư mục lưu ảnh slide nếu chưa có
$dir = __DIR__ . '/assets/images/slides';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    $log[] = ['ok', 'Đã tạo thư mục <b>assets/images/slides/</b>.'];
} else {
    $log[] = ['ok', 'Thư mục <b>assets/images/slides/</b> đã tồn tại.'];
}

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Setup: slide image_url</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:600px;">
    <h4 class="mb-4">🖼 Migration: Hình ảnh cho Slider</h4>
    <?php foreach ($log as [$type, $msg]): ?>
    <div class="alert alert-<?= $type === 'ok' ? 'success' : 'danger' ?> py-2 px-3">
        <?= $type === 'ok' ? '✅' : '❌' ?> <?= $msg ?>
    </div>
    <?php endforeach; ?>
    <div class="mt-3 d-flex gap-2">
        <a href="admin/slides.php" class="btn btn-success">→ Quản lý Slider</a>
        <a href="index.php" class="btn btn-outline-secondary">Trang chủ</a>
    </div>
</div>
</body>
</html>

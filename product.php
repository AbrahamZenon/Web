<?php
require_once 'includes/db.php';
require_once 'includes/illustrations.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: products.php'); exit; }

$conn = getConnection();
$stmt = sqlsrv_query($conn,
    "SELECT p.id, p.name, p.specs, p.price, p.image_url, p.description,
            c.name AS category, c.slug AS cat_slug, c.id AS cat_id
     FROM products p JOIN categories c ON p.category_id = c.id
     WHERE p.id = ? AND p.visible = 1",
    [$id]);
$product = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : null;
if (!$product) { header('Location: products.php'); exit; }

// Lấy tags của sản phẩm
$product_tags = [];
$rt = @sqlsrv_query($conn,
    "SELECT t.id, t.name FROM tags t
     JOIN product_tags pt ON pt.tag_id = t.id
     WHERE pt.product_id = ?", [$id]);
if ($rt) while ($row = sqlsrv_fetch_array($rt, SQLSRV_FETCH_ASSOC)) $product_tags[] = $row;

// Lấy SP liên quan
$related = [];
$r2 = sqlsrv_query($conn,
    "SELECT TOP 4 p.id, p.name, p.specs, p.price, p.image_url, c.name AS category
     FROM products p JOIN categories c ON p.category_id = c.id
     WHERE p.category_id = ? AND p.id <> ? AND p.visible = 1
     ORDER BY p.id DESC",
    [$product['cat_id'], $id]);
if ($r2) while ($row = sqlsrv_fetch_array($r2, SQLSRV_FETCH_ASSOC)) $related[] = $row;
sqlsrv_close($conn);

function fmt($n) { return number_format($n,0,',','.') . ' ₫'; }
function parseSpecs($s) { return array_filter(array_map('trim', explode(',', $s))); }

$key = 'laptop';
$nl = mb_strtolower($product['name']);
$cl = mb_strtolower($product['category']);
if (str_contains($nl,'chuột')||str_contains($nl,'cestus'))  $key='mouse';
elseif (str_contains($nl,'bàn phím')||str_contains($nl,'aethon')) $key='keyboard';
elseif (str_contains($cl,'phụ kiện'))  $key='accessory';
elseif (str_contains($cl,'màn hình'))  $key='monitor';
elseif (str_contains($cl,'máy tính bàn')) $key='desktop';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> — Acer Vietnam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-500 d-flex align-items-center" href="index.php">
            <span class="logo-box">A</span> Acer Vietnam
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link active" href="products.php">Sản phẩm</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:13px;">
            <li class="breadcrumb-item"><a href="index.php" style="color:#3d7a1f;">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="products.php" style="color:#3d7a1f;">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="products.php?cat=<?= htmlspecialchars($product['cat_slug']) ?>" style="color:#3d7a1f;"><?= htmlspecialchars($product['category']) ?></a></li>
            <li class="breadcrumb-item active text-dark"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <!-- Chi tiết -->
    <div class="row g-5 mb-5">

        <!-- Ảnh -->
        <div class="col-md-5">
            <div class="product-visual">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= htmlspecialchars($product['image_url']) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         style="max-width:300px; max-height:280px; object-fit:contain;">
                <?php else: ?>
                    <?= getIllustration($key, 280, 220) ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin -->
        <div class="col-md-7 d-flex flex-column gap-3">
            <span class="badge-category"><?= htmlspecialchars($product['category']) ?></span>
            <h1 class="fs-3 fw-600 mb-0"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="fs-2 fw-600 mb-0" style="color:#3d7a1f;"><?= fmt($product['price']) ?></p>

            <?php if (!empty($product['specs'])): ?>
            <div class="specs-list">
                <p class="text-uppercase text-secondary mb-2" style="font-size:12px; letter-spacing:.05em;">Cấu hình</p>
                <ul class="ps-3 mb-0">
                    <?php foreach (parseSpecs($product['specs']) as $spec): ?>
                    <li><?= htmlspecialchars(trim($spec)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <a href="index.php#lien-he" class="btn btn-acer px-4">Liên hệ mua hàng</a>
                <a href="products.php" class="btn btn-outline-secondary">← Quay lại</a>
            </div>

            <?php if (!empty($product_tags)): ?>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <?php foreach ($product_tags as $tag): ?>
                <a href="products.php?tag=<?= $tag['id'] ?>"
                   style="font-size:12px; padding:3px 12px; background:#eaf7e4; color:#2e5c17; border:1px solid #b2dfa0; border-radius:20px; text-decoration:none;">
                    # <?= htmlspecialchars($tag['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mô tả chi tiết -->
    <?php if (!empty($product['description'])): ?>
    <div class="border rounded-3 p-4 mb-5">
        <h3 class="fs-5 fw-500 mb-1">Mô tả chi tiết</h3>
        <div class="section-bar"></div>
        <p class="mb-0 text-dark" style="font-size:14px; line-height:1.8; white-space:pre-line;"><?= htmlspecialchars($product['description']) ?></p>
    </div>
    <?php endif; ?>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($related)): ?>
    <div>
        <h3 class="fs-5 fw-500 mb-1">Sản phẩm liên quan</h3>
        <div class="section-bar"></div>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($related as $r): ?>
            <div class="col">
                <a href="product.php?id=<?= $r['id'] ?>" style="text-decoration:none;">
                <div class="product-card">
                    <div class="card-img-wrap">
                        <?php if (!empty($r['image_url'])): ?>
                            <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
                        <?php else: ?>
                            <?= getProductIllustration($r['name'], $r['category']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <div class="card-name"><?= htmlspecialchars($r['name']) ?></div>
                        <div class="card-specs"><?= htmlspecialchars($r['specs']) ?></div>
                        <div class="card-price"><?= fmt($r['price']) ?></div>
                    </div>
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Back to top -->
<button class="back-to-top" id="backToTop" title="Lên đầu trang">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M8 12V4M8 4L4 8M8 4L12 8" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const btt = document.getElementById('backToTop');
window.addEventListener('scroll', () => btt.classList.toggle('show', window.scrollY > 300));
btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>
</body>
</html>

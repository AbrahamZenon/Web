<?php
// ============================================================
//  TRANG CHỦ — index.php
//  Cấu trúc trang:
//    1. PHP: load dữ liệu (sản phẩm nổi bật + hero slides)
//    2. HTML: Navbar
//    3. HTML: Hero Slider (banner đầu trang)
//    4. HTML: Giới thiệu công ty (có logo)
//    5. HTML: Sản phẩm chính + Sản phẩm nổi bật
//    6. HTML: Tầm nhìn & Sứ mệnh
//    7. HTML: Đối tác
//    8. HTML: Liên hệ + bản đồ + footer
// ============================================================

require_once 'includes/db.php';         // kết nối DB (SQL Server)
require_once 'includes/illustrations.php'; // hàm vẽ SVG minh họa

$conn = getConnection();

// ── SẢN PHẨM NỔI BẬT (4 sản phẩm đầu tiên visible=1) ──────
// Thay đổi: sửa "ORDER BY p.id ASC" thành "ORDER BY p.id DESC"
//           để lấy sản phẩm mới nhất, hoặc thêm WHERE tùy ý.
// Ảnh sản phẩm lấy từ cột image_url trong bảng products.
// → Để thêm/sửa ảnh sản phẩm: Admin → Sản phẩm → Sửa → trường "Ảnh URL"
$result = sqlsrv_query($conn,
    "SELECT TOP 4 p.id, p.name, p.specs, p.price, p.image_url, c.name AS category
     FROM products p JOIN categories c ON p.category_id = c.id
     WHERE p.visible = 1 ORDER BY p.id ASC");
$featured = [];
if ($result) while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) $featured[] = $row;

// ── HERO SLIDES (banner slideshow đầu trang) ────────────────
// Dữ liệu lấy từ bảng hero_slides trong DB.
// → Để THÊM / SỬA / XÓA slide và ảnh: vào Admin → Slider
//    URL: http://localhost/acer_website/admin/slides.php
//
// Mỗi slide có thể có:
//   - image_url : ảnh sản phẩm thật (hiện bên PHẢI slide)
//                 lưu tại thư mục: assets/images/slides/
//                 hoặc URL bên ngoài (https://...)
//   - illustration: SVG tự vẽ (laptop/monitor/...) — dùng khi không có ảnh thật
//   - bg_gradient : màu nền gradient CSS
//   - badge_text, title_line1, title_line2, description, btn_text, btn_link
//
// Nếu bảng hero_slides chưa tồn tại hoặc trống → dùng slide fallback bên dưới.
$slides = [];
$slideStmt = @sqlsrv_query($conn,
    "SELECT * FROM hero_slides WHERE visible = 1 ORDER BY sort_order, id");
if ($slideStmt) {
    while ($row = sqlsrv_fetch_array($slideStmt, SQLSRV_FETCH_ASSOC)) $slides[] = $row;
}
if (empty($slides)) {
    // ── SLIDE FALLBACK (hiện khi DB chưa có dữ liệu) ──────────
    // Để sửa slide mặc định này: chỉnh trực tiếp các giá trị bên dưới.
    // Khi đã chạy setup_features.php và thêm slide qua Admin,
    // đoạn này sẽ tự động bị bỏ qua.
    $slides = [
        [
            'badge_text'  => 'Mới nhất 2024',
            'title_line1' => 'Predator Helios 300',
            'title_line2' => 'Gaming không giới hạn',
            'description' => 'Intel i7 • RTX 3070 • 144Hz QHD',
            'btn_text'    => 'Khám phá ngay →',
            'btn_link'    => 'products.php?cat=laptop-gaming',
            'bg_gradient' => 'linear-gradient(135deg,#1a3a0a 0%,#2e5c17 40%,#3d7a1f 70%,#6BBF59 100%)',
            'text_color'  => '#ffffff',
            'accent_color'=> '#6BBF59',
            'illustration'=> 'laptop',
            'image_url'   => '', // ← để trống hoặc điền đường dẫn ảnh, VD: 'assets/images/slides/helios.png'
        ],
        [
            'badge_text'  => 'Văn phòng',
            'title_line1' => 'Swift 3 & Aspire 5',
            'title_line2' => 'Làm việc hiệu quả mọi lúc',
            'description' => 'Mỏng nhẹ • Pin trâu • Giá tốt',
            'btn_text'    => 'Xem dòng văn phòng →',
            'btn_link'    => 'products.php?cat=laptop-vanphong',
            'bg_gradient' => 'linear-gradient(135deg,#0C447C 0%,#185FA5 50%,#378ADD 100%)',
            'text_color'  => '#ffffff',
            'accent_color'=> '#378ADD',
            'illustration'=> 'monitor',
            'image_url'   => '', // ← điền đường dẫn ảnh nếu muốn
        ],
        [
            'badge_text'  => 'Ưu đãi đặc biệt',
            'title_line1' => 'Phụ kiện chính hãng',
            'title_line2' => 'Chuột • Bàn phím • Hub',
            'description' => 'Bảo hành 12 tháng • Giao hàng toàn quốc',
            'btn_text'    => 'Xem phụ kiện →',
            'btn_link'    => 'products.php?cat=phu-kien',
            'bg_gradient' => 'linear-gradient(135deg,#5c1a0a 0%,#7a3f1f 40%,#c47a2a 80%,#f0b429 100%)',
            'text_color'  => '#ffffff',
            'accent_color'=> '#f0b429',
            'illustration'=> null,
            'image_url'   => '', // ← điền đường dẫn ảnh nếu muốn
        ],
    ];
}
sqlsrv_close($conn);

function fmt($n) { return number_format($n,0,',','.') . ' ₫'; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acer Vietnam — Trang chủ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ============================================================
     NAVBAR — thanh điều hướng cố định đầu trang
     Để thêm/xóa mục menu: sửa các thẻ <li> bên dưới
     ============================================================ -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-4">
        <!-- Logo chữ "A" + tên thương hiệu — sửa CSS .logo-box trong style.css -->
        <a class="navbar-brand fw-500 d-flex align-items-center" href="index.php">
            <span class="logo-box">A</span> Acer Vietnam
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span style="color:#d4f0c4; font-size:20px;">☰</span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Sản phẩm</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4" id="mainScroll">

    <!-- ============================================================
         SECTION 1 — HERO SLIDER (banner slideshow đầu trang)
         ============================================================
         CÁCH THAY ẢNH SLIDER:
           Cách 1 (khuyến nghị): Vào Admin → Slider
                  http://localhost/acer_website/admin/slides.php
                  → Nhấn "Sửa" trên slide cần đổi
                  → Mục "🖼 Ảnh sản phẩm thật": upload file hoặc dán URL
                  → Ảnh upload lưu vào: assets/images/slides/
           Cách 2 (fallback, chỉ khi DB trống): sửa 'image_url' trong
                  mảng $slides ở đầu file này (dòng ~65-90)
         ============================================================ -->
    <div id="heroBanner" class="carousel slide mb-4 rounded-4 overflow-hidden"
         data-bs-ride="carousel" data-bs-interval="5000"
         data-bs-touch="true" data-bs-wrap="true">

        <?php if (count($slides) > 1): ?>
        <!-- Chấm chỉ thị (indicator dots) bên dưới slide -->
        <div class="carousel-indicators">
            <?php foreach ($slides as $i => $_s): ?>
            <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="<?= $i ?>"
                    class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i+1 ?>"></button>
            <?php endforeach; ?>
        </div>
        <!-- Mũi tên trái/phải -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroBanner" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroBanner" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
        <?php endif; ?>

        <div class="carousel-inner">
        <?php foreach ($slides as $i => $s):
            $textColor   = htmlspecialchars($s['text_color']   ?? '#ffffff');
            $accentColor = htmlspecialchars($s['accent_color'] ?? '#6BBF59');
            $bg          = htmlspecialchars($s['bg_gradient']);
            $hasImg      = !empty($s['image_url']);
        ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div style="height:260px; background:<?= $bg ?>; display:flex; align-items:center; padding:0 48px; position:relative; overflow:hidden;">

                    <?php if ($hasImg): ?>
                    <!-- ── ẢNH SẢN PHẨM THẬT (bên phải slide) ──────────────
                         Nguồn: cột image_url trong bảng hero_slides
                         Thay ảnh: Admin → Slider → Sửa → "🖼 Ảnh sản phẩm thật"
                         ─────────────────────────────────────────────────── -->
                    <div style="position:absolute; right:0; top:0; bottom:0; width:42%;
                                display:flex; align-items:flex-end; justify-content:center;
                                pointer-events:none;">
                        <img src="<?= htmlspecialchars($s['image_url']) ?>"
                             alt="<?= htmlspecialchars($s['title_line1']) ?>"
                             style="max-height:280px; max-width:100%; object-fit:contain;
                                    filter:drop-shadow(0 8px 24px rgba(0,0,0,.45));
                                    transform:translateY(10px);">
                    </div>

                    <?php elseif (!empty($s['illustration'])): ?>
                    <!-- ── SVG MINH HỌA (fallback khi không có ảnh thật) ──
                         Giá trị hợp lệ: laptop, monitor, desktop, accessory,
                                         mouse, keyboard
                         SVG được vẽ bởi hàm getIllustration() trong
                         includes/illustrations.php
                         ─────────────────────────────────────────────────── -->
                    <div style="position:absolute;right:0;top:0;bottom:0;width:45%;opacity:.1;">
                        <?= getIllustration($s['illustration'], 400, 260) ?>
                    </div>
                    <?php endif; ?>

                    <!-- ── NỘI DUNG CHỮ (bên trái slide) ─────────────────
                         Thay đổi: Admin → Slider → Sửa slide
                         ─────────────────────────────────────────────────── -->
                    <div style="z-index:1; <?= $hasImg ? 'max-width:55%;' : '' ?>">
                        <?php if (!empty($s['badge_text'])): ?>
                        <span style="background:<?= $accentColor ?>; color:#fff; font-size:11px;
                                     font-weight:600; padding:3px 10px; border-radius:20px;
                                     text-transform:uppercase; letter-spacing:.05em;">
                            <?= htmlspecialchars($s['badge_text']) ?>
                        </span>
                        <?php endif; ?>
                        <h2 style="color:<?= $textColor ?>; font-size:2rem; font-weight:600;
                                   margin:10px 0 8px; line-height:1.2;">
                            <?= htmlspecialchars($s['title_line1']) ?>
                            <?php if (!empty($s['title_line2'])): ?>
                            <br><span style="color:<?= $textColor ?>; opacity:0.85;
                                             font-size:1.1rem; font-weight:400;">
                                <?= htmlspecialchars($s['title_line2']) ?>
                            </span>
                            <?php endif; ?>
                        </h2>
                        <?php if (!empty($s['description'])): ?>
                        <p style="color:<?= $textColor ?>; opacity:0.85; font-size:14px; margin-bottom:16px;">
                            <?= htmlspecialchars($s['description']) ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($s['btn_text']) && !empty($s['btn_link'])): ?>
                        <a href="<?= htmlspecialchars($s['btn_link']) ?>" class="btn btn-sm px-4 py-2"
                           style="background:#fff; color:#222; font-weight:500; border-radius:8px;">
                            <?= htmlspecialchars($s['btn_text']) ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <!-- /HERO SLIDER -->

    <!-- ============================================================
         SECTION 2 — GIỚI THIỆU CÔNG TY
         ============================================================
         CÁCH THAY LOGO / HÌNH ẢNH THƯƠNG HIỆU:
           Hiện tại: logo là chữ SVG "Acer" màu xanh (inline, dòng ~195-203)
           Để thay bằng ảnh thật:
             Xóa thẻ <svg>...</svg> bên dưới và thay bằng:
             <img src="assets/images/logo-acer.png" alt="Acer" style="max-width:180px;">
             (đặt file ảnh vào thư mục: assets/images/)
         CÁCH SỬA NỘI DUNG:
           - Đoạn văn giới thiệu: sửa 2 thẻ <p> trong col-md-9
           - Các số liệu thống kê (160+, 7000+, #5): sửa trong .stat-card bên dưới
         ============================================================ -->
    <section class="section" id="gioi-thieu">
        <h2 class="fs-4 fw-500 mb-1">Giới thiệu công ty</h2>
        <div class="section-bar"></div>
        <div class="row align-items-start g-3">

            <!-- Đoạn văn giới thiệu — sửa nội dung tại đây -->
            <div class="col-md-9">
                <p class="text-dark">Acer Inc. là tập đoàn công nghệ hàng đầu thế giới, thành lập năm 1976 tại Đài Loan. Với hơn 45 năm kinh nghiệm, Acer cung cấp các giải pháp công nghệ toàn diện cho người dùng cá nhân và doanh nghiệp trên 160 quốc gia.</p>
                <p class="text-dark mt-2">Tại Việt Nam, Acer hiện là một trong những thương hiệu máy tính được tin dùng nhất, với mạng lưới phân phối rộng khắp 63 tỉnh thành và dịch vụ hậu mãi chuyên nghiệp.</p>
            </div>

            <!-- ── LOGO THƯƠNG HIỆU ────────────────────────────────────
                 Đang dùng: SVG chữ "Acer" màu xanh (#83B81A)
                 ĐỂ THAY BẰNG ẢNH LOGO THẬT:
                   1. Xóa toàn bộ thẻ <svg>...</svg> bên dưới
                   2. Thay bằng: <img src="assets/images/logo.png"
                                      alt="Acer" style="max-width:180px;">
                   3. Copy file logo vào thư mục: assets/images/
                 ─────────────────────────────────────────────────────── -->
            <div class="col-md-3 text-center">
                <!-- XÓA ĐOẠN NÀY VÀ THAY BẰNG <img> NẾU CÓ FILE LOGO -->
                <svg viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg" style="max-width:180px;">
                    <text x="100" y="52" text-anchor="middle"
                          font-family="Arial,Helvetica,sans-serif"
                          font-size="52" font-weight="bold"
                          letter-spacing="2"
                          fill="#83B81A">Acer</text>
                </svg>
                <!-- VÍ DỤ thay logo bằng ảnh: -->
                <!-- <img src="assets/images/logo-acer.png" alt="Acer Logo" style="max-width:180px;"> -->
            </div>
        </div>

        <!-- Số liệu thống kê — sửa stat-num và stat-lbl tại đây -->
        <div class="row g-3 mt-2">
            <div class="col-4"><div class="stat-card"><div class="stat-num">160+</div><div class="stat-lbl">quốc gia</div></div></div>
            <div class="col-4"><div class="stat-card"><div class="stat-num">7000+</div><div class="stat-lbl">nhân viên</div></div></div>
            <div class="col-4"><div class="stat-card"><div class="stat-num">#5</div><div class="stat-lbl">PC toàn cầu</div></div></div>
        </div>
    </section>
    <!-- /GIỚI THIỆU -->

    <!-- ============================================================
         SECTION 3 — SẢN PHẨM CHÍNH + SẢN PHẨM NỔI BẬT
         ============================================================
         - 4 ô danh mục (Laptop, Màn hình, ...): sửa mảng $cats bên dưới
         - Sản phẩm nổi bật: lấy tự động từ DB (4 sản phẩm đầu tiên)
           Ảnh sản phẩm: từ cột image_url — thêm qua Admin → Sản phẩm → Sửa
         ============================================================ -->
    <section class="section" id="san-pham">
        <h2 class="fs-4 fw-500 mb-1">Sản phẩm chính</h2>
        <div class="section-bar"></div>

        <!-- 4 ô danh mục — sửa title, desc, slug tại đây -->
        <div class="row g-3 mb-4">
            <?php
            $cats = [
                ['key'=>'laptop',    'slug'=>'laptop-gaming',    'title'=>'Laptop',       'desc'=>'Aspire, Swift, Predator, Nitro — đa dạng từ phổ thông đến gaming cao cấp.'],
                ['key'=>'monitor',   'slug'=>'man-hinh',         'title'=>'Màn hình',     'desc'=>'Gaming 144Hz–360Hz, đồ họa 4K, màn hình văn phòng IPS Full HD.'],
                ['key'=>'desktop',   'slug'=>'may-tinh-ban',     'title'=>'Máy tính bàn', 'desc'=>'Veriton cho doanh nghiệp, Aspire Desktop cho gia đình và văn phòng.'],
                ['key'=>'accessory', 'slug'=>'phu-kien',         'title'=>'Phụ kiện',     'desc'=>'Chuột, bàn phím, túi xách, hub và phụ kiện chính hãng Acer.'],
            ];
            foreach ($cats as $c): ?>
            <div class="col-md-6">
                <a href="products.php?cat=<?= $c['slug'] ?>" style="text-decoration:none;">
                <div class="p-3 border rounded-3 d-flex align-items-center gap-3 h-100 cat-card">
                    <!-- Icon SVG danh mục — thay key để đổi icon -->
                    <div class="cat-icon"><?= getIllustration($c['key'], 72, 56) ?></div>
                    <div>
                        <p class="fw-500 mb-1" style="color:#111;"><?= $c['title'] ?></p>
                        <p class="text-secondary mb-0" style="font-size:13px;"><?= $c['desc'] ?></p>
                    </div>
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sản phẩm nổi bật — load tự động từ DB -->
        <?php if (!empty($featured)): ?>
        <p class="fw-500 text-secondary mb-3" style="font-size:14px;">Sản phẩm nổi bật</p>
        <div class="row row-cols-2 row-cols-md-4 g-3 mb-3">
            <?php foreach ($featured as $p): ?>
            <div class="col">
                <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration:none;">
                <div class="product-card">
                    <div class="card-img-wrap">
                        <?php $b = getProductBadge($p['name'], (int)$p['price']); if ($b): ?>
                        <span class="product-badge <?= $b['class'] ?>"><?= $b['label'] ?></span>
                        <?php endif; ?>
                        <!-- Ảnh sản phẩm: ưu tiên image_url từ DB, fallback SVG -->
                        <!-- Để thêm ảnh: Admin → Sản phẩm → Sửa → trường "Ảnh URL" -->
                        <?php if (!empty($p['image_url'])): ?>
                            <img src="<?= htmlspecialchars($p['image_url']) ?>"
                                 alt="<?= htmlspecialchars($p['name']) ?>">
                        <?php else: ?>
                            <?= getProductIllustration($p['name'], $p['category']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="card-specs"><?= htmlspecialchars($p['specs']) ?></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="card-price"><?= fmt($p['price']) ?></span>
                            <span class="btn btn-outline-acer btn-sm" style="font-size:11px;">Chi tiết</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <a href="products.php" class="btn btn-acer px-4">Xem tất cả sản phẩm →</a>
    </section>
    <!-- /SẢN PHẨM -->

    <!-- ============================================================
         SECTION 4 — TẦM NHÌN & SỨ MỆNH
         Sửa nội dung: chỉnh trực tiếp 2 thẻ <p> bên dưới
         ============================================================ -->
    <section class="section" id="tam-nhin">
        <h2 class="fs-4 fw-500 mb-1">Tầm nhìn &amp; sứ mệnh</h2>
        <div class="section-bar"></div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-4 rounded-3 d-flex gap-3 align-items-start h-100" style="background:#eaf7e4;">
                    <?= getIllustration('vision', 80, 64) ?>
                    <div>
                        <p class="fw-500 mb-2" style="color:#2e5c17;">Tầm nhìn</p>
                        <p class="mb-0 text-dark" style="font-size:13px;">Trở thành thương hiệu công nghệ được yêu thích nhất, mang đến trải nghiệm số tốt nhất cho mọi người.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-3 d-flex gap-3 align-items-start h-100" style="background:#eaf7e4;">
                    <?= getIllustration('mission', 80, 64) ?>
                    <div>
                        <p class="fw-500 mb-2" style="color:#2e5c17;">Sứ mệnh</p>
                        <p class="mb-0 text-dark" style="font-size:13px;">Phá bỏ rào cản giữa con người và công nghệ, tạo ra các sản phẩm thông minh, bền vững và dễ tiếp cận.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /TẦM NHÌN -->

    <!-- ============================================================
         SECTION 5 — ĐỐI TÁC
         Để thêm/xóa logo đối tác: sửa mảng ['intel','nvidia',...] bên dưới
         SVG được vẽ bởi getIllustration() trong includes/illustrations.php
         ============================================================ -->
    <section class="section" id="doi-tac">
        <h2 class="fs-4 fw-500 mb-1">Đối tác &amp; khách hàng</h2>
        <div class="section-bar"></div>
        <p class="text-dark mb-3">Acer hợp tác cùng các tập đoàn công nghệ hàng đầu và phục vụ hàng triệu khách hàng từ cá nhân, doanh nghiệp đến các tổ chức giáo dục, chính phủ.</p>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <?php foreach (['intel','nvidia','microsoft','google'] as $p): ?>
            <div class="border rounded-3 p-2"><?= getIllustration($p, 140, 70) ?></div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- /ĐỐI TÁC -->

    <!-- ============================================================
         SECTION 6 — LIÊN HỆ + BẢN ĐỒ + FOOTER
         Sửa thông tin liên hệ: chỉnh các thẻ <p> trong .contact-card
         Sửa bản đồ Google Maps: thay src của <iframe>
         ============================================================ -->
    <section class="section mb-0" id="lien-he">
        <h2 class="fs-4 fw-500 mb-1">Liên hệ</h2>
        <div class="section-bar"></div>

        <!-- Thông tin liên hệ — sửa nội dung 4 ô bên dưới -->
        <div class="row g-3 mb-4">
            <div class="col-md-6"><div class="contact-card h-100"><p class="label">Địa chỉ</p><p class="mb-0" style="font-size:14px;">Tầng 15, Tòa nhà Viettel, 285 Cách Mạng Tháng 8, TP.HCM</p></div></div>
            <div class="col-md-6"><div class="contact-card h-100"><p class="label">Hotline</p><p class="mb-0" style="font-size:14px;">1800 599 974 (miễn phí)</p></div></div>
            <div class="col-md-6"><div class="contact-card h-100"><p class="label">Email</p><p class="mb-0" style="font-size:14px;">support@acer.com.vn</p></div></div>
            <div class="col-md-6"><div class="contact-card h-100"><p class="label">Giờ làm việc</p><p class="mb-0" style="font-size:14px;">Thứ 2 – Thứ 7: 8:00 – 17:30</p></div></div>
        </div>

        <!-- Google Maps embed — thay src bên dưới để đổi vị trí bản đồ -->
        <div class="rounded-3 overflow-hidden mb-4" style="height:320px; border:1px solid #e8e8e8;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.3985!2d106.6600!3d10.7769!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ede0033ba27%3A0x47bfbb0b22fe9a95!2s285%20C%C3%A1ch%20M%E1%BA%A1ng%20Th%C3%A1ng%208%2C%20Ph%C6%B0%E1%BB%9Dng%2012%2C%20Q.%20Ph%C3%BA%20Nhu%E1%BA%ADn%2C%20TP.HCM!5e0!3m2!1svi!2svn!4v1"
                width="100%" height="100%"
                style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <!-- Footer banner xanh — sửa tiêu đề và mô tả tại đây -->
        <div class="rounded-4 p-4 mt-2" style="background:linear-gradient(135deg,#2e5c17 0%,#3d7a1f 50%,#6BBF59 100%);">
            <div class="row align-items-center g-3">
                <div class="col-md-7">
                    <p class="fw-600 mb-1" style="color:#fff; font-size:18px;">Khám phá thế giới công nghệ Acer</p>
                    <p class="mb-0" style="color:#c5e8b0; font-size:13px;">Hơn 45 năm đổi mới — Hàng triệu người tin dùng — 160+ quốc gia</p>
                </div>
                <div class="col-md-5 d-flex gap-2 justify-content-md-end flex-wrap">
                    <a href="products.php" class="btn btn-sm px-4 py-2 fw-500"
                       style="background:#fff; color:#3d7a1f; border-radius:8px;">
                        Xem sản phẩm
                    </a>
                    <a href="mailto:support@acer.com.vn" class="btn btn-sm px-4 py-2 fw-500"
                       style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.4); border-radius:8px;">
                        Gửi email
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer bottom — sửa link và copyright tại đây -->
        <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <!-- ── LOGO FOOTER (chữ SVG) ──────────────────────────────
                     Để thay bằng ảnh logo: xóa <svg> và dùng <img>
                     VD: <img src="assets/images/logo.png" style="height:22px;">
                     ──────────────────────────────────────────────────── -->
                <svg viewBox="0 0 80 28" xmlns="http://www.w3.org/2000/svg" style="height:22px;">
                    <text x="40" y="22" text-anchor="middle" font-family="Arial,sans-serif"
                          font-size="22" font-weight="bold" fill="#83B81A">Acer</text>
                </svg>
                <span style="font-size:12px; color:#888;">Vietnam</span>
            </div>
            <p class="mb-0" style="font-size:12px; color:#aaa;">© <?= date('Y') ?> Acer Inc. All rights reserved.</p>
            <div class="d-flex gap-3">
                <a href="#" style="font-size:12px; color:#888;">Chính sách bảo mật</a>
                <a href="#" style="font-size:12px; color:#888;">Điều khoản</a>
                <a href="#" style="font-size:12px; color:#888;">Hỗ trợ</a>
            </div>
        </div>
    </section>
    <!-- /LIÊN HỆ & FOOTER -->

</div>

<!-- Back to top button -->
<button class="back-to-top" id="backToTop" title="Lên đầu trang">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M8 12V4M8 4L4 8M8 4L12 8" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── HERO CAROUSEL — khởi tạo qua JS API, chặn click khi đang trượt ──
const heroEl = document.getElementById('heroBanner');
if (heroEl) {
    const heroCarousel = bootstrap.Carousel.getOrCreateInstance(heroEl, {
        interval: 5000,
        ride: 'carousel',
        wrap: true,
        touch: true
    });
    let sliding = false;
    heroEl.addEventListener('slide.bs.carousel', () => { sliding = true; });
    heroEl.addEventListener('slid.bs.carousel',  () => { sliding = false; });
    // Chống bấm indicator liên tục gây kẹt animation
    heroEl.querySelectorAll('.carousel-indicators button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (sliding) { e.preventDefault(); e.stopPropagation(); return; }
            const idx = parseInt(btn.getAttribute('data-bs-slide-to'), 10);
            e.preventDefault();
            heroCarousel.to(idx);
        });
    });
}

// Back to top button
const btt = document.getElementById('backToTop');
window.addEventListener('scroll', () => btt.classList.toggle('show', window.scrollY > 300));
btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>
</body>
</html>

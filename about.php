<?php
require_once 'includes/illustrations.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu — Acer Vietnam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-500 d-flex align-items-center" href="index.php">
            <span class="logo-box">A</span> Acer Vietnam
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span style="color:#d4f0c4; font-size:20px;">☰</span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link active" href="about.php">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Sản phẩm</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<div style="background:linear-gradient(135deg,#1a3a0a 0%,#2e5c17 45%,#3d7a1f 75%,#6BBF59 100%); padding:56px 0 40px;">
    <div class="container text-center">
        <svg viewBox="0 0 160 50" xmlns="http://www.w3.org/2000/svg" style="height:42px; margin-bottom:12px;">
            <text x="80" y="40" text-anchor="middle" font-family="Arial,Helvetica,sans-serif"
                  font-size="42" font-weight="bold" letter-spacing="2" fill="#83B81A">Acer</text>
        </svg>
        <h1 style="color:#fff; font-size:1.6rem; font-weight:600; margin-bottom:8px;">Giới thiệu về Acer Vietnam</h1>
        <p style="color:#c5e8b0; font-size:14px; max-width:520px; margin:0 auto;">
            Hơn 45 năm đổi mới công nghệ — phục vụ hàng triệu người dùng tại 160+ quốc gia
        </p>
    </div>
</div>

<div class="container py-5">

    <!-- Stats -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">1976</div><div class="stat-lbl">Năm thành lập</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">160+</div><div class="stat-lbl">Quốc gia</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">7 000+</div><div class="stat-lbl">Nhân viên</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">#5</div><div class="stat-lbl">PC toàn cầu</div></div></div>
    </div>

    <!-- About text + logo -->
    <div class="row g-5 align-items-center mb-5">
        <div class="col-md-7">
            <h2 class="fs-4 fw-500 mb-1">Về chúng tôi</h2>
            <div class="section-bar"></div>
            <p class="text-dark mb-3">
                Acer Inc. là tập đoàn công nghệ hàng đầu thế giới, được thành lập năm 1976 tại Đài Loan bởi Stan Shih và các cộng sự.
                Khởi đầu với tên gọi Multitech, công ty đã phát triển mạnh mẽ và trở thành Acer năm 1987 — một thương hiệu được tin dùng trên toàn cầu.
            </p>
            <p class="text-dark mb-3">
                Tại Việt Nam, Acer hiện là một trong những thương hiệu máy tính được tin dùng nhất,
                với mạng lưới phân phối rộng khắp 63 tỉnh thành và dịch vụ hậu mãi chuyên nghiệp.
                Từ laptop phổ thông đến gaming cao cấp, từ màn hình đồ họa đến máy tính bàn doanh nghiệp —
                Acer đáp ứng mọi nhu cầu công nghệ của người dùng Việt.
            </p>
            <p class="text-dark">
                Với cam kết về chất lượng, đổi mới và bền vững, Acer không ngừng đầu tư vào nghiên cứu phát triển
                để mang đến những sản phẩm tốt nhất cho người dùng ở mọi phân khúc.
            </p>
        </div>
        <div class="col-md-5">
            <div class="p-4 rounded-4 text-center" style="background:var(--acer-green-light);">
                <?= getIllustration('laptop', 260, 180) ?>
                <p class="mt-3 mb-0" style="font-size:13px; color:#3d7a1f; font-weight:500;">
                    Hơn 45 năm công nghệ đỉnh cao
                </p>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <h2 class="fs-4 fw-500 mb-1">Lịch sử phát triển</h2>
            <div class="section-bar"></div>
            <div class="timeline mt-3">
                <div class="timeline-item">
                    <div class="timeline-year">1976</div>
                    <div class="timeline-text">Thành lập với tên <strong>Multitech</strong> tại Đài Loan, chuyên sản xuất linh kiện điện tử và vi xử lý.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">1987</div>
                    <div class="timeline-text">Đổi tên thành <strong>Acer</strong> và niêm yết trên sàn chứng khoán Đài Loan. Ra mắt laptop đầu tiên.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">1998</div>
                    <div class="timeline-text">Mở rộng thị trường toàn cầu, gia nhập Top 5 thương hiệu máy tính lớn nhất thế giới.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2008</div>
                    <div class="timeline-text">Mua lại <strong>Gateway</strong> và <strong>Packard Bell</strong>, củng cố vị thế tại thị trường Bắc Mỹ và châu Âu.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2012</div>
                    <div class="timeline-text">Ra mắt dòng <strong>Predator</strong> — thương hiệu gaming cao cấp được game thủ toàn cầu yêu thích.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2018</div>
                    <div class="timeline-text">Acer đặt chân vào thị trường <strong>e-sports</strong>, tài trợ cho các giải đấu lớn quốc tế.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2021</div>
                    <div class="timeline-text">Cam kết phát triển bền vững — ra mắt dòng sản phẩm <strong>Vero</strong> thân thiện môi trường.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2024</div>
                    <div class="timeline-text">Tích hợp <strong>AI</strong> vào dòng sản phẩm mới, hướng đến kỷ nguyên máy tính thông minh.</div>
                </div>
            </div>
        </div>

        <!-- Tầm nhìn & Sứ mệnh + Giá trị cốt lõi -->
        <div class="col-md-6">
            <h2 class="fs-4 fw-500 mb-1">Tầm nhìn &amp; Sứ mệnh</h2>
            <div class="section-bar"></div>
            <div class="d-flex flex-column gap-3 mt-3">
                <div class="p-4 rounded-3 d-flex gap-3 align-items-start" style="background:#eaf7e4;">
                    <?= getIllustration('vision', 64, 52) ?>
                    <div>
                        <p class="fw-500 mb-1" style="color:#2e5c17;">Tầm nhìn</p>
                        <p class="mb-0 text-dark" style="font-size:13px; line-height:1.7;">
                            Trở thành thương hiệu công nghệ được yêu thích nhất, mang đến trải nghiệm số tốt nhất cho mọi người trên toàn thế giới.
                        </p>
                    </div>
                </div>
                <div class="p-4 rounded-3 d-flex gap-3 align-items-start" style="background:#eaf7e4;">
                    <?= getIllustration('mission', 64, 52) ?>
                    <div>
                        <p class="fw-500 mb-1" style="color:#2e5c17;">Sứ mệnh</p>
                        <p class="mb-0 text-dark" style="font-size:13px; line-height:1.7;">
                            Phá bỏ rào cản giữa con người và công nghệ, tạo ra các sản phẩm thông minh, bền vững và dễ tiếp cận cho mọi người.
                        </p>
                    </div>
                </div>
            </div>

            <h2 class="fs-4 fw-500 mb-1 mt-4">Giá trị cốt lõi</h2>
            <div class="section-bar"></div>
            <div class="row g-3 mt-1">
                <?php
                $values = [
                    ['icon'=>'💡','title'=>'Đổi mới', 'desc'=>'Không ngừng nghiên cứu và phát triển sản phẩm tiên tiến.'],
                    ['icon'=>'🌱','title'=>'Bền vững', 'desc'=>'Cam kết giảm tác động môi trường qua từng sản phẩm.'],
                    ['icon'=>'🤝','title'=>'Tin cậy',  'desc'=>'Xây dựng niềm tin bằng chất lượng và dịch vụ tốt nhất.'],
                    ['icon'=>'🌍','title'=>'Toàn cầu', 'desc'=>'Hiện diện tại 160+ quốc gia, phục vụ đa dạng khách hàng.'],
                ];
                foreach ($values as $v): ?>
                <div class="col-6">
                    <div class="p-3 border rounded-3 h-100" style="border-color:#e8e8e8 !important;">
                        <div style="font-size:22px; margin-bottom:6px;"><?= $v['icon'] ?></div>
                        <p class="fw-500 mb-1" style="font-size:14px; color:#2e5c17;"><?= $v['title'] ?></p>
                        <p class="mb-0 text-secondary" style="font-size:12px; line-height:1.5;"><?= $v['desc'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Đối tác -->
    <div class="mb-5">
        <h2 class="fs-4 fw-500 mb-1">Đối tác &amp; Khách hàng</h2>
        <div class="section-bar"></div>
        <p class="text-dark mb-3">
            Acer hợp tác cùng các tập đoàn công nghệ hàng đầu và phục vụ hàng triệu khách hàng
            từ cá nhân, doanh nghiệp đến các tổ chức giáo dục và chính phủ.
        </p>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <?php foreach (['intel','nvidia','microsoft','google'] as $p): ?>
            <div class="border rounded-3 p-2" style="border-color:#e8e8e8 !important;">
                <?= getIllustration($p, 140, 70) ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Văn phòng & Bản đồ -->
    <div class="mb-4">
        <h2 class="fs-4 fw-500 mb-1">Văn phòng Việt Nam</h2>
        <div class="section-bar"></div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100">
                    <p class="mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#999; font-weight:600;">Địa chỉ</p>
                    <p class="mb-0" style="font-size:14px; color:#333;">Tầng 15, Tòa nhà Viettel, 285 Cách Mạng Tháng 8, TP.HCM</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100">
                    <p class="mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#999; font-weight:600;">Hotline</p>
                    <p class="mb-0" style="font-size:14px; color:#333;">1800 599 974 (miễn phí)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100">
                    <p class="mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#999; font-weight:600;">Email</p>
                    <p class="mb-0" style="font-size:14px; color:#333;">support@acer.com.vn</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100">
                    <p class="mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#999; font-weight:600;">Giờ làm việc</p>
                    <p class="mb-0" style="font-size:14px; color:#333;">Thứ 2 – Thứ 7: 8:00 – 17:30</p>
                </div>
            </div>
        </div>
        <div class="rounded-3 overflow-hidden" style="height:320px; border:1px solid #e8e8e8;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.3985!2d106.6600!3d10.7769!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ede0033ba27%3A0x47bfbb0b22fe9a95!2s285%20C%C3%A1ch%20M%E1%BA%A1ng%20Th%C3%A1ng%208%2C%20Ph%C6%B0%E1%BB%9Dng%2012%2C%20Q.%20Ph%C3%BA%20Nhu%E1%BA%ADn%2C%20TP.HCM!5e0!3m2!1svi!2svn!4v1"
                width="100%" height="100%"
                style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    <!-- CTA -->
    <div class="rounded-4 p-4" style="background:linear-gradient(135deg,#2e5c17 0%,#3d7a1f 50%,#6BBF59 100%);">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <p class="fw-600 mb-1" style="color:#fff; font-size:18px;">Khám phá thế giới công nghệ Acer</p>
                <p class="mb-0" style="color:#c5e8b0; font-size:13px;">Hơn 45 năm đổi mới — Hàng triệu người tin dùng — 160+ quốc gia</p>
            </div>
            <div class="col-md-5 d-flex gap-2 justify-content-md-end flex-wrap">
                <a href="products.php" class="btn btn-sm px-4 py-2 fw-500"
                   style="background:#fff; color:#3d7a1f; border-radius:8px;">Xem sản phẩm</a>
                <a href="mailto:support@acer.com.vn" class="btn btn-sm px-4 py-2 fw-500"
                   style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.4); border-radius:8px;">Gửi email</a>
            </div>
        </div>
    </div>

    <!-- Footer bottom -->
    <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
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

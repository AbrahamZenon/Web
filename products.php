<?php
require_once 'includes/db.php';
require_once 'includes/illustrations.php';

$conn = getConnection();
$cats = [];
$r = sqlsrv_query($conn, "SELECT id, name, slug FROM categories ORDER BY id");
if ($r) while ($row = sqlsrv_fetch_array($r, SQLSRV_FETCH_ASSOC)) $cats[] = $row;

$products = [];
$r2 = sqlsrv_query($conn,
    "SELECT p.id, p.name, p.specs, p.price, p.image_url, c.name AS category, c.slug AS cat_slug
     FROM products p JOIN categories c ON p.category_id = c.id
     WHERE p.visible = 1 ORDER BY p.category_id, p.name");
if ($r2) while ($row = sqlsrv_fetch_array($r2, SQLSRV_FETCH_ASSOC)) $products[] = $row;
sqlsrv_close($conn);

$grouped = [];
foreach ($products as $p) $grouped[$p['category']][] = $p;

$allPrices   = array_column($products, 'price');
$globalMin   = !empty($allPrices) ? (int)min($allPrices) : 0;
$globalMax   = !empty($allPrices) ? (int)max($allPrices) : 100000000;

function fmt($n) { return number_format($n,0,',','.') . ' ₫'; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm — Acer Vietnam</title>
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
                <li class="nav-item"><a class="nav-link" href="about.php">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link active" href="products.php">Sản phẩm</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">

    <?php $activeCat = trim($_GET['cat'] ?? 'all'); ?>

    <!-- ── FILTER BAR ── -->
    <div class="filter-bar">
        <div class="row g-3 align-items-start">
            <!-- Search -->
            <div class="col-md-5">
                <label class="form-label">Tìm kiếm sản phẩm</label>
                <div class="search-wrap">
                    <svg class="search-icon" width="16" height="16" viewBox="0 0 14 14" fill="none">
                        <circle cx="6" cy="6" r="4.5" stroke="#999" stroke-width="1.5"/>
                        <path d="M9.5 9.5L12 12" stroke="#999" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="searchInput" class="search-input" placeholder="Nhập tên, cấu hình...">
                    <button type="button" id="searchClear" class="search-clear" onclick="clearSearch()" title="Xóa">&times;</button>
                </div>
                <p class="search-hint">Gõ tên sản phẩm, CPU, GPU, RAM... để lọc nhanh.</p>
            </div>
            <!-- Price range -->
            <div class="col-md-7">
                <label class="form-label">Khoảng giá</label>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <input type="number" id="priceMin" class="form-control form-control-sm"
                           placeholder="Từ" style="max-width:120px;" step="500000" min="0">
                    <span class="text-secondary">—</span>
                    <input type="number" id="priceMax" class="form-control form-control-sm"
                           placeholder="Đến" style="max-width:120px;" step="500000" min="0">
                    <span style="font-size:13px; color:#555;">₫</span>
                    <button onclick="clearPriceFilter()" class="btn btn-sm btn-outline-secondary" style="font-size:12px;">Xóa</button>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="price-preset" data-min="0" data-max="15000000">Dưới 15 triệu</button>
                    <button class="price-preset" data-min="15000000" data-max="25000000">15 – 25 triệu</button>
                    <button class="price-preset" data-min="25000000" data-max="">Trên 25 triệu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CATEGORY TABS ── -->
    <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom">
        <button class="cat-tab <?= $activeCat === 'all' ? 'active' : '' ?>" data-cat="all">Tất cả</button>
        <?php foreach ($cats as $cat): ?>
        <button class="cat-tab <?= $activeCat === $cat['slug'] ? 'active' : '' ?>"
                data-cat="<?= htmlspecialchars($cat['slug']) ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- No results message -->
    <div class="no-results" id="noResults">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" style="margin-bottom:12px; opacity:.3;">
            <circle cx="18" cy="18" r="12" stroke="#999" stroke-width="2"/>
            <path d="M27 27L36 36" stroke="#999" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p>Không tìm thấy sản phẩm phù hợp.</p>
    </div>

    <!-- ── PRODUCT GRID ── -->
    <?php if (empty($grouped)): ?>
        <div class="text-center text-secondary py-5">Chưa có sản phẩm nào.</div>
    <?php else: ?>
        <?php foreach ($grouped as $catName => $items):
            $slug = $items[0]['cat_slug']; ?>
        <div class="category-section mb-5" data-cat="<?= htmlspecialchars($slug) ?>">
            <h2 class="fs-5 fw-500 mb-1"><?= htmlspecialchars($catName) ?></h2>
            <div class="section-bar"></div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                <?php foreach ($items as $p):
                    $badge = getProductBadge($p['name'], (int)$p['price']);
                    $specsJson = htmlspecialchars(json_encode($p['specs']), ENT_QUOTES);
                ?>
                <div class="col product-col"
                     data-name="<?= htmlspecialchars(mb_strtolower($p['name']), ENT_QUOTES) ?>"
                     data-specs="<?= htmlspecialchars(mb_strtolower((string)$p['specs']), ENT_QUOTES) ?>"
                     data-price="<?= (int)$p['price'] ?>">
                    <div class="product-card"
                         data-id="<?= $p['id'] ?>"
                         data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                         data-price="<?= (int)$p['price'] ?>"
                         data-specs="<?= htmlspecialchars((string)$p['specs'], ENT_QUOTES) ?>"
                         data-category="<?= htmlspecialchars($catName, ENT_QUOTES) ?>">
                        <div class="card-img-wrap">
                            <?php if ($badge): ?>
                            <span class="product-badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                            <?php endif; ?>
                            <?php if (!empty($p['image_url'])): ?>
                                <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                            <?php else: ?>
                                <?= getProductIllustration($p['name'], $p['category']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-3">
                            <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="card-specs"><?= htmlspecialchars((string)$p['specs']) ?></div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="card-price"><?= fmt($p['price']) ?></span>
                                <a href="product.php?id=<?= $p['id'] ?>"
                                   onclick="event.stopPropagation();"
                                   class="btn btn-outline-acer btn-sm" style="font-size:11px;">
                                    Xem chi tiết
                                </a>
                            </div>
                            <!-- Compare checkbox -->
                            <label class="compare-check-wrap" onclick="event.stopPropagation();">
                                <input type="checkbox" class="compare-cb" onchange="toggleCompare(this)">
                                So sánh
                            </label>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<!-- ── COMPARE FLOATING BAR ── -->
<div class="compare-bar" id="compareBar">
    <span style="font-size:13px; font-weight:500; color:#333; flex-shrink:0;">So sánh:</span>
    <div id="compareSlots" class="d-flex gap-2 flex-wrap flex-grow-1"></div>
    <button class="btn btn-acer btn-sm px-4" onclick="openCompareModal()" style="flex-shrink:0;">So sánh ngay</button>
    <button class="btn btn-outline-secondary btn-sm" onclick="clearCompare()" style="flex-shrink:0;">Xóa tất cả</button>
</div>

<!-- ── COMPARE MODAL ── -->
<div class="modal fade" id="compareModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #eee;">
                <h5 class="modal-title fw-500" style="color:#2e5c17;">So sánh sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="compareTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── BACK TO TOP ── -->
<button class="back-to-top" id="backToTop" title="Lên đầu trang">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M8 12V4M8 4L4 8M8 4L12 8" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── State ──────────────────────────────────────────────
let currentCat = '<?= $activeCat ?>';
let compareList = [];
const MAX_COMPARE = 3;

// ── Filtering ──────────────────────────────────────────
function applyFilters() {
    const q      = document.getElementById('searchInput').value.trim().toLowerCase();
    const minVal = parseInt(document.getElementById('priceMin').value) || 0;
    const maxVal = parseInt(document.getElementById('priceMax').value) || Infinity;

    let total = 0;
    document.querySelectorAll('.product-col').forEach(col => {
        const name  = col.dataset.name;
        const specs = col.dataset.specs;
        const price = parseInt(col.dataset.price);
        const sec   = col.closest('.category-section');

        const matchSearch = !q || name.includes(q) || specs.includes(q);
        const matchPrice  = price >= minVal && price <= maxVal;
        const matchCat    = currentCat === 'all' || sec?.dataset.cat === currentCat;

        const show = matchSearch && matchPrice && matchCat;
        col.style.display = show ? '' : 'none';
        if (show) total++;
    });

    // Hide/show category headings
    document.querySelectorAll('.category-section').forEach(sec => {
        const hasVisible = [...sec.querySelectorAll('.product-col')].some(c => c.style.display !== 'none');
        sec.style.display = hasVisible ? '' : 'none';
    });

    document.getElementById('noResults').classList.toggle('show', total === 0);
}

function filterCat(cat) {
    currentCat = cat;
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`.cat-tab[data-cat="${cat}"]`)?.classList.add('active');
    applyFilters();
}

function clearSearch() {
    const input = document.getElementById('searchInput');
    input.value = '';
    document.getElementById('searchClear').classList.remove('show');
    input.focus();
    applyFilters();
}

function clearPriceFilter() {
    document.getElementById('priceMin').value = '';
    document.getElementById('priceMax').value = '';
    document.querySelectorAll('.price-preset').forEach(b => b.classList.remove('active'));
    applyFilters();
}

// Init
if (currentCat && currentCat !== 'all') filterCat(currentCat);

document.getElementById('searchInput').addEventListener('input', function() {
    document.getElementById('searchClear').classList.toggle('show', this.value.length > 0);
    applyFilters();
});
document.getElementById('priceMin').addEventListener('input', applyFilters);
document.getElementById('priceMax').addEventListener('input', applyFilters);

document.querySelectorAll('.cat-tab').forEach(tab => {
    tab.addEventListener('click', () => filterCat(tab.dataset.cat));
});

document.querySelectorAll('.price-preset').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.price-preset').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('priceMin').value = btn.dataset.min || '';
        document.getElementById('priceMax').value = btn.dataset.max || '';
        applyFilters();
    });
});

// ── Compare ────────────────────────────────────────────
function toggleCompare(cb) {
    const card = cb.closest('.product-card');
    const prod = {
        id:       card.dataset.id,
        name:     card.dataset.name,
        price:    parseInt(card.dataset.price),
        specs:    card.dataset.specs,
        category: card.dataset.category
    };

    if (cb.checked) {
        if (compareList.length >= MAX_COMPARE) {
            cb.checked = false;
            alert(`Chỉ có thể so sánh tối đa ${MAX_COMPARE} sản phẩm.`);
            return;
        }
        compareList.push(prod);
    } else {
        compareList = compareList.filter(p => p.id !== prod.id);
    }
    renderCompareBar();
}

function renderCompareBar() {
    const bar   = document.getElementById('compareBar');
    const slots = document.getElementById('compareSlots');
    slots.innerHTML = '';

    compareList.forEach(p => {
        const el = document.createElement('div');
        el.className = 'compare-slot';
        el.innerHTML = `<span title="${p.name}">${p.name}</span><span class="rm" onclick="removeCompare('${p.id}')" title="Bỏ">&times;</span>`;
        slots.appendChild(el);
    });

    bar.classList.toggle('show', compareList.length > 0);
}

function removeCompare(id) {
    compareList = compareList.filter(p => p.id !== id);
    // uncheck the checkbox
    document.querySelectorAll('.product-card').forEach(card => {
        if (card.dataset.id === id) {
            const cb = card.querySelector('.compare-cb');
            if (cb) cb.checked = false;
        }
    });
    renderCompareBar();
}

function clearCompare() {
    compareList = [];
    document.querySelectorAll('.compare-cb').forEach(cb => cb.checked = false);
    renderCompareBar();
}

function openCompareModal() {
    if (compareList.length < 2) { alert('Chọn ít nhất 2 sản phẩm để so sánh.'); return; }

    const thead = document.querySelector('#compareTable thead');
    const tbody = document.querySelector('#compareTable tbody');

    // Header row
    let headerRow = '<tr><th class="row-label" style="min-width:110px;"></th>';
    compareList.forEach(p => headerRow += `<th style="min-width:200px;">${p.name}</th>`);
    thead.innerHTML = headerRow + '</tr>';

    // Rows
    const rows = [
        { label: 'Danh mục', key: 'category' },
        { label: 'Giá',      key: 'price',    format: v => Number(v).toLocaleString('vi-VN') + ' ₫' },
        { label: 'Cấu hình', key: 'specs',    format: v => v ? v.split(',').map(s => `<div>• ${s.trim()}</div>`).join('') : '—' },
    ];

    tbody.innerHTML = rows.map(row => {
        let cells = `<td class="row-label">${row.label}</td>`;
        compareList.forEach(p => {
            const val = p[row.key] ?? '—';
            cells += `<td>${row.format ? row.format(val) : val}</td>`;
        });
        return `<tr>${cells}</tr>`;
    }).join('');

    new bootstrap.Modal(document.getElementById('compareModal')).show();
}

// ── Back to top ────────────────────────────────────────
const btt = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
    btt.classList.toggle('show', window.scrollY > 300);
});
btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>
</body>
</html>

USE AcerDB;
GO

-- Xóa dữ liệu cũ nếu muốn reset sạch (bỏ comment 2 dòng dưới nếu cần)
-- DELETE FROM products;
-- DBCC CHECKIDENT ('products', RESEED, 0);

-- ============================================
-- LAPTOP GAMING (category_id = 1)
-- ============================================
INSERT INTO products (name, category_id, specs, price, visible) VALUES
(N'Predator Helios 300',     1, N'Intel i7-12700H, RTX 3070 8GB, 16GB RAM, 512GB SSD, 15.6" 144Hz', 32990000, 1),
(N'Nitro 5 AN515',           1, N'Intel i5-12500H, RTX 3050 4GB, 8GB RAM, 512GB SSD, 15.6" 144Hz',  21490000, 1),
(N'Nitro 7 AN715',           1, N'Intel i7-12700H, RTX 3060 6GB, 16GB RAM, 512GB SSD, 15.6" 165Hz', 28490000, 1);

-- ============================================
-- LAPTOP VĂN PHÒNG (category_id = 2)
-- ============================================
INSERT INTO products (name, category_id, specs, price, visible) VALUES
(N'Swift 3 SF314',           2, N'Intel i5-1235U, Iris Xe Graphics, 16GB RAM, 512GB SSD, 14" FHD',  16990000, 1),
(N'Aspire 5 A515',           2, N'Intel i3-1215U, UHD Graphics, 8GB RAM, 256GB SSD, 15.6" FHD',     12490000, 1),
(N'TravelMate P2 TMP214',    2, N'Intel i5-1235U, Iris Xe Graphics, 8GB RAM, 512GB SSD, 14" FHD',   15490000, 1);

-- ============================================
-- MÀN HÌNH (category_id = 3)
-- ============================================
INSERT INTO products (name, category_id, specs, price, visible) VALUES
(N'Nitro XV272U',            3, N'27" QHD 2560x1440, IPS, 170Hz, 1ms, AMD FreeSync Premium',        8990000, 1),
(N'Predator XB273U',         3, N'27" QHD, IPS, 240Hz, 1ms, NVIDIA G-Sync Compatible',              14490000, 1),
(N'Acer CB242Y',             3, N'23.8" FHD 1920x1080, IPS, 75Hz, 4ms, màn hình văn phòng',          3990000, 1);

-- ============================================
-- MÁY TÍNH BÀN (category_id = 4)
-- ============================================
INSERT INTO products (name, category_id, specs, price, visible) VALUES
(N'Veriton X2690G',          4, N'Intel i5-12400, UHD 730, 8GB RAM, 256GB SSD, Windows 11 Pro',     14990000, 1),
(N'Aspire XC-1760',          4, N'Intel i3-12100, UHD 730, 8GB RAM, 512GB SSD, Windows 11 Home',    11490000, 1),
(N'Predator Orion 3000',     4, N'Intel i7-12700F, RTX 3060, 16GB RAM, 512GB SSD, Windows 11 Home', 29990000, 1);
GO

-- Kiểm tra kết quả
SELECT c.name AS [Loại máy], COUNT(p.id) AS [Số sản phẩm]
FROM categories c
LEFT JOIN products p ON p.category_id = c.id AND p.visible = 1
GROUP BY c.name
ORDER BY c.id;

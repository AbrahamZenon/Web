USE AcerDB;
GO

-- Thêm danh mục Phụ kiện nếu chưa có
IF NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'phu-kien')
    INSERT INTO categories (name, slug) VALUES (N'Phụ kiện', 'phu-kien');
GO

-- Lấy category_id của Phụ kiện
DECLARE @cat_id INT = (SELECT id FROM categories WHERE slug = 'phu-kien');

-- Thêm 2 sản phẩm phụ kiện mẫu
INSERT INTO products (name, category_id, specs, price, visible) VALUES
(N'Chuột gaming Acer Predator Cestus 315', @cat_id,
 N'Optical 16000 DPI, RGB, 7 nút, USB, tương thích Windows/Mac', 890000, 1),

(N'Bàn phím cơ Acer Predator Aethon 300', @cat_id,
 N'Switch Blue, RGB per-key, TKL, chống nước, USB braided', 1490000, 1);
GO

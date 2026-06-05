-- ============================================================
--  KHUNG DATABASE CHUNG CHO NHÓM
--  Mỗi thành viên = 1 nhãn hàng (brand) riêng
--  Dùng chung 1 SQL Server instance
-- ============================================================

IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'ShopGroupDB')
    CREATE DATABASE ShopGroupDB;
GO

USE ShopGroupDB;
GO

-- ============================================================
-- 1. BRANDS — Mỗi thành viên nhóm = 1 nhãn hàng
-- ============================================================
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='brands' AND xtype='U')
CREATE TABLE brands (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    name        NVARCHAR(100)  NOT NULL,           -- Tên nhãn hàng: Acer, Nike, Samsung...
    slug        NVARCHAR(100)  NOT NULL UNIQUE,    -- URL: acer, nike, samsung
    description NVARCHAR(500),                     -- Mô tả ngắn
    logo_url    NVARCHAR(500),                     -- URL logo
    owner_name  NVARCHAR(100),                     -- Tên thành viên phụ trách
    created_at  DATETIME       NOT NULL DEFAULT GETDATE()
);
GO

-- ============================================================
-- 2. CATEGORIES — Danh mục sản phẩm (gắn với từng brand)
-- ============================================================
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='categories' AND xtype='U')
CREATE TABLE categories (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    brand_id    INT            NOT NULL REFERENCES brands(id) ON DELETE CASCADE,
    name        NVARCHAR(100)  NOT NULL,
    slug        NVARCHAR(100)  NOT NULL,
    UNIQUE (brand_id, slug)   -- slug chỉ unique trong cùng brand
);
GO

-- ============================================================
-- 3. PRODUCTS — Sản phẩm (gắn với brand + category)
-- ============================================================
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='products' AND xtype='U')
CREATE TABLE products (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    brand_id    INT            NOT NULL REFERENCES brands(id) ON DELETE CASCADE,
    category_id INT            NOT NULL REFERENCES categories(id),
    name        NVARCHAR(200)  NOT NULL,
    specs       NVARCHAR(500),                     -- Thông số kỹ thuật
    description NVARCHAR(MAX),                     -- Mô tả chi tiết
    price       BIGINT         NOT NULL DEFAULT 0,
    image_url   NVARCHAR(500),
    visible     BIT            NOT NULL DEFAULT 1,
    created_at  DATETIME       NOT NULL DEFAULT GETDATE()
);
GO

-- ============================================================
-- 4. TAGS — Từ khóa tìm kiếm
-- ============================================================
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='tags' AND xtype='U')
CREATE TABLE tags (
    id      INT IDENTITY(1,1) PRIMARY KEY,
    name    NVARCHAR(50)  NOT NULL UNIQUE   -- gaming, văn phòng, cao cấp...
);
GO

-- ============================================================
-- 5. PRODUCT_TAGS — Bảng liên kết nhiều-nhiều (SP ↔ Tag)
-- ============================================================
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='product_tags' AND xtype='U')
CREATE TABLE product_tags (
    product_id  INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    tag_id      INT NOT NULL REFERENCES tags(id)     ON DELETE CASCADE,
    PRIMARY KEY (product_id, tag_id)
);
GO

-- ============================================================
-- 6. ADMINS — Tài khoản quản trị (mỗi thành viên 1 account)
-- ============================================================
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='admins' AND xtype='U')
CREATE TABLE admins (
    id            INT IDENTITY(1,1) PRIMARY KEY,
    brand_id      INT           REFERENCES brands(id) ON DELETE SET NULL,
    username      NVARCHAR(100) NOT NULL UNIQUE,
    password_hash NVARCHAR(255) NOT NULL,
    is_superadmin BIT           NOT NULL DEFAULT 0   -- 1 = xem được tất cả brand
);
GO

-- ============================================================
-- DỮ LIỆU MẪU
-- ============================================================

-- Brands mẫu (mỗi thành viên đổi lại tên mình)
INSERT INTO brands (name, slug, description, owner_name) VALUES
(N'Acer',         'acer',         N'Tập đoàn công nghệ Đài Loan',        N'Thành viên 1'),
(N'Nhãn hàng 2',  'brand-2',      N'Mô tả nhãn hàng thành viên 2',       N'Thành viên 2'),
(N'Nhãn hàng 3',  'brand-3',      N'Mô tả nhãn hàng thành viên 3',       N'Thành viên 3'),
(N'Nhãn hàng 4',  'brand-4',      N'Mô tả nhãn hàng thành viên 4',       N'Thành viên 4'),
(N'Nhãn hàng 5',  'brand-5',      N'Mô tả nhãn hàng thành viên 5',       N'Thành viên 5');
GO

-- Categories mẫu cho Acer (brand_id = 1)
INSERT INTO categories (brand_id, name, slug) VALUES
(1, N'Laptop gaming',    'laptop-gaming'),
(1, N'Laptop văn phòng', 'laptop-vanphong'),
(1, N'Màn hình',         'man-hinh'),
(1, N'Máy tính bàn',     'may-tinh-ban'),
(1, N'Phụ kiện',         'phu-kien');
GO

-- Sản phẩm mẫu Acer
DECLARE @cat1 INT = (SELECT id FROM categories WHERE brand_id=1 AND slug='laptop-gaming');
DECLARE @cat2 INT = (SELECT id FROM categories WHERE brand_id=1 AND slug='laptop-vanphong');
DECLARE @cat5 INT = (SELECT id FROM categories WHERE brand_id=1 AND slug='phu-kien');

INSERT INTO products (brand_id, category_id, name, specs, price) VALUES
(1, @cat1, N'Predator Helios 300', N'Intel i7-12700H, RTX 3070, 16GB RAM, 512GB SSD', 32990000),
(1, @cat1, N'Nitro 5',             N'Intel i5-12500H, RTX 3050, 8GB RAM, 512GB SSD',  21490000),
(1, @cat2, N'Swift 3',             N'Intel i5-1235U, Iris Xe, 16GB RAM, 512GB SSD',   16990000),
(1, @cat2, N'Aspire 5',            N'Intel i3-1215U, UHD, 8GB RAM, 256GB SSD',        12490000),
(1, @cat5, N'Chuột Predator Cestus 315', N'Optical 16000 DPI, RGB, 7 nút, USB',       890000),
(1, @cat5, N'Bàn phím Predator Aethon 300', N'Switch Blue, RGB, TKL, chống nước',     1490000);
GO

-- Tags chung cho tất cả brand
INSERT INTO tags (name) VALUES
(N'gaming'), (N'văn phòng'), (N'cao cấp'), (N'phổ thông'),
(N'mỏng nhẹ'), (N'pin trâu'), (N'màn hình lớn'), (N'RGB'),
(N'học sinh'), (N'doanh nghiệp'), (N'mới nhất'), (N'bán chạy');
GO

-- Gán tags cho sản phẩm mẫu
-- Predator Helios 300 (id=1): gaming, cao cấp, RGB
INSERT INTO product_tags (product_id, tag_id)
SELECT p.id, t.id FROM products p, tags t
WHERE p.name = N'Predator Helios 300'
  AND t.name IN (N'gaming', N'cao cấp', N'RGB');

-- Nitro 5: gaming, phổ thông, học sinh
INSERT INTO product_tags (product_id, tag_id)
SELECT p.id, t.id FROM products p, tags t
WHERE p.name = N'Nitro 5'
  AND t.name IN (N'gaming', N'phổ thông', N'học sinh');

-- Swift 3: văn phòng, mỏng nhẹ, pin trâu
INSERT INTO product_tags (product_id, tag_id)
SELECT p.id, t.id FROM products p, tags t
WHERE p.name = N'Swift 3'
  AND t.name IN (N'văn phòng', N'mỏng nhẹ', N'pin trâu');
GO

PRINT N'Setup hoàn tất! Database ShopGroupDB đã sẵn sàng.';
GO

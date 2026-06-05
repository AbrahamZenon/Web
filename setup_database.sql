-- Chạy script này trong SQL Server Management Studio (SSMS)
-- hoặc sqlcmd để tạo database và bảng

-- Tạo database
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'AcerDB')
    CREATE DATABASE AcerDB;
GO

USE AcerDB;
GO

-- Tạo bảng danh mục loại máy
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='categories' AND xtype='U')
CREATE TABLE categories (
    id       INT IDENTITY(1,1) PRIMARY KEY,
    name     NVARCHAR(100) NOT NULL,
    slug     NVARCHAR(100) NOT NULL UNIQUE
);
GO

-- Tạo bảng sản phẩm
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='products' AND xtype='U')
CREATE TABLE products (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    name        NVARCHAR(200)  NOT NULL,
    category_id INT            NOT NULL REFERENCES categories(id),
    specs       NVARCHAR(300),
    price       BIGINT         NOT NULL DEFAULT 0,
    image_url   NVARCHAR(500),
    visible     BIT            NOT NULL DEFAULT 1,
    created_at  DATETIME       NOT NULL DEFAULT GETDATE()
);
GO

-- Tạo bảng tài khoản admin
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='admins' AND xtype='U')
CREATE TABLE admins (
    id           INT IDENTITY(1,1) PRIMARY KEY,
    username     NVARCHAR(100) NOT NULL UNIQUE,
    password_hash NVARCHAR(255) NOT NULL
);
GO

-- Dữ liệu mẫu: danh mục
INSERT INTO categories (name, slug) VALUES
(N'Laptop gaming',    'laptop-gaming'),
(N'Laptop văn phòng', 'laptop-vanphong'),
(N'Màn hình',         'man-hinh'),
(N'Máy tính bàn',     'may-tinh-ban');
GO

-- Dữ liệu mẫu: sản phẩm
INSERT INTO products (name, category_id, specs, price) VALUES
(N'Predator Helios 300', 1, N'Intel i7-12700H, RTX 3070, 16GB RAM, 512GB SSD', 32990000),
(N'Nitro 5',             1, N'Intel i5-12500H, RTX 3050, 8GB RAM, 512GB SSD',  21490000),
(N'Nitro 7',             1, N'Intel i7-12700H, RTX 3060, 16GB RAM, 512GB SSD', 28490000),
(N'Swift 3',             2, N'Intel i5-1235U, Iris Xe, 16GB RAM, 512GB SSD',   16990000),
(N'Aspire 5',            2, N'Intel i3-1215U, UHD Graphics, 8GB RAM, 256GB SSD',12490000);
GO

-- Tài khoản admin mặc định: admin / Admin@123
-- password_hash tạo bằng PHP: password_hash('Admin@123', PASSWORD_BCRYPT)
-- Chạy dòng dưới SAU KHI đã cài PHP và biết hash, hoặc dùng script setup_admin.php
-- INSERT INTO admins (username, password_hash) VALUES ('admin', '<hash>');

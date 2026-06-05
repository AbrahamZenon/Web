-- ============================================================
-- Migration: Thêm cột role vào bảng admins để phân quyền
-- ============================================================
-- Role:
--   'admin' = Quản trị viên (toàn quyền + quản lý nhân viên)
--   'staff' = Nhân viên (chỉ thêm/sửa sản phẩm)
-- ============================================================

USE AcerDB;
GO

-- Thêm cột role nếu chưa có
IF NOT EXISTS (
    SELECT 1 FROM sys.columns
    WHERE object_id = OBJECT_ID('admins') AND name = 'role'
)
BEGIN
    ALTER TABLE admins ADD role NVARCHAR(20) NOT NULL DEFAULT 'admin';
END
GO

-- Thêm cột full_name nếu chưa có (để hiển thị tên thân thiện)
IF NOT EXISTS (
    SELECT 1 FROM sys.columns
    WHERE object_id = OBJECT_ID('admins') AND name = 'full_name'
)
BEGIN
    ALTER TABLE admins ADD full_name NVARCHAR(150) NULL;
END
GO

-- Thêm cột created_at
IF NOT EXISTS (
    SELECT 1 FROM sys.columns
    WHERE object_id = OBJECT_ID('admins') AND name = 'created_at'
)
BEGIN
    ALTER TABLE admins ADD created_at DATETIME NOT NULL DEFAULT GETDATE();
END
GO

-- Đảm bảo tài khoản 'admin' có role = 'admin'
UPDATE admins SET role = 'admin' WHERE username = 'admin';
GO

PRINT 'Migration hoàn tất. Cột role/full_name/created_at đã được thêm vào bảng admins.';

-- Chạy script này trong SSMS để thêm cột mô tả chi tiết
USE AcerDB;
GO

IF NOT EXISTS (
    SELECT 1 FROM sys.columns
    WHERE object_id = OBJECT_ID('products') AND name = 'description'
)
BEGIN
    ALTER TABLE products ADD description NVARCHAR(MAX) NULL;
    PRINT N'Đã thêm cột description vào bảng products.';
END
ELSE
    PRINT N'Cột description đã tồn tại.';
GO

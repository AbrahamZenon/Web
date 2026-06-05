# 📋 Hướng dẫn cài đặt — Acer Vietnam Website

---

## ✅ Yêu cầu hệ thống

| Phần mềm | Phiên bản | Ghi chú |
|---|---|---|
| XAMPP | 8.x trở lên | Cần Apache + PHP |
| PHP | 8.0+ | Đã có trong XAMPP |
| SQL Server | 2016+ | Đã cài sẵn |
| ODBC Driver | 17 hoặc 18 | Tải từ Microsoft |
| SSMS | Bất kỳ | Để quản lý database |

---

## 🚀 Các bước cài đặt

### Bước 1 — Cài XAMPP

- Tải XAMPP tại https://www.apachefriends.org
- Khi cài, **không cài vào** `C:\Program Files\` vì bị lỗi quyền UAC
- Cài vào `C:\xampp\` (thư mục gốc ổ C)
- Nếu xuất hiện cảnh báo UAC → nhấn **OK**, bỏ qua

---

### Bước 2 — Cài ODBC Driver cho SQL Server

- Tải **Microsoft ODBC Driver 17 for SQL Server** tại:
  https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server
- Cài driver phù hợp hệ điều hành (x64 cho Windows 64-bit)
- Khởi động lại máy sau khi cài

---

### Bước 3 — Cài PHP Driver cho SQL Server

1. Kiểm tra phiên bản PHP: mở XAMPP Shell → gõ `php -v`
2. Tải driver tại:
   https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server
3. Giải nén, copy 2 file vào `C:\xampp\php\ext\`:
   ```
   php_sqlsrv_82_ts_x64.dll      ← thay 82 bằng version PHP của bạn
   php_pdo_sqlsrv_82_ts_x64.dll
   ```
4. Mở XAMPP → Config Apache → **php.ini**, thêm 2 dòng:
   ```ini
   extension=php_sqlsrv_82_ts_x64
   extension=php_pdo_sqlsrv_82_ts_x64
   ```
5. **Restart Apache** trong XAMPP

---

### Bước 4 — Cấu hình SQL Server

#### 4a. Bật SQL Server Authentication
1. Mở SSMS → chuột phải tên server → **Properties** → **Security**
2. Chọn **SQL Server and Windows Authentication mode**
3. Nhấn **OK**

#### 4b. Bật tài khoản sa
1. Vào **Security** → **Logins** → chuột phải **sa** → **Properties**
2. Tab **General**:
   - Đặt password (ví dụ: `Acer@1234`)
   - Bỏ tick **Enforce password policy**
3. Tab **Status**:
   - Login → **Enabled**
4. Nhấn **OK**

#### 4c. Bật TCP/IP
1. Nhấn `Windows + R` → gõ `SQLServerManager13.msc` *(thay 13 bằng version SQL Server của bạn: 2019→15, 2017→14, 2016→13)*
2. Vào **SQL Server Network Configuration** → **Protocols for [tên instance]**
3. Chuột phải **TCP/IP** → **Enable**
4. Vào **SQL Server Services** → chuột phải **SQL Server** → **Restart**

#### 4d. Bật SQL Server Browser
1. Nhấn `Windows + R` → gõ `services.msc`
2. Tìm **SQL Server Browser** → chuột phải → **Start**
3. Đổi **Startup type** thành **Automatic**

---

### Bước 5 — Tạo Database

1. Mở SSMS, kết nối bằng **Windows Authentication**
2. Mở file `setup_group_database.sql` → nhấn **Execute (F5)**
3. Kiểm tra: trong Object Explorer xuất hiện database **ShopGroupDB**

> ⚠️ Nếu đã chạy nhầm nhiều lần, chạy lệnh sau để xóa dữ liệu trùng:
> ```sql
> USE ShopGroupDB;
> DELETE FROM products WHERE id NOT IN (SELECT MIN(id) FROM products GROUP BY name, category_id);
> DELETE FROM categories WHERE id NOT IN (SELECT MIN(id) FROM categories GROUP BY slug, brand_id);
> ```

---

### Bước 6 — Copy file web

1. Giải nén `acer_website.zip`
2. Copy thư mục `acer_website` vào:
   ```
   C:\xampp\htdocs\acer_website\
   ```

---

### Bước 7 — Cấu hình kết nối database

Mở file `includes/db.php`, sửa các thông tin sau:

```php
define('DB_SERVER', 'TEN_LAPTOP\\TEN_INSTANCE');  // xem trong SSMS
define('DB_NAME',   'ShopGroupDB');
define('DB_USER',   'sa');
define('DB_PASS',   'Acer@1234');               // password vừa đặt

define('BRAND_ID',  1);  // số thứ tự nhãn hàng của bạn trong nhóm
```

> ⚠️ Dùng **2 dấu `\\`** khi viết tên server trong PHP, ví dụ:
> ```php
> define('DB_SERVER', 'LAPTOP-ABC\\SQLEXPRESS');
> // KHÔNG viết: 'LAPTOP-ABC\SQLEXPRESS'
> ```

**Cách xem tên server:** mở SSMS → nhìn dòng đầu tiên trong Object Explorer

---

### Bước 8 — Tạo tài khoản admin

1. Truy cập: `http://localhost/acer_website/setup_admin.php`
2. Trang sẽ tạo tài khoản mặc định:
   - Username: `admin`
   - Password: `Admin@123`
3. **Xóa file `setup_admin.php` ngay sau khi chạy!**

---

### Bước 9 — Kiểm tra

| URL | Kết quả mong đợi |
|---|---|
| `http://localhost/acer_website/` | Trang chủ hiện bình thường |
| `http://localhost/acer_website/products.php` | Trang sản phẩm |
| `http://localhost/acer_website/admin/login.php` | Trang đăng nhập admin |

---

## 👥 Hướng dẫn cho nhóm

Mỗi thành viên trong nhóm làm theo:

1. Copy toàn bộ thư mục `acer_website` → đổi tên thành tên nhãn hàng của mình (ví dụ: `nike_website`)
2. Mở `includes/db.php` → đổi `BRAND_ID` thành số của mình:
   ```
   Thành viên 1 → BRAND_ID = 1 (Acer)
   Thành viên 2 → BRAND_ID = 2
   Thành viên 3 → BRAND_ID = 3
   ...
   ```
3. Vào Admin → thêm **Categories** và **Products** cho nhãn hàng của mình
4. Chỉnh sửa nội dung: tên công ty, màu sắc, logo trong các file PHP

> ✅ Tất cả dùng chung 1 database `ShopGroupDB`, các bạn không ảnh hưởng dữ liệu của nhau vì mọi query đều lọc theo `BRAND_ID`

---

## 🔧 Lỗi thường gặp & cách sửa

### ❌ "Server is not found or not accessible"
- SQL Server chưa chạy → vào `services.msc` → Start **SQL Server**
- TCP/IP chưa bật → làm lại Bước 4c
- Sai tên server trong `db.php` → kiểm tra lại tên trong SSMS
- Thiếu dấu `\\` trong tên server PHP

### ❌ "Login failed for user 'sa'"
- Tài khoản `sa` chưa được bật → làm lại Bước 4b
- SQL Server Authentication chưa bật → làm lại Bước 4a
- Sai mật khẩu → vào SSMS đặt lại password cho `sa`

### ❌ "Column ... invalid in ORDER BY"
- Lỗi SQL Server strict mode
- Sửa `ORDER BY c.id` thành `ORDER BY p.category_id` trong query

### ❌ Apache không Start được (cổng 80 bị chiếm)
- Skype hoặc IIS đang dùng cổng 80
- Trong XAMPP → Config → đổi cổng Apache sang `8080`
- Truy cập bằng `http://localhost:8080/acer_website/`

### ❌ Ảnh upload không hiện
- Kiểm tra thư mục `assets/img/products/` đã tồn tại chưa
- Tạo thư mục nếu chưa có

### ❌ Font tiếng Việt bị vỡ/lỗi dấu
- Đảm bảo có kết nối internet để load font **Be Vietnam Pro** từ Google Fonts
- Hoặc tải font về máy và đặt vào `assets/fonts/`

---

## 📁 Cấu trúc thư mục

```
acer_website/
├── index.php                  ← Trang chủ
├── products.php               ← Trang danh sách sản phẩm
├── product.php                ← Trang chi tiết sản phẩm
├── setup_admin.php            ← Tạo admin (xóa sau khi dùng!)
├── setup_database.sql         ← Database cũ (chỉ 1 brand)
├── setup_group_database.sql   ← Database nhóm (nhiều brand) ✅
├── add_accessories.sql        ← Thêm danh mục phụ kiện
├── add_description_column.sql ← Thêm cột mô tả (migration)
├── includes/
│   ├── db.php                 ← Cấu hình kết nối + BRAND_ID
│   └── illustrations.php     ← SVG minh họa sản phẩm
├── assets/
│   ├── css/
│   │   └── style.css          ← CSS toàn bộ website
│   └── img/products/          ← Ảnh upload từ admin
└── admin/
    ├── login.php              ← Trang đăng nhập
    ├── dashboard.php          ← Quản lý sản phẩm
    ├── logout.php             ← Đăng xuất
    └── upload_image.php       ← Xử lý upload ảnh
```

---

## 🔐 Bảo mật (lưu ý quan trọng)

- **Xóa `setup_admin.php`** ngay sau khi tạo tài khoản admin
- Đổi password `Admin@123` thành password mạnh hơn
- Không để `DB_PASS` trống trong môi trường production
- Thư mục `admin/` chỉ truy cập khi đã đăng nhập (đã có `session_start` bảo vệ)

---

*Cập nhật lần cuối: 2024 — Acer Vietnam Group Project*

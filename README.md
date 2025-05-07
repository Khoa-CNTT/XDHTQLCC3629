# # Hệ Thống Quản Lý Chuỗi Cung Ứng Và Vận Tải Hàng Hóa Bằng Blockchain

## 📌 Giới Thiệu

Dự án này nhằm xây dựng một hệ thống quản lý chuỗi cung ứng và vận tải hàng hóa ứng dụng công nghệ blockchain. Hệ thống giúp minh bạch hóa thông tin sản phẩm từ khâu sản xuất đến tay người tiêu dùng, đảm bảo truy xuất nguồn gốc, giảm gian lận và tối ưu hóa quy trình vận chuyển.

## 🎯 Mục Tiêu Dự Án

-   **🔍 Minh bạch hóa chuỗi cung ứng**: Mọi giao dịch và thông tin về sản phẩm được ghi nhận trên blockchain, giúp kiểm tra tính xác thực và xuất xứ hàng hóa.
-   **🚀 Tối ưu hóa quy trình vận tải**: Theo dõi đơn hàng và trạng thái vận chuyển theo thời gian thực.
-   **🔒 Tăng cường bảo mật dữ liệu**: Blockchain đảm bảo dữ liệu không thể bị thay đổi hoặc giả mạo.
-   **🤖 Tích hợp hợp đồng thông minh (Smart Contract)**: Tự động hóa các quy trình như tạo đơn hàng, xác nhận thanh toán và vận chuyển.

## 🛠 Công Nghệ Sử Dụng

-   **💻 Frontend**: Vue.js
-   **🖥 Backend**: Laravel
-   **⛓ Blockchain**: Smart Contract trên TronBox
-   **🗄 Cơ sở dữ liệu**: MySQL
-   **📋 Quản lý dự án**: Jira, Trello

## 🔧 Chức Năng Chính

### 1️⃣ Quản Lý Người Dùng

-   **👨‍💼 Admin**: Quản lý hệ thống, kiểm duyệt dữ liệu.
-   **🏭 Nhà sản xuất**: Quản lý nguyên liệu, sản xuất sản phẩm.
-   **🛒 Đại lý**: Đặt hàng và theo dõi đơn hàng.
-   **🚛 Đơn vị vận chuyển**: Theo dõi và vận chuyển đơn hàng.

### 2️⃣ Quy Trình Đặt Hàng

1. 🛍 Lấy giỏ hàng của đại lý → tạo đơn hàng.
2. ⏳ Đơn hàng ở trạng thái "Chờ xác nhận" để admin kiểm duyệt.
3. ✅ Admin xác nhận đơn hàng → chuyển sang trạng thái "Đã xác nhận".
4. ⏳ Chuyển đơn hàng cho đúng nhà sản xuất.
5. 📦 Duyệt từng sản phẩm trong giỏ hàng → lưu lịch sử đơn hàng.
6. 🗑 Xóa giỏ hàng sau khi đặt hàng thành công.
7. ⛓ Ghi dữ liệu đơn hàng lên blockchain.

### 3️⃣ Quy Trình Vận Chuyển

1. 📦 Đơn vị vận chuyển tiếp nhận đơn hàng từ nhà sản xuất.
2. 🚛 Vận chuyển đơn hàng đến đại lý theo lộ trình tối ưu.
3. 🏬 Đại lý nhận hàng và xác nhận trạng thái đơn hàng.
4. ⛓ Ghi nhận toàn bộ quá trình vận chuyển trên blockchain để đảm bảo minh bạch.

### 4️⃣ Truy Xuất Nguồn Gốc Sản Phẩm

-   🔗 Xem lịch sử sản phẩm từ khâu nguyên liệu đến khi giao đến khách hàng.
-   🛡 Lưu trữ thông tin trên blockchain để đảm bảo tính minh bạch.

## ⚙️ Cài Đặt & Chạy Dự Án

### 1️⃣ Yêu Cầu Hệ Thống

-   🟢 Node.js >= 14.x
-   🟠 PHP >= 8.0
-   🔵 Composer
-   🗄 MySQL
-   ⛓ Ganache

### 2️⃣ Cài Đặt

## 📁 Repository

-   🔗 Frontend: [GitHub Repo Frontend](https://github.com/MaiXuanTung/XDHTQLCC3629-FE)
-   🔗 Backend: [GitHub Repo Backend](https://github.com/Khoa-CNTT/XDHTQLCC3629)
-   🔗 BlockChain: [GitHub Repo BlockChain](https://github.com/MaiXuanTung/XDHTQLCC3629-BlockChain)

```sh
# Clone repository
git clone git@github.com:Khoa-CNTT/XDHTQLCC3629.git
cd XDHTQLCC3629
git clone git@github.com:MaiXuanTung/XDHTQLCC3629-FE.git
cd XDHTQLCC3629-FE
git clone git@github.com:MaiXuanTung/XDHTQLCC3629-BlockChain.git
cd XDHTQLCC3629-BlockChain
# Cài đặt backend
composer install
cp .env.example .env
php artisan key:generate

# Cài đặt frontend
cd frontend
npm install
npm run dev

# Cài đặt blockchain
cd blockchain
npm run download
npm run compile
```

### 3️⃣ Khởi Chạy Dự Án

```sh
# Chạy backend
php artisan serve

# Chạy frontend
cd frontend
npm run dev

# Chạy blockchain
npm run server
```

## 📞 Liên Hệ

-   📧 Email: maitung801@gmail.com

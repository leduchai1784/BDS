# BDS Rental - Hệ thống Tìm kiếm & Cho thuê Bất động sản (Next.js & AI)

Chào mừng bạn đến với **BDS Rental** - Hệ thống tìm kiếm, đăng tin và quản lý bất động sản toàn diện tích hợp Trí tuệ nhân tạo (AI Assistant, AI Marketing Studio, AI OCR). Dự án được xây dựng bằng **Next.js 15 (App Router)** và **Prisma Client**.

---

## 🚀 Các tính năng chính

* **Tìm kiếm bất động sản**: Tìm kiếm theo bộ lọc nâng cao và hiển thị trực quan trên bản đồ tương tác **MapLibre GL JS** tích hợp GPS.
* **Xác thực CCCD bằng AI OCR**: Tải ảnh CCCD, hệ thống tự động bóc tách thông tin qua FPT AI OCR API.
* **Sales Chatbot AI**: Hỗ trợ khách hàng tư vấn dự án, so sánh bất động sản và gợi ý đặt lịch xem nhà trực tiếp.
* **AI Marketing & Content Studio**: Sử dụng **Gemini Pro** để sinh 20 bài đăng Facebook, kịch bản video TikTok (kèm prompt tiếng Anh làm video), bài SEO HTML, Email, SMS chỉ với 1 click. Cho phép chia sẻ trực tiếp sang Facebook, Zalo, Email.
* **Đặt lịch xem nhà**: Hệ thống quản lý lịch hẹn thông minh giữa chủ nhà và khách hàng, đồng bộ trực tiếp lên CRM Wordpress.
* **Bảo mật**: Xác thực an toàn với **NextAuth.js v5**.

---

## 🛠️ Yêu cầu hệ thống

* **Node.js** >= 18.x
* **NPM** >= 9.x
* Cơ sở dữ liệu **PostgreSQL** (hoặc MySQL, SQLite được cấu hình qua Prisma)

---

## ⚙️ Cài đặt và Khởi chạy

### 1. Clone dự án và cài đặt dependencies
```bash
git clone https://github.com/leduchai1784/BDS.git
cd BDS
npm install
```

### 2. Cấu hình biến môi trường (`.env`)
Tạo file `.env` tại thư mục gốc của dự án và điền đầy đủ các thông số cấu hình sau:

```env
# Database Connection (Prisma)
DATABASE_URL="postgresql://username:password@localhost:5432/bds_rental?schema=public"

# NextAuth Config (Xác thực)
NEXTAUTH_URL="http://localhost:3000"
NEXTAUTH_SECRET="your_nextauth_secret_key" # Tạo ngẫu nhiên bằng lệnh: openssl rand -base64 32

# Gemini AI API Config (AI Marketing Studio)
GEMINI_API_KEY="your_gemini_api_key"
GEMINI_MODEL="gemini-1.5-flash" # Hoặc gemini-1.5-pro, gemini-3.1-flash-lite

# FPT AI OCR API (Quét căn cước công dân)
FPT_OCR_API_KEY="your_fpt_ai_ocr_api_key"

# NKS API (Đồng bộ thông tin và bản đồ hành chính)
NKS_API_URL="https://api.nks.vn"
NKS_API_TOKEN="your_nks_api_token"

# Wordpress CRM Integration (Đồng bộ Lead & Appointments)
WORDPRESS_CRM_API_URL="https://your-crm-domain.com/wp-json/wp/v2"
WORDPRESS_CRM_USERNAME="your_username"
WORDPRESS_CRM_PASSWORD="your_application_password"
```

### 3. Đồng bộ Cơ sở dữ liệu với Prisma
Chạy các lệnh sau để khởi tạo Prisma Client và đồng bộ schema vào database của bạn:
```bash
# Generate Prisma Client
npm run db:generate

# Push schema lên Database
npm run db:push
```

*(Tùy chọn) Chạy Seeder để nạp dữ liệu mẫu:*
```bash
node scratch/seed.js
```

### 4. Khởi chạy dự án ở chế độ Phát triển (Development)
```bash
npm run dev
```
Ứng dụng sẽ chạy tại địa chỉ: [http://localhost:3000](http://localhost:3000)

### 5. Build dự án cho Production
```bash
npm run build
npm run start
```

---

## 📁 Cấu trúc Thư mục chính

```bash
├── src/
│   ├── app/                 # Routes, API endpoints & Pages (App Router)
│   │   ├── api/             # REST APIs (Auth, Profile, Marketing, Chatbot...)
│   │   ├── listings/        # Trang danh sách bất động sản
│   │   ├── map/             # Trang bản đồ tương tác MapLibre
│   │   ├── profile/         # Dashboard cá nhân, AI Marketing Studio
│   │   └── page.tsx         # Trang chủ chính
│   ├── components/          # Reusable UI components (Navbar, Footer, Profile...)
│   ├── lib/                 # Lib configs (Prisma Client, NextAuth, NKS API...)
│   ├── store/               # Zustand global state management
│   └── types/               # TypeScript type definitions
├── prisma/
│   └── schema.prisma        # Prisma database schema definition
├── public/                  # Assets tĩnh (images, icons...)
├── package.json             # Scripts và các gói dependencies
└── tsconfig.json            # Cấu hình TypeScript compiler
```

---

## 🔄 Quy trình Deploy (GitHub & Vercel Sync)

Dự án được tích hợp cơ chế tự động triển khai (CI/CD) qua Vercel. Khi code được đẩy lên nhánh `main` của GitHub, Vercel sẽ tự động build và deploy lại ứng dụng:

```bash
# 1. Add các thay đổi
git add .

# 2. Commit code
git commit -m "feat: mô tả tính năng mới"

# 3. Push lên GitHub (Vercel tự động deploy lại)
git push origin main
```

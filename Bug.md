# Bug Tracker - BDS Rental

> Tài liệu ghi nhận các lỗi đã gặp phải, nguyên nhân và cách xử lý trong quá trình phát triển hệ thống.

---

## Quy ước Mức độ

| Nhãn | Mô tả |
|---|---|
| 🔴 **CRITICAL** | Lỗi nghiêm trọng, chặn toàn bộ hệ thống |
| 🟠 **HIGH** | Lỗi lớn, ảnh hưởng chức năng chính |
| 🟡 **MEDIUM** | Lỗi vừa, ảnh hưởng trải nghiệm người dùng |
| 🟢 **LOW** | Lỗi nhỏ, giao diện hoặc cosmetic |
| ✅ **FIXED** | Đã sửa |
| 🔲 **OPEN** | Chưa xử lý |
| 🔍 **INVESTIGATING** | Đang điều tra |

---

## Bug Log

---

### BUG-001 — Vercel không nhận commit mới nhất
- **Mức độ**: 🟠 HIGH | **Trạng thái**: ✅ FIXED
- **Môi trường**: Production (Vercel)
- **Ngày ghi nhận**: 2026-07-22
- **Mô tả**: Sau khi push commit lên GitHub, Vercel không tự động triển khai bản mới. Trang web vẫn hiển thị phiên bản cũ.
- **Nguyên nhân**: Vercel cần thời gian build (~1-3 phút). Có thể xảy ra do cache build hoặc GitHub webhook bị delay.
- **Cách xử lý**: Chờ Vercel build xong hoặc manually trigger redeploy từ Vercel Dashboard → Deployments.
- **Liên quan đến**: Deployment pipeline, GitHub Actions

---

### BUG-002 — Sidebar mở/đóng bị nháy, chữ bị co giãn giật
- **Mức độ**: 🟡 MEDIUM | **Trạng thái**: ✅ FIXED
- **Môi trường**: Admin & Owner Dashboard
- **Ngày ghi nhận**: 2026-07-22
- **File liên quan**:
  - [`AppSidebar.tsx`](file:///c:/ThucTap/BDS/src/components/admin/dashboard/layout/AppSidebar.tsx)
  - [`OwnerSidebar.tsx`](file:///c:/ThucTap/BDS/src/components/owner/dashboard/layout/OwnerSidebar.tsx)
- **Mô tả**: Khi mở sidebar, chữ menu bị nháy (flash), co rút và giãn ra bất thường trong quá trình animation.
- **Nguyên nhân gốc rễ**: Code cũ dùng conditional render `{isExpanded && <span>text</span>}` khiến chữ bị tạo ra / xóa đi khi sidebar đang chuyển động, dẫn tới hiệu ứng bóp méo/nháy.
- **Cách xử lý**:
  - Thay conditional render bằng **CSS Transition** thuần túy.
  - Dùng `whitespace-nowrap` + chuyển tiếp `opacity-100 max-w-[200px]` → `opacity-0 max-w-0 overflow-hidden`.
  - Chữ luôn tồn tại trong DOM nhưng được fade out mượt mà.
- **Commit liên quan**: Sidebar animation fix

---

### BUG-003 — Hiệu ứng đóng mở Sidebar quá chậm, thiếu dứt khoát
- **Mức độ**: 🟢 LOW | **Trạng thái**: ✅ FIXED
- **Môi trường**: Admin & Owner Dashboard
- **Ngày ghi nhận**: 2026-07-22
- **File liên quan**:
  - [`AppSidebar.tsx`](file:///c:/ThucTap/BDS/src/components/admin/dashboard/layout/AppSidebar.tsx)
  - [`OwnerSidebar.tsx`](file:///c:/ThucTap/BDS/src/components/owner/dashboard/layout/OwnerSidebar.tsx)
  - [`OwnerLayoutContentWrapper.tsx`](file:///c:/ThucTap/BDS/src/components/owner/dashboard/layout/OwnerLayoutContentWrapper.tsx)
  - [`AdminLayoutContentWrapper.tsx`](file:///c:/ThucTap/BDS/src/app/admin/AdminLayoutContentWrapper.tsx)
- **Mô tả**: Animation mở/đóng sidebar cảm giác "lề mề", không dứt khoát.
- **Nguyên nhân**: Transition `300ms ease-in-out` (chậm 2 đầu, giống kiểu cũ).
- **Cách xử lý**: Đổi sang `150ms ease-out` cho tất cả sidebar và content wrapper đồng bộ.

---

### BUG-004 — Header không hiển thị Avatar người dùng sau khi đăng nhập
- **Mức độ**: 🟡 MEDIUM | **Trạng thái**: ✅ FIXED
- **Môi trường**: Production & Local
- **Ngày ghi nhận**: 2026-07-22
- **File liên quan**:
  - [`auth.ts`](file:///c:/ThucTap/BDS/src/lib/auth.ts)
  - [`UserDropdown.tsx`](file:///c:/ThucTap/BDS/src/components/admin/dashboard/header/UserDropdown.tsx)
- **Mô tả**: Ảnh đại diện người dùng không hiển thị trong dropdown Header sau khi đăng nhập thành công. Chỉ hiển thị placeholder màu xám.
- **Nguyên nhân**:
  - Callback `jwt` trong `auth.ts` không lưu trường `avatar` vào JWT Token trong các request sau lần đăng nhập đầu.
  - `UserDropdown.tsx` không xử lý đường dẫn ảnh tương đối (ví dụ: `/uploads/avatar.jpg`).
- **Cách xử lý**:
  - `auth.ts`: Thêm logic fetch `avatar` từ DB trong nhánh `else if (token?.id)` (refresh token).
  - `auth.ts`: Gán `token.avatar = dbUser.avatar` và truyền sang `session.user.avatar`.
  - `UserDropdown.tsx`: Chuẩn hóa đường dẫn (`/` → prefix domain, `http` → dùng nguyên).

---

### BUG-005 — Thông báo mẫu bằng tiếng Anh trong Notification Dropdown
- **Mức độ**: 🟢 LOW | **Trạng thái**: ✅ FIXED
- **Môi trường**: Admin & Owner Dashboard
- **Ngày ghi nhận**: 2026-07-22
- **File liên quan**:
  - [`NotificationDropdown.tsx`](file:///c:/ThucTap/BDS/src/components/admin/dashboard/header/NotificationDropdown.tsx)
- **Mô tả**: Dropdown thông báo hiển thị danh sách placeholder bằng tiếng Anh ("New user registered", "Property approved"…) thay vì giao diện trống Việt hóa.
- **Cách xử lý**: Xóa toàn bộ `<li>` mẫu. Thay bằng Empty State với icon chuông, text "Chưa có thông báo mới" và đồng bộ màu primary `#0077bb`.

---

### BUG-006 — AI Chatbot hiện trên trang quản trị Admin/Owner
- **Mức độ**: 🟡 MEDIUM | **Trạng thái**: ✅ FIXED
- **Môi trường**: Admin & Owner Dashboard
- **Ngày ghi nhận**: 2026-07-23
- **File liên quan**:
  - [`ChatBot.tsx`](file:///c:/ThucTap/BDS/src/components/ai/ChatBot.tsx)
- **Mô tả**: Bong bóng Chatbot AI tư vấn BĐS hiển thị cả trên trang `/admin` và `/owner`, gây phân tâm và không phù hợp ngữ cảnh.
- **Cách xử lý**: Dùng `usePathname()` để kiểm tra pathname. Ẩn component nếu pathname bắt đầu bằng `/admin`, `/owner`, hoặc `/system`.

---

### BUG-007 — Application Error khi load trang trên Vercel (Client-side Exception)
- **Mức độ**: 🔴 CRITICAL | **Trạng thái**: ✅ FIXED
- **Môi trường**: Production (Vercel) — bds-sage.vercel.app
- **Ngày ghi nhận**: 2026-07-23
- **Mô tả**: Trang web hiển thị "Application error: a client-side exception has occurred" khi load trên Vercel, trong khi local dev hoạt động bình thường.
- **Nguyên nhân điều tra**:
  - Thường do import thư viện server-only (Node.js modules như `fs`, `https`, `bcrypt`) bị bundle vào client-side components.
  - Biến môi trường server (`process.env.*`) không được expose cho client-side.
  - Lỗi hydration mismatch giữa SSR và CSR.
- **Cách xử lý**: Kiểm tra logs trên Vercel Dashboard → Functions để xem stack trace cụ thể. Đảm bảo các thư viện Node.js chỉ dùng trong `route.ts` (API) hoặc Server Components, không import vào Client Components.
- **Ghi chú**: Lỗi này có thể tái phát khi tích hợp NKS API trong các bước tiếp theo nếu không tách biệt rõ server/client code.

---

### BUG-008 — Ảnh đại diện từ tài khoản NKS không hiển thị
- **Mức độ**: 🟡 MEDIUM | **Trạng thái**: ✅ FIXED
- **Môi trường**: Production & Local
- **Ngày ghi nhận**: 2026-07-23
- **Mô tả**: Người dùng đăng nhập qua NKS có avatar trên NKS nhưng không thấy ảnh hiển thị trên web BDS.
- **Nguyên nhân**:
  - Trường `avatar` từ NKS trả về dưới dạng đường dẫn tương đối (ví dụ: `storage/avatars/abc.jpg`) thay vì URL đầy đủ.
  - Logic mapping trong `mapNksUserToLocal` không chuẩn hóa path trước khi lưu vào DB.
- **Cách xử lý**:
  - Trong `UserDropdown.tsx`: Kiểm tra nếu avatar không bắt đầu bằng `http`, thêm prefix `https://data.nks.vn/`.
  - Trong `auth.ts`: Đảm bảo trường `avatar` luôn được include trong Session.

---

### BUG-009 — `nksId` chưa tồn tại trong schema → Không thể đồng bộ tin đăng lên NKS
- **Mức độ**: 🟠 HIGH | **Trạng thái**: 🔲 OPEN
- **Môi trường**: Production & Local
- **Ngày ghi nhận**: 2026-07-23
- **File liên quan**:
  - [`schema.prisma`](file:///c:/ThucTap/BDS/prisma/schema.prisma)
  - [`route.ts (create)`](file:///c:/ThucTap/BDS/src/app/api/properties/create/route.ts)
- **Mô tả**: Model `Property` và `PropertyImage` trong Prisma Schema chưa có trường `nksId` để lưu ID tương ứng trên hệ thống NKS. Khi đồng bộ tin đăng lên NKS thành công, không có chỗ lưu `nksId` trả về → Không thể gọi update/delete sau này.
- **Cách xử lý dự kiến**:
  ```prisma
  // Trong model Property
  nksId  String? @map("nks_id") @db.VarChar(255)
  
  // Trong model PropertyImage  
  nksImgId  String? @map("nks_img_id") @db.VarChar(255)
  ```
  - Chạy `npx prisma db push` sau khi thêm vào schema.
- **Phụ thuộc**: Cần hoàn thành trước khi triển khai tích hợp NKS API cho tin đăng.

---

### BUG-010 — Tin đăng tạo mới không đồng bộ lên NKS API
- **Mức độ**: 🟠 HIGH | **Trạng thái**: 🔲 OPEN
- **Môi trường**: Production & Local
- **Ngày ghi nhận**: 2026-07-23
- **File liên quan**:
  - [`route.ts (create)`](file:///c:/ThucTap/BDS/src/app/api/properties/create/route.ts)
  - [`nks.ts`](file:///c:/ThucTap/BDS/src/lib/nks.ts)
- **Mô tả**: Sau khi Owner/Agent tạo tin đăng thành công trên hệ thống BDS local, tin đăng không được đồng bộ lên tài khoản NKS của người dùng đó. Hàm `createNksProperty` đã có trong `nks.ts` nhưng chưa được gọi trong API route.
- **Cách xử lý dự kiến**: Sau khi `prisma.property.create()` thành công:
  1. Lấy `nksToken` từ `session.user.nksToken`
  2. Map fields BDS local → NKS payload format
  3. Gọi `createNksProperty(token, payload)` → nhận `nksId`
  4. Lưu `nksId` vào bản ghi `Property`
  5. Upload từng ảnh gallery lên NKS (`rsitemimg/add` - Base64)
  6. Lưu `nksImgId` vào từng `PropertyImage`
- **Lưu ý**: Nếu NKS API fail, BDS vẫn lưu tin local (không rollback). Cần xử lý async retry hoặc thông báo rõ.
- **Phụ thuộc**: BUG-009 (cần có `nksId` trong schema trước)

---

### BUG-011 — `phone` trên tin đăng bị hardcode fallback
- **Mức độ**: 🟡 MEDIUM | **Trạng thái**: 🔲 OPEN
- **Môi trường**: Production & Local
- **Ngày ghi nhận**: 2026-07-23
- **File liên quan**:
  - [`route.ts (create)`](file:///c:/ThucTap/BDS/src/app/api/properties/create/route.ts) — Dòng 123
- **Mô tả**:
  ```ts
  phone: user.phone || '0977.758.217'
  ```
  Nếu user không có SĐT trong session, tin đăng sẽ hiển thị số điện thoại hardcode `0977.758.217` của dev thay vì yêu cầu người dùng nhập.
- **Cách xử lý**: Thêm trường `phone` vào form đăng tin và validate bắt buộc hoặc lấy từ profile DB.

---

### BUG-012 — `transactionType` không được lưu vào DB khi tạo tin
- **Mức độ**: 🟡 MEDIUM | **Trạng thái**: 🔲 OPEN
- **Môi trường**: Production & Local
- **Ngày ghi nhận**: 2026-07-23
- **File liên quan**:
  - [`route.ts (create)`](file:///c:/ThucTap/BDS/src/app/api/properties/create/route.ts)
- **Mô tả**: Trường `purpose` (rent/sale) được nhận từ form nhưng không được map vào trường `transactionType` trong bảng `properties`. Hậu quả là bộ lọc "Mua/Thuê" trên bản đồ và listing không hoạt động chính xác với tin đăng mới.
- **Cách xử lý**:
  ```ts
  // Thêm vào data của prisma.property.create()
  transactionType: purpose || 'rent'
  ```

---

### BUG-013 — Rate Limit Chatbot reset khi Vercel serverless cold start
- **Mức độ**: 🟢 LOW | **Trạng thái**: 🔲 OPEN
- **Môi trường**: Production (Vercel)
- **Ngày ghi nhận**: 2026-07-23
- **File liên quan**:
  - [`chatbot/route.ts`](file:///c:/ThucTap/BDS/src/app/api/chatbot/route.ts)
- **Mô tả**: Rate limit của chatbot dùng `Map` in-memory. Khi Vercel serverless function bị cold start (restart sau idle), toàn bộ Map bị reset → Rate limit không hoạt động trên production thực tế.
- **Cách xử lý dự kiến**: Dùng Redis (Upstash) hoặc Vercel KV để lưu rate limit state persistent giữa các invocations.

---

## Ghi chú Chung

- **NKS API SSL**: `rejectUnauthorized: false` được đặt vì NKS dùng self-signed certificate. Đây là workaround tạm thời, **không nên dùng trong môi trường production lý tưởng**.
- **SCRM CRM**: `NODE_TLS_REJECT_UNAUTHORIZED = '0'` được set inline trước mỗi call đến `sdata.io.vn`. Cần chuyển sang environment variable cấu hình đúng cách.
- **Appointment Auto-Approve**: Lịch hẹn hiện tại được tạo với `status: "approved"` luôn. Chưa có luồng chủ nhà duyệt thủ công (UI có nhưng logic tạo bỏ qua pending).
- **Property Auto-Approve**: Tương tự, tin đăng mới được set `status: "approved"` luôn mà không qua duyệt Admin.

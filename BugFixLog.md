# Nhật Ký Sửa Lỗi & Cập Nhật Tính Năng - BDS Rental

Tài liệu này lưu trữ thông tin chi tiết về các lỗi đã được khắc phục và các cập nhật chức năng trên hệ thống **BDS Rental** để phục vụ việc tra cứu và quản lý dự án.

---

## Danh Sách Lỗi & Cập Nhật

### 1. Đồng bộ thương hiệu Chatbot AI
* **Vấn đề**: Chatbot hiển thị tên thương hiệu cũ là "BDS NKS" thay vì "BDS Rental".
* **Nguyên nhân**: Tên hiển thị và chỉ dẫn hệ thống (system instructions) chưa được cập nhật đồng bộ.
* **Cách khắc phục**:
  * Cập nhật tiêu đề cửa sổ chat trong [chat-widget.blade.php](file:///c:/ThucTap/BDS/resources/views/components/chat-widget.blade.php#L208).
  * Cập nhật nội dung System Instructions trong [ChatService.php](file:///c:/ThucTap/BDS/app/Services/ChatService.php#L55) để chatbot tự nhận diện là trợ lý ảo của **BDS Rental**.

### 2. Thiếu phân cấp Breadcrumbs trang chi tiết tin đăng
* **Vấn đề**: Breadcrumbs hiển thị thiếu cấp danh mục trung gian `Nhà đất` (ví dụ: Trang chủ / Nhà nguyên căn...).
* **Cách khắc phục**: Bổ sung phân cấp `Nhà đất` vào thanh điều hướng breadcrumbs trong tệp [detail.blade.php](file:///c:/ThucTap/BDS/resources/views/detail.blade.php#L13).

### 3. Tối ưu Bản đồ tương tác & Custom Popup
* **Vấn đề**:
  * Bản đồ tự động zoom cục bộ vào Gò Vấp thay vì hiển thị toàn bộ tin đăng.
  * Bộ lọc mục đích "Mua bán / Cho thuê" không lọc được tin từ API đối tác do thiếu trường phân loại.
  * Bong bóng ghim (Marker) và Popup tóm tắt tin thiết kế chưa cân đối.
* **Cách khắc phục**:
  * Áp dụng phương thức `fitBounds()` trong [map.blade.php](file:///c:/ThucTap/BDS/resources/views/map.blade.php#L920) để tự động co giãn khung hình hiển thị toàn bộ các điểm ghim trên bản đồ khi tải trang.
  * Bổ sung gán trường dữ liệu mục đích giao dịch `transaction_type` từ API trong [PropertyService.php](file:///c:/ThucTap/BDS/app/Services/PropertyService.php#L816) để bộ lọc đồng bộ chính xác.
  * Thiết kế lại giao diện Popup: Đưa padding về `0`, ảnh lấp đầy góc bo tròn, badge loại hình nằm góc trái trên ảnh, nút đóng tròn nổi bật góc phải. Trực quan hóa bong bóng ghim khi được click chọn sang màu trắng viền xanh và đính kèm dấu tích xanh lục.

### 4. Lỗi 422 khi đặt lịch hẹn xem tin đăng từ API đối tác
* **Vấn đề**: Gửi yêu cầu đặt lịch hẹn đi xem nhà trên các tin đăng lấy từ API đối tác bị báo lỗi `422 Unprocessable Entity` với thông báo `The property id field must be a valid UUID`.
* **Nguyên nhân**:
  * Bảng `appointments` ràng buộc trường `property_id` dạng khóa ngoại bắt buộc kiểu UUID cục bộ. Các tin API có ID dạng số nguyên (ví dụ: `90`) nên không lưu được và bị chặn bởi validator.
* **Cách khắc phục**:
  * Viết tệp migration [2026_07_01_000000_change_property_id_in_appointments_table.php](file:///c:/ThucTap/BDS/database/migrations/2026_07_01_000000_change_property_id_in_appointments_table.php) xóa bỏ ràng buộc khóa ngoại cũ và đổi kiểu trường `property_id` sang kiểu chuỗi (String).
  * Điều chỉnh validator trong [AppointmentController.php](file:///c:/ThucTap/BDS/app/Http/Controllers/Tenant/AppointmentController.php#L26) để chấp nhận `property_id` kiểu chuỗi.
  * Nâng cấp logic tại [AppointmentService.php](file:///c:/ThucTap/BDS/app/Services/AppointmentService.php#L21) sử dụng `PropertyService` để kiểm tra sự tồn tại của tin đăng (hỗ trợ cả nội bộ lẫn API) và chỉ gửi mail cho chủ nhà nếu tin đăng đó thuộc cơ sở dữ liệu nội bộ.

### 5. Lỗi so sánh kiểu dữ liệu PostgreSQL (uuid = character varying)
* **Vấn đề**: Khi chuyển `property_id` sang dạng chuỗi, PostgreSQL báo lỗi `SQLSTATE[42883]: Undefined function: 7 ERROR: operator does not exist: uuid = character varying` khi chạy các câu lệnh JOIN liên quan đến lịch hẹn của chủ nhà.
* **Nguyên nhân**:
  * PostgreSQL rất nghiêm ngặt, không cho phép so sánh trực tiếp hoặc JOIN cột kiểu UUID (`properties.id`) với cột kiểu VARCHAR (`appointments.property_id`).
  * Trình eager load của Eloquent (`with('property')`) crash khi danh sách ID chứa chuỗi số thường (ví dụ: `"88"`) không thể ép kiểu thành UUID.
* **Cách khắc phục**:
  * Thay thế quan hệ `hasManyThrough` trong model [User.php](file:///c:/ThucTap/BDS/app/Models/User.php#L52) bằng truy vấn mảng `whereIn('property_id', $propertyIds)` thủ công để tránh lệnh JOIN trực tiếp.
  * Xây dựng quan hệ tùy biến [SafeUuidBelongsTo.php](file:///c:/ThucTap/BDS/app/Relations/SafeUuidBelongsTo.php). Lớp này tự động lọc bỏ các ID không phải UUID trước khi nạp dữ liệu (`addEagerConstraints`) và ngăn chặn truy vấn DB nếu khóa ngoại không phải UUID lúc lazy load (`getResults`).
  * Áp dụng `SafeUuidBelongsTo` cho quan hệ `property` tại [Appointment.php](file:///c:/ThucTap/BDS/app/Models/Appointment.php#L35) và [Wishlist.php](file:///c:/ThucTap/BDS/app/Models/Wishlist.php#L24).
  * Viết hàm Accessor `getPropertyAttribute` trong [Appointment.php](file:///c:/ThucTap/BDS/app/Models/Appointment.php#L42): Nếu tin thuộc API, tự động kéo thông tin từ API về và dựng thành một thực thể ảo `Property` đầy đủ thông tin để hiển thị an toàn trên tất cả các view.

### 6. Đổi tên nút & Thêm nút gạt "Địa chỉ mới" trên Navbar
* **Yêu cầu**: 
  * Chuyển chữ "Đăng tin miễn phí" thành "Đăng tin" và làm nút nhỏ gọn lại.
  * Thêm nút gạt "Địa chỉ mới" bên cạnh với màu sắc đồng bộ giao diện.
* **Cách khắc phục**:
  * Thay đổi toàn bộ nút thành "Đăng tin" ở tất cả các khu vực navbar (máy tính, mobile, guest).
  * Thu nhỏ khoảng đệm và cỡ chữ của nút: padding ngang chuyển thành `px-2.5 lg:px-3.5`, dọc thành `py-1.5 lg:py-2`, cỡ chữ thành `text-xs lg:text-sm` để nút gọn gàng và tinh tế hơn.
  * Tích hợp thêm nút gạt **"Địa chỉ mới"** thiết kế dạng chuyển mạch iOS, sử dụng Alpine.js quản lý trạng thái, tự động lưu lựa chọn vào `localStorage` (`diaChiMoi`) và kích hoạt sự kiện window `dia-chi-moi-toggled`. Nút gạt tự động đồng bộ màu nền xanh dương (`bg-primary`) khi bật và đổi màu chữ linh hoạt theo trạng thái cuộn trang của thanh điều hướng.

### 7. Lỗi giới hạn độ dài Quận/Huyện khi đăng tin mới & Tích hợp Log Debug
* **Vấn đề**: Người dùng điền đầy đủ thông tin đăng tin mới nhưng nhấn gửi tin thì không có phản hồi/thất bại mà không biết lý do.
* **Nguyên nhân**:
  * Trường `district` trong [StorePropertyRequest.php](file:///c:/ThucTap/BDS/app/Http/Requests/Owner/StorePropertyRequest.php#L23) thiết lập quy tắc xác thực độ dài tối đa quá ngắn: `max:10`. Khi người dùng chọn những quận có tên dài hơn 10 ký tự như "Quận Gò Vấp" (11 ký tự), "Quận Bình Thạnh" (15 ký tự), hay "Quận Tân Bình" (13 ký tự), Validator của Laravel sẽ chặn lại và trả về lỗi khiến người dùng không đăng được tin.
* **Cách khắc phục**:
  * Tăng độ dài tối đa của trường `district` từ `10` lên `255` trong [StorePropertyRequest.php](file:///c:/ThucTap/BDS/app/Http/Requests/Owner/StorePropertyRequest.php#L23) để hỗ trợ đầy đủ các quận/huyện trên toàn quốc.
  * Tích hợp ghi đè phương thức `failedValidation` trong [StorePropertyRequest.php](file:///c:/ThucTap/BDS/app/Http/Requests/Owner/StorePropertyRequest.php#L47) để ghi nhận chi tiết danh sách lỗi validator và dữ liệu đầu vào (loại trừ tệp tin ảnh) vào hệ thống Log của Laravel (`Log::warning`).
  * Bao bọc toàn bộ logic lưu tin đăng `store()` trong [PropertyController.php](file:///c:/ThucTap/BDS/app/Http/Controllers/Owner/PropertyController.php#L27) bằng khối lệnh `try-catch`. Khi xảy ra lỗi ngoại lệ (DB, Cloudinary, ...), hệ thống sẽ tự động bắt lỗi, ghi lại đầy đủ vết lỗi (stack trace) vào file Log (`Log::error`) và quay về trang form hiển thị thông báo lỗi chi tiết thay vì bị màn hình trắng (White Screen of Death).


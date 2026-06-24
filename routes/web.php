<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Dữ liệu giả lập (Mock Data) dùng chung cho trang chủ, trang chi tiết và trang bản đồ
if (!function_exists('getMockProperties')) {
    function getMockProperties() {
        return [
        [
            'id' => 1,
            'title' => 'Căn hộ chung cư Vinhomes Ocean Park Studio Full Nội Thất',
            'type' => 'Căn hộ chung cư',
            'price' => '6.5 triệu/tháng',
            'price_label' => '6.5tr',
            'area' => '35',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'location' => 'Gia Lâm, Hà Nội',
            'lat' => 20.9944,
            'lng' => 105.9567,
            'image' => 'images/apartment_3.png',
            'images' => [
                'images/apartment_3.png',
                'images/apartment_1.png',
                'images/apartment_2.png',
                'images/hero_bg.png'
            ],
            'direction' => 'Đông Nam',
            'furniture' => 'Đầy đủ nội thất (Tivi, Tủ lạnh, Máy giặt, Điều hòa, Sofa, Giường nệm)',
            'legal' => 'Sổ hồng, Hợp đồng cho thuê tối thiểu 1 năm',
            'is_vip' => true,
            'is_new' => false,
            'agent' => [
                'name' => 'Nguyễn Hải Đăng',
                'phone' => '0987.654.321',
                'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff'
            ],
            'created_at' => '2 giờ trước',
            'description' => 'Căn hộ Studio Vinhomes Ocean Park với thiết kế tối ưu, thoáng đãng, tận dụng tối đa ánh sáng tự nhiên. Căn hộ đã trang bị đầy đủ nội thất cao cấp chỉ việc xách vali vào ở. \n\nTiện ích nội khu đẳng cấp: Hồ nước mặn Ocean Park, biển hồ nước ngọt cát trắng mịn, bể bơi bốn mùa, sân tennis, cầu lông, khu BBQ ngoài trời. Hệ thống an ninh đa lớp, camera giám sát 24/7. Phù hợp cho người độc thân hoặc cặp vợ chồng trẻ.'
        ],
        [
            'id' => 2,
            'title' => 'Căn hộ Duplex Vinhomes Metropolis Liễu Giai view hồ cực đẹp',
            'type' => 'Căn hộ chung cư',
            'price' => '18 triệu/tháng',
            'price_label' => '18tr',
            'area' => '85',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Ba Đình, Hà Nội',
            'lat' => 21.0315,
            'lng' => 105.8152,
            'image' => 'images/apartment_2.png',
            'images' => [
                'images/apartment_2.png',
                'images/apartment_1.png',
                'images/apartment_3.png',
                'images/hero_bg.png'
            ],
            'direction' => 'Tây Nam',
            'furniture' => 'Full nội thất sang trọng nhập khẩu Châu Âu',
            'legal' => 'Hợp đồng công chứng, cọc 2 tháng',
            'is_vip' => true,
            'is_new' => true,
            'agent' => [
                'name' => 'Trần Thị Tuyết',
                'phone' => '0912.345.678',
                'avatar' => 'https://ui-avatars.com/api/?name=Tran+Thi+Tuyet&background=0077bb&color=fff'
            ],
            'created_at' => '4 giờ trước',
            'description' => 'Căn hộ thông tầng Duplex độc bản tại Vinhomes Metropolis Liễu Giai, sở hữu tầm nhìn panorama triệu đô hướng trực diện hồ Ngọc Khánh và hồ Tây lộng gió. Căn hộ được thiết kế thông tầng thoáng đãng với phòng khách cao 6m, nội thất hiện đại sang trọng.\n\nCư dân được tận hưởng toàn bộ dịch vụ tiêu chuẩn 5 sao chân tòa nhà, trung tâm thương mại Vincom sầm uất. Cọc 2 tháng thanh toán đầu tháng.'
        ],
        [
            'id' => 3,
            'title' => 'Biệt thự sân vườn Ciputra hiện đại có hồ bơi riêng biệt lập',
            'type' => 'Biệt thự / Villa',
            'price' => '45 triệu/tháng',
            'price_label' => '45tr',
            'area' => '250',
            'bedrooms' => 4,
            'bathrooms' => 4,
            'location' => 'Tây Hồ, Hà Nội',
            'lat' => 21.0722,
            'lng' => 105.7984,
            'image' => 'images/house_2.png',
            'images' => [
                'images/house_2.png',
                'images/house_1.png',
                'images/hero_bg.png',
                'images/apartment_1.png'
            ],
            'direction' => 'Nam',
            'furniture' => 'Nội thất liền tường cao cấp, khách thuê tự trang bị đồ rời',
            'legal' => 'Hợp đồng dài hạn từ 2 năm trở lên',
            'is_vip' => true,
            'is_new' => false,
            'agent' => [
                'name' => 'Lê Hoàng Long',
                'phone' => '0909.123.456',
                'avatar' => 'https://ui-avatars.com/api/?name=Le+Hoang+Long&background=0077bb&color=fff'
            ],
            'created_at' => '1 ngày trước',
            'description' => 'Biệt thự đơn lập sân vườn tuyệt đẹp tọa lạc tại vị trí đắc địa quận Tây Hồ. Biệt thự có khuôn viên rộng rãi với sân cỏ xanh mướt, hồ bơi riêng biệt ngoài trời cực mát mẻ.\n\nThiết kế 3 tầng gồm 4 phòng ngủ rộng rãi ngập tràn ánh sáng, phòng khách rộng lớn liên thông bếp ăn hiện đại. Khu vực an ninh, yên tĩnh tuyệt đối, dân trí cao, rất thích hợp cho các chuyên gia nước ngoài hoặc gia đình thượng lưu sinh sống.'
        ],
        [
            'id' => 4,
            'title' => 'Nhà nguyên căn 3 tầng ngõ xe hơi Duy Tân thích hợp làm văn phòng',
            'type' => 'Nhà nguyên căn',
            'price' => '22 triệu/tháng',
            'price_label' => '22tr',
            'area' => '120',
            'bedrooms' => 3,
            'bathrooms' => 3,
            'location' => 'Cầu Giấy, Hà Nội',
            'lat' => 21.0362,
            'lng' => 105.7865,
            'image' => 'images/house_1.png',
            'images' => [
                'images/house_1.png',
                'images/house_2.png',
                'images/hero_bg.png',
                'images/apartment_3.png'
            ],
            'direction' => 'Đông Bắc',
            'furniture' => 'Cơ bản (Thiết bị vệ sinh, hệ thống đèn chiếu sáng, điều hòa các phòng)',
            'legal' => 'Chính chủ cho thuê, hợp đồng lâu dài',
            'is_vip' => false,
            'is_new' => true,
            'agent' => [
                'name' => 'Phạm Minh Tuấn',
                'phone' => '0888.777.999',
                'avatar' => 'https://ui-avatars.com/api/?name=Pham+Minh+Tuan&background=0077bb&color=fff'
            ],
            'created_at' => '3 ngày trước',
            'description' => 'Nhà nguyên căn mặt tiền ngõ lớn xe hơi tránh nhau tại trung tâm Cầu Giấy. Nhà xây dựng kiên cố 1 trệt 2 lầu sân thượng thoáng mát. Mặt tiền rộng 6m đỗ xe thoải mái.\n\nKhông gian trống sàn rộng rãi, thích hợp làm văn phòng công ty, spa làm đẹp, trung tâm đào tạo hoặc kinh doanh online kết hợp ở gia đình. Vị trí giao thương thuận lợi, di chuyển nhanh sang các tuyến phố lớn Duy Tân, Xuân Thủy, Trần Thái Tông.'
        ],
        [
            'id' => 5,
            'title' => 'Căn hộ chung cư Sky City Láng Hạ nội thất tối giản hiện đại',
            'type' => 'Căn hộ chung cư',
            'price' => '12 triệu/tháng',
            'price_label' => '12tr',
            'area' => '72',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'location' => 'Đống Đa, Hà Nội',
            'lat' => 21.0185,
            'lng' => 105.8159,
            'image' => 'images/apartment_1.png',
            'images' => [
                'images/apartment_1.png',
                'images/apartment_2.png',
                'images/apartment_3.png',
                'images/hero_bg.png'
            ],
            'direction' => 'Bắc',
            'furniture' => 'Đầy đủ nội thất thông minh tối giản diện tích',
            'legal' => 'Hợp đồng thuê 1 năm, cọc 1 tháng',
            'is_vip' => false,
            'is_new' => false,
            'agent' => [
                'name' => 'Hoàng Thanh Mai',
                'phone' => '0977.888.999',
                'avatar' => 'https://ui-avatars.com/api/?name=Hoang+Thanh+Mai&background=0077bb&color=fff'
            ],
            'created_at' => '4 ngày trước',
            'description' => 'Căn hộ chung cư 2 phòng ngủ nằm trong tổ hợp chung cư cao cấp Sky City Láng Hạ. Căn hộ được decor theo phong cách Bắc Âu (Scandinavian) tối giản và hiện đại, mang lại cảm giác cực kỳ thoải mái và dễ chịu sau giờ làm việc căng thẳng.\n\nDịch vụ quản lý tòa nhà chuyên nghiệp, có phòng tập gym, yoga, siêu thị chân tòa nhà. Vị trí trung tâm Đống Đa dễ dàng kết nối đi Ba Đình, Cầu Giấy và Thanh Xuân.'
        ],
        [
            'id' => 6,
            'title' => 'Văn phòng hiện đại sẵn bàn ghế làm việc tại trung tâm Hoàn Kiếm',
            'type' => 'Văn phòng cho thuê',
            'price' => '35 triệu/tháng',
            'price_label' => '35tr',
            'area' => '110',
            'bedrooms' => 0,
            'bathrooms' => 2,
            'location' => 'Hoàn Kiếm, Hà Nội',
            'lat' => 21.0285,
            'lng' => 105.8521,
            'image' => 'images/apartment_2.png',
            'images' => [
                'images/apartment_2.png',
                'images/apartment_3.png',
                'images/hero_bg.png',
                'images/house_1.png'
            ],
            'direction' => 'Đông',
            'furniture' => 'Bàn ghế làm việc cao cấp, tủ tài liệu, máy chiếu, bảng viết',
            'legal' => 'Hợp đồng xuất hóa đơn đỏ VAT đầy đủ',
            'is_vip' => false,
            'is_new' => false,
            'agent' => [
                'name' => 'Nguyễn Hải Đăng',
                'phone' => '0987.654.321',
                'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff'
            ],
            'created_at' => '5 ngày trước',
            'description' => 'Văn phòng cho thuê cao cấp nằm tại tầng cao trung tâm sầm uất quận Hoàn Kiếm. Không gian văn phòng được thiết kế theo tiêu chuẩn quốc tế, trang bị sẵn đầy đủ hệ thống bàn ghế làm việc, tủ hồ sơ, thiết bị phòng họp hiện đại.\n\nGiá thuê đã bao gồm phí quản lý tòa nhà, nước sinh hoạt và dịch vụ dọn dẹp vệ sinh hàng ngày. Thích hợp cho doanh nghiệp start-up, văn phòng đại diện quy mô 15-20 nhân viên.'
        ],
    ];
}
}

// Route trang chủ
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route trang chi tiết bất động sản
Route::get('/property/{id}', [App\Http\Controllers\PropertyController::class, 'show'])->name('properties.show');

Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Route trang hồ sơ thành viên (Bảo vệ bởi middleware auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/cccd', [ProfileController::class, 'updateCccd'])->name('profile.cccd');
    Route::post('/profile/scan-cccd', [ProfileController::class, 'scanCccd'])->name('profile.scan-cccd');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/register-owner', [ProfileController::class, 'registerOwner'])->name('profile.register-owner');
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // Đặt lịch xem nhà
    Route::post('/appointments', [App\Http\Controllers\AppointmentController::class, 'book'])->name('appointments.book');
    // Hủy lịch hẹn
    Route::post('/appointments/{id}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

// Route dành cho Chủ nhà (Bảo vệ bởi auth và owner middleware)
Route::middleware(['auth', 'owner'])->group(function () {
    // Quản lý tin đăng (CRUD)
    Route::get('/properties/create', [\App\Http\Controllers\Owner\PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [\App\Http\Controllers\Owner\PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{id}/edit', [\App\Http\Controllers\Owner\PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{id}', [\App\Http\Controllers\Owner\PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{id}', [\App\Http\Controllers\Owner\PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::post('/properties/{id}/extend', [\App\Http\Controllers\Owner\PropertyController::class, 'extend'])->name('properties.extend');
    Route::post('/properties/{id}/hide', [\App\Http\Controllers\Owner\PropertyController::class, 'hide'])->name('properties.hide');

    // Quản lý lịch hẹn
    Route::post('/appointments/{id}/approve', [\App\Http\Controllers\Owner\AppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{id}/reject', [\App\Http\Controllers\Owner\AppointmentController::class, 'reject'])->name('appointments.reject');
    Route::post('/appointments/{id}/complete', [\App\Http\Controllers\Owner\AppointmentController::class, 'complete'])->name('appointments.complete');
});

// Route dành cho Admin (Bảo vệ bởi auth và admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý người dùng
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // Quản lý tin đăng
    Route::get('/properties', [\App\Http\Controllers\Admin\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/{id}', [\App\Http\Controllers\Admin\PropertyController::class, 'show'])->name('properties.show');
    Route::post('/properties/{id}/status', [\App\Http\Controllers\Admin\PropertyController::class, 'updateStatus'])->name('properties.status');
    Route::delete('/properties/{id}', [\App\Http\Controllers\Admin\PropertyController::class, 'destroy'])->name('properties.destroy');
    
    // Quản lý lịch hẹn
    Route::get('/appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/{id}/cancel', [\App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // Quản lý danh mục (CRUD)
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Báo cáo thống kê
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
});

// Route trang danh sách bất động sản
Route::get('/listings', [App\Http\Controllers\PropertyController::class, 'index'])->name('listings.index');

// Route trang bản đồ tìm kiếm
Route::get('/map', [App\Http\Controllers\PropertyController::class, 'map'])->name('properties.map');

// Route API gợi ý tìm kiếm (Autocomplete)
Route::get('/api/properties/autocomplete', [App\Http\Controllers\PropertyController::class, 'autocomplete'])->name('properties.autocomplete');


// Route phục vụ file vietnam_provinces.json cho Vercel (bổ sung caching)
Route::get('/vietnam_provinces.json', function () {
    $path = public_path('vietnam_provinces.json');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/json',
        'Cache-Control' => 'public, max-age=86400',
    ]);
});

// Route lựa chọn loại tin đăng (Bán / Cho thuê)
Route::get('/properties/choose-type', function () {
    return view('properties.choose_type');
})->name('properties.choose-type');


// Route dành riêng cho Khách thuê (Tenant) (Bảo vệ bởi auth và tenant middleware)
Route::middleware(['auth', 'tenant'])->group(function () {
    //
});

// Route đăng nhập / đăng ký (Bảo vệ bởi middleware guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

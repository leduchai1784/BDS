<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;


// Route trang chủ
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route trang chi tiết bất động sản
Route::get('/property/{id}', [App\Http\Controllers\PropertyController::class, 'show'])->name('properties.show');

Route::post('/wishlist/toggle', [\App\Http\Controllers\Tenant\WishlistController::class, 'toggle'])->name('wishlist.toggle');

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
    Route::post('/appointments', [\App\Http\Controllers\Tenant\AppointmentController::class, 'book'])->name('appointments.book');
    // Hủy lịch hẹn
    Route::post('/appointments/{id}/cancel', [\App\Http\Controllers\Tenant\AppointmentController::class, 'cancel'])->name('appointments.cancel');
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

// Route dự án
Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');

// Route nhà môi giới
Route::get('/agents', [App\Http\Controllers\AgentController::class, 'index'])->name('agents.index');
Route::get('/agents/{id}', [App\Http\Controllers\AgentController::class, 'show'])->name('agents.show');

// Route trang tin tức
Route::get('/news', [App\Http\Controllers\HomeController::class, 'news'])->name('news');
Route::get('/news/{slug}', [App\Http\Controllers\HomeController::class, 'newsDetail'])->name('news.show');

// Route trang bản đồ tìm kiếm
Route::get('/map', [App\Http\Controllers\PropertyController::class, 'map'])->name('properties.map');

// Route API gợi ý tìm kiếm (Autocomplete)
Route::get('/api/properties/autocomplete', [App\Http\Controllers\PropertyController::class, 'autocomplete'])->name('properties.autocomplete');

// Route API AI Chatbot
Route::post('/chatbot/send', [\App\Http\Controllers\ChatController::class, 'chat'])->name('api.chat');

// Route API Geocoding Proxy (tránh CORS và Rate Limit của OpenStreetMap Nominatim)
Route::get('/location/geocode', function (Illuminate\Http\Request $request) {
    $query = $request->query('q');
    if (empty($query)) {
        return response()->json([]);
    }
    $cacheKey = 'geocode_' . md5($query);
    return Illuminate\Support\Facades\Cache::remember($cacheKey, 60 * 24 * 30, function () use ($query) {
        try {
            $response = Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'BdsRentalApp/1.0 (lehai17082004@gmail.com)',
            ])
            ->timeout(8)
            ->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $query,
                'limit' => 1,
            ]);
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Illuminate\Support\Facades\Log::warning('Geocoding proxy failed: ' . $e->getMessage());
        }
        return [];
    });
});

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
    Route::post('/login/forget-account', [AuthController::class, 'forgetAccount'])->name('login.forget-account');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// API địa chỉ lấy từ NKS
Route::get('/api/locations/provinces', [LocationController::class, 'getProvinces']);
Route::get('/api/locations/wards', [LocationController::class, 'getWards']);

<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use App\Services\WishlistService;
use App\Services\AppointmentService;
use App\Services\NksAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected ProfileService $profileService;
    protected WishlistService $wishlistService;
    protected AppointmentService $appointmentService;
    protected NksAuthService $nksAuthService;

    public function __construct(
        ProfileService $profileService,
        WishlistService $wishlistService,
        AppointmentService $appointmentService,
        NksAuthService $nksAuthService
    ) {
        $this->profileService = $profileService;
        $this->wishlistService = $wishlistService;
        $this->appointmentService = $appointmentService;
        $this->nksAuthService = $nksAuthService;
    }

    /**
     * Display member profile / dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Self-healing sync: pull latest profile & CCCD details from NKS if logged in via NKS
        if ($user->nks_token) {
            $nksInfo = $this->nksAuthService->getUserInfo($user->nks_token);
            if ($nksInfo['success'] && !empty($nksInfo['user'])) {
                $localData = $this->nksAuthService->mapNksUserToLocal($nksInfo['user'], $user->nks_token);
                $user->update($localData);
                $user->refresh();
            }
        }

        $favorites = $this->wishlistService->getUserFavorites($user->id);

        $userData = [
            'name'         => $user->name,
            'firstname'    => $user->firstname,
            'lastname'     => $user->lastname,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'avatar'       => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0077bb&color=fff',
            'role'         => $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'owner' ? 'Chủ nhà / Môi giới' : 'Thành viên thuê nhà'),
            'join_date'    => $user->created_at ? $user->created_at->format('d/m/Y') : '06/01/2015',
            'gender'       => $user->gender,
            'dob'          => $user->dob,
            'pob'          => $user->pob,
            'id_number'    => $user->id_number,
            'id_date'      => $user->id_date,
            'id_place'     => $user->id_place,
            'cccd_front'   => $user->cccd_front,
            'cccd_back'    => $user->cccd_back,
            'add_street'   => $user->add_street,
            'add_ward'     => $user->add_ward,
            'add_district' => $user->add_district,
            'add_province' => $user->add_province,
            'permanent_address' => $user->permanent_address,
            'zalo_id'      => $user->zalo_id,
            'zalo_key'     => $user->zalo_key,
            'intro'        => $user->intro,
            'website'      => $user->website,
        ];
        
        // Handle admin profile view
        if ($user->role === 'admin') {
            return view('profile', [
                'user' => $userData,
                'stats' => null,
                'properties' => $favorites,
                'appointments' => collect()
            ]);
        }
        
        // Check user role
        if ($user->role === 'owner') {
            // Owner stats
            $totalProperties = $user->properties()->count();
            $totalViews = $user->properties()->sum('views_count');
            $totalAppointments = $user->ownerAppointments()->count();
            
            // Owner lists
            $myProperties = $user->properties()->latest()->get();
            $ownerAppointments = $user->ownerAppointments()
                ->with(['property', 'user'])
                ->latest()
                ->get();

            $myBookedAppointments = $this->appointmentService->getUserAppointments($user->id);

            // Load categories and edit target property
            $categories = \App\Models\Category::all();
            $editProperty = null;
            if (request('tab') === 'edit_property' && request('property_id') && \Illuminate\Support\Str::isUuid(request('property_id'))) {
                $editProperty = \App\Models\Property::find(request('property_id'));
                if ($editProperty) {
                    abort_if($editProperty->owner_id !== $user->id, 403, 'Bạn không có quyền chỉnh sửa tin đăng này.');
                }
            }
                
            return view('profile', [
                'user' => $userData,
                'stats' => [
                    'total_properties' => $totalProperties,
                    'total_views' => $totalViews,
                    'total_appointments' => $totalAppointments,
                ],
                'myProperties' => $myProperties,
                'ownerAppointments' => $ownerAppointments,
                'properties' => $favorites, // keep favorites just in case
                'appointments' => $myBookedAppointments,
                'categories' => $categories,
                'property' => $editProperty
            ]);
        } else {
            // Tenant stats
            $totalFavorites = $favorites->count();
            $tenantAppointments = $this->appointmentService->getUserAppointments($user->id);
            $totalAppointments = $tenantAppointments->count();
            
            return view('profile', [
                'user' => $userData,
                'stats' => [
                    'total_properties' => 0,
                    'total_favorites' => $totalFavorites,
                    'total_appointments' => $totalAppointments,
                ],
                'properties' => $favorites,
                'appointments' => $tenantAppointments
            ]);
        }
    }

    /**
     * Update user details.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|integer',
            'dob' => 'nullable|string|max:50',
            'add_street' => 'nullable|string|max:255',
            'add_ward' => 'nullable|string|max:100',
            'add_district' => 'nullable|string|max:100',
            'add_province' => 'nullable|string|max:100',
            'intro' => 'nullable|string|max:1000',
            'website' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng bởi thành viên khác.',
        ]);

        // Convert YYYY-MM-DD from type="date" to dd/mm/yyyy for DB and API compatibility
        if ($request->filled('dob') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->dob)) {
            try {
                $request->merge([
                    'dob' => \Carbon\Carbon::createFromFormat('Y-m-d', $request->dob)->format('d/m/Y')
                ]);
            } catch (\Exception $e) {}
        }

        // Split name into firstname and lastname
        $fullName = trim($request->name);
        $parts = explode(' ', $fullName);
        if (count($parts) > 1) {
            $lastname = array_pop($parts);
            $firstname = implode(' ', $parts);
        } else {
            $firstname = '';
            $lastname = $fullName;
        }

        $updateData = $request->only([
            'name', 'phone', 'email', 'gender', 'dob',
            'add_street', 'add_ward', 'add_district', 'add_province', 'intro', 'website'
        ]);
        $updateData['firstname'] = $firstname;
        $updateData['lastname']  = $lastname;

        // ✅ 1. Save to local DB immediately (fast, no external calls)
        $this->profileService->updateProfile($user->id, $updateData);

        // ✅ 2. Sync to NKS AFTER response is sent — user doesn't wait for this
        if ($user->nks_token) {
            $nksUpdateData = $updateData;
            if (!empty($nksUpdateData['dob']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $nksUpdateData['dob'])) {
                try {
                    $nksUpdateData['dob'] = \Carbon\Carbon::createFromFormat('d/m/Y', $nksUpdateData['dob'])->format('Y-m-d');
                } catch (\Exception $e) {}
            }

            $nksAuthService = $this->nksAuthService;
            $nksToken       = $user->nks_token;

            // terminating() runs after response is flushed to browser
            app()->terminating(function () use ($nksAuthService, $nksToken, $nksUpdateData) {
                $nksAuthService->updateInfo($nksToken, $nksUpdateData);
            });
        }

        return redirect()->route('profile.index', ['tab' => 'profile', 'subtab' => 'info'])->with('success', 'Hồ sơ cá nhân đã được cập nhật thành công!');
    }


    /**
     * Update user avatar separately.
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|string'
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh đại diện.',
            'avatar.string' => 'Dữ liệu ảnh đại diện không hợp lệ.'
        ]);

        $avatarData = $request->input('avatar');

        // Handle Avatar upload locally (will fail gracefully on read-only file systems)
        $avatarPath = $this->profileService->updateAvatar($user->id, $avatarData);

        // Sync to NKS if user has a token
        if ($user->nks_token) {
            $this->nksAuthService->updateAvatar($user->nks_token, $avatarData);

            // Fetch the updated user profile from NKS and save the hosted avatar URL locally
            $nksInfo = $this->nksAuthService->getUserInfo($user->nks_token);
            if ($nksInfo['success'] && !empty($nksInfo['user']['avatar'])) {
                $user->update([
                    'avatar' => $nksInfo['user']['avatar']
                ]);
            }
        }

        return redirect()->route('profile.index', ['tab' => 'profile', 'subtab' => 'avatar'])->with('success', 'Ảnh đại diện đã được cập nhật thành công!');
    }

    /**
     * Update user CCCD details.
     */
    public function updateCccd(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'dob' => 'required|string|max:50',
            'pob' => 'required|string|max:255',
            'id_number' => 'required|string|max:50',
            'id_date' => 'required|string|max:50',
            'id_place' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'cccd_front' => 'nullable|string',
            'cccd_back' => 'nullable|string',
        ], [
            'dob.required' => 'Ngày sinh không được để trống.',
            'pob.required' => 'Quê quán không được để trống.',
            'id_number.required' => 'Số CCCD không được để trống.',
            'id_date.required' => 'Ngày cấp không được để trống.',
            'id_place.required' => 'Nơi cấp không được để trống.',
            'permanent_address.required' => 'Nơi thường trú không được để trống.',
            'cccd_front.string' => 'Ảnh mặt trước CCCD không hợp lệ.',
            'cccd_back.string' => 'Ảnh mặt sau CCCD không hợp lệ.',
        ]);

        // Convert YYYY-MM-DD from type="date" to dd/mm/yyyy for DB and API compatibility
        if ($request->filled('dob') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->dob)) {
            try {
                $request->merge([
                    'dob' => \Carbon\Carbon::createFromFormat('Y-m-d', $request->dob)->format('d/m/Y')
                ]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('id_date') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->id_date)) {
            try {
                $request->merge([
                    'id_date' => \Carbon\Carbon::createFromFormat('Y-m-d', $request->id_date)->format('d/m/Y')
                ]);
            } catch (\Exception $e) {}
        }

        $cccdFront = $request->input('cccd_front');
        $cccdBack = $request->input('cccd_back');

        $localData = [
            'dob' => $request->dob,
            'pob' => $request->pob,
            'id_number' => $request->id_number,
            'id_date' => $request->id_date,
            'id_place' => $request->id_place,
            'permanent_address' => $request->permanent_address,
        ];

        if ($cccdFront) {
            $localData['cccd_front'] = $cccdFront;
        }
        if ($cccdBack) {
            $localData['cccd_back'] = $cccdBack;
        }

        // Save locally (will fail gracefully on read-only file systems)
        $this->profileService->updateCccd($user->id, $localData);
        $user->refresh();

        // Sync to NKS if user has a token
        if ($user->nks_token) {
            // Helper to get base64 string (either new upload or existing path)
            $helperGetBase64 = function ($inputBase64, $existingPath) {
                if ($inputBase64) {
                    return $inputBase64;
                }
                if (!$existingPath) {
                    return '';
                }
                if (str_starts_with($existingPath, 'data:image')) {
                    return $existingPath;
                }
                
                $url = $existingPath;
                if (str_starts_with($existingPath, 'users/')) {
                    $url = 'https://data.nks.vn/storage/' . $existingPath;
                }

                if (str_starts_with($url, 'http')) {
                    try {
                        $imgData = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url)->body();
                        return 'data:image/jpeg;base64,' . base64_encode($imgData);
                    } catch (\Exception $e) {
                        return '';
                    }
                }
                $fullPath = public_path($existingPath);
                if (file_exists($fullPath)) {
                    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                    return 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fullPath));
                }
                return '';
            };

            // Convert id_date from d/m/Y to Y-m-d format for NKS API compatibility
            $nksIdDate = $request->id_date;
            if (!empty($nksIdDate) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $nksIdDate)) {
                try {
                    $nksIdDate = \Carbon\Carbon::createFromFormat('d/m/Y', $nksIdDate)->format('Y-m-d');
                } catch (\Exception $e) {}
            }

            // Only update CCCD images on NKS if at least one new image was uploaded.
            // This prevents downloading/re-uploading existing images and avoids timeout failures on text-only edits.
            if ($cccdFront || $cccdBack) {
                $nksData = [
                    'id_number' => $request->id_number,
                    'id_date' => $nksIdDate,
                    'id_place' => $request->id_place,
                    'cccd_front' => $helperGetBase64($cccdFront, $user->cccd_front),
                    'cccd_back' => $helperGetBase64($cccdBack, $user->cccd_back),
                ];

                $this->nksAuthService->updateCccd($user->nks_token, $nksData);
            }

            // Sync other profile fields and CCCD fields to NKS as well (acts as a fail-safe if updateCccd fails due to image library issues on NKS)
            $nksInfoData = [
                'dob' => $request->dob,
                'pob' => $request->pob,
                'permanent_address' => $request->permanent_address,
                'id_number' => $request->id_number,
                'id_date' => $nksIdDate,
                'id_place' => $request->id_place,
            ];

            if (!empty($nksInfoData['dob']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $nksInfoData['dob'])) {
                try {
                    $nksInfoData['dob'] = \Carbon\Carbon::createFromFormat('d/m/Y', $nksInfoData['dob'])->format('Y-m-d');
                } catch (\Exception $e) {}
            }

            $this->nksAuthService->updateInfo($user->nks_token, $nksInfoData);

            // Fetch updated profile from NKS to sync URLs back to local DB
            $nksInfo = $this->nksAuthService->getUserInfo($user->nks_token);
            if ($nksInfo['success'] && !empty($nksInfo['user'])) {
                $syncFields = [];
                if (!empty($nksInfo['user']['cccd_front'])) {
                    $syncFields['cccd_front'] = $nksInfo['user']['cccd_front'];
                }
                if (!empty($nksInfo['user']['cccd_back'])) {
                    $syncFields['cccd_back'] = $nksInfo['user']['cccd_back'];
                }
                if (!empty($syncFields)) {
                    $user->update($syncFields);
                }
            }
        }

        return redirect()->route('profile.index', ['tab' => 'profile', 'subtab' => 'cccd'])->with('success', 'Thông tin xác thực CCCD đã được cập nhật thành công!');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password'
        ], [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống.',
            'new_password.required' => 'Mật khẩu mới không được để trống.',
            'new_password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'new_password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại.'
        ]);

        $this->profileService->changePassword($user->id, $request->current_password, $request->new_password);

        return redirect()->route('profile.index', ['tab' => 'profile', 'subtab' => 'password'])->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }

    /**
     * Scan CCCD image using FPT AI OCR API.
     */
    public function scanCccd(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'side' => 'required|string|in:front,back'
        ]);

        $base64Image = $request->input('image');
        $side = $request->input('side');

        // Extract raw base64 data
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        }

        $decodedImage = base64_decode($base64Image);
        if (!$decodedImage) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu ảnh không hợp lệ.'
            ], 400);
        }

        // Save to system temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'cccd_ocr_');
        file_put_contents($tempFile, $decodedImage);

        try {
            $apiKey = env('FPT_AI_API_KEY', 'jEg5yvUc8HLoUnesjGKVuBEyaZz1NRFa');
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'api-key' => $apiKey
            ])->attach(
                'image', file_get_contents($tempFile), 'cccd.jpg'
            )->post('https://api.fpt.ai/vision/idr/vnm');

            // Delete temp file
            @unlink($tempFile);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể kết nối đến API OCR của FPT.'
                ], 500);
            }

            $ocrData = $response->json();
            if (isset($ocrData['errorCode']) && $ocrData['errorCode'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi OCR từ FPT: ' . ($ocrData['errorMessage'] ?? 'Không xác định')
                ], 422);
            }

            $data = $ocrData['data'][0] ?? null;
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đọc được thông tin từ hình ảnh.'
                ], 422);
            }

            $result = [];
            if ($side === 'front') {
                if (!empty($data['id'])) {
                    $result['number'] = $data['id'];
                }
                if (!empty($data['dob'])) {
                    $result['dob'] = $this->formatOcrDate($data['dob']);
                }
                if (!empty($data['home'])) {
                    $result['pob'] = $data['home'];
                }
                if (!empty($data['address'])) {
                    $result['permanent_address'] = $data['address'];
                }
            } else {
                if (!empty($data['issue_date'])) {
                    $result['issue_date'] = $this->formatOcrDate($data['issue_date']);
                }
                if (!empty($data['issue_loc'])) {
                    $result['issue_place'] = $data['issue_loc'];
                }
                if (!empty($data['address'])) {
                    $result['permanent_address'] = $data['address'];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            @unlink($tempFile);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình quét OCR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to format DD/MM/YYYY to YYYY-MM-DD.
     */
    private function formatOcrDate($dateStr)
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            $dateStr = str_replace(' ', '', $dateStr);
            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return $dateStr;
        }
    }

    /**
     * Register tenant user as owner.
     */
    public function registerOwner(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Make sure only tenant users can register
        if ($user->role !== 'tenant') {
            return redirect()->route('profile.index')->withErrors(['role' => 'Chỉ tài khoản khách hàng mới có thể đăng ký làm chủ nhà.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'company.max' => 'Tên công ty không được vượt quá 255 ký tự.',
        ]);

        $user->update([
            'role' => 'owner',
            'name' => $request->name,
            'phone' => $request->phone,
            'company' => $request->company,
        ]);

        // Sync name and phone to NKS if user has a token
        if ($user->nks_token) {
            $this->nksAuthService->updateInfo($user->nks_token, [
                'name' => $request->name,
                'phone' => $request->phone
            ]);
        }

        return redirect()->route('profile.index', ['tab' => 'profile'])->with('success', 'Đăng ký làm chủ nhà thành công! Chào mừng đối tác mới.');
    }
}

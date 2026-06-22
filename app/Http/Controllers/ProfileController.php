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
                'appointments' => [],
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
            'firstname' => 'nullable|string|max:100',
            'lastname' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|integer',
            'dob' => 'nullable|string|max:50',
            'pob' => 'nullable|string|max:255',
            'add_street' => 'nullable|string|max:255',
            'add_ward' => 'nullable|string|max:100',
            'add_district' => 'nullable|string|max:100',
            'add_province' => 'nullable|string|max:100',
            'zalo_id' => 'nullable|string|max:100',
            'zalo_key' => 'nullable|string|max:100',
            'intro' => 'nullable|string|max:1000',
            'website' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng bởi thành viên khác.',
        ]);

        $updateData = $request->only([
            'name', 'firstname', 'lastname', 'phone', 'email', 'gender', 'dob', 'pob',
            'add_street', 'add_ward', 'add_district', 'add_province', 'zalo_id', 'zalo_key', 'intro', 'website'
        ]);

        // Update fields locally
        $this->profileService->updateProfile($user->id, $updateData);

        // Sync to NKS if user has a token
        if ($user->nks_token) {
            $this->nksAuthService->updateInfo($user->nks_token, $updateData);
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
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh đại diện.',
            'avatar.image' => 'Ảnh đại diện phải là định dạng hình ảnh.',
            'avatar.mimes' => 'Hỗ trợ các định dạng ảnh: jpeg, png, jpg, gif.',
            'avatar.max' => 'Dung lượng ảnh tối đa là 2MB.'
        ]);

        // Handle Avatar upload locally
        $avatarPath = $this->profileService->updateAvatar($user->id, $request->file('avatar'));

        // Sync to NKS if user has a token
        if ($user->nks_token) {
            $this->nksAuthService->updateAvatar($user->nks_token, $request->file('avatar'));
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
            'cccd_front' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'cccd_back' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ], [
            'dob.required' => 'Ngày sinh không được để trống.',
            'pob.required' => 'Quê quán không được để trống.',
            'id_number.required' => 'Số CCCD không được để trống.',
            'id_date.required' => 'Ngày cấp không được để trống.',
            'id_place.required' => 'Nơi cấp không được để trống.',
            'permanent_address.required' => 'Nơi thường trú không được để trống.',
            'cccd_front.image' => 'Mặt trước CCCD phải là định dạng hình ảnh.',
            'cccd_front.mimes' => 'Hỗ trợ các định dạng ảnh: jpeg, png, jpg.',
            'cccd_front.max' => 'Dung lượng ảnh mặt trước tối đa là 3MB.',
            'cccd_back.image' => 'Mặt sau CCCD phải là định dạng hình ảnh.',
            'cccd_back.mimes' => 'Hỗ trợ các định dạng ảnh: jpeg, png, jpg.',
            'cccd_back.max' => 'Dung lượng ảnh mặt sau tối đa là 3MB.',
        ]);

        $cccdFrontPath = null;
        $cccdBackPath = null;

        // Local CCCD uploads
        if ($request->hasFile('cccd_front')) {
            $file = $request->file('cccd_front');
            $filename = 'cccd_front_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cccd'), $filename);
            $cccdFrontPath = 'uploads/cccd/' . $filename;
        }

        if ($request->hasFile('cccd_back')) {
            $file = $request->file('cccd_back');
            $filename = 'cccd_back_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cccd'), $filename);
            $cccdBackPath = 'uploads/cccd/' . $filename;
        }

        $localData = [
            'dob' => $request->dob,
            'pob' => $request->pob,
            'id_number' => $request->id_number,
            'id_date' => $request->id_date,
            'id_place' => $request->id_place,
            'permanent_address' => $request->permanent_address,
        ];

        if ($cccdFrontPath) {
            $localData['cccd_front'] = $cccdFrontPath;
        }
        if ($cccdBackPath) {
            $localData['cccd_back'] = $cccdBackPath;
        }

        // Save locally
        $this->profileService->updateCccd($user->id, $localData);

        // Sync to NKS if user has a token
        if ($user->nks_token) {
            $nksData = [
                'dob' => $request->dob,
                'pob' => $request->pob,
                'id_number' => $request->id_number,
                'id_date' => $request->id_date,
                'id_place' => $request->id_place,
                'permanent_address' => $request->permanent_address,
            ];
            
            if ($request->hasFile('cccd_front')) {
                $nksData['cccd_front'] = $request->file('cccd_front');
            }
            if ($request->hasFile('cccd_back')) {
                $nksData['cccd_back'] = $request->file('cccd_back');
            }

            $this->nksAuthService->updateCccd($user->nks_token, $nksData);
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
}

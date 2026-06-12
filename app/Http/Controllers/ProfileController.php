<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use App\Services\WishlistService;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected ProfileService $profileService;
    protected WishlistService $wishlistService;
    protected AppointmentService $appointmentService;

    public function __construct(
        ProfileService $profileService,
        WishlistService $wishlistService,
        AppointmentService $appointmentService
    ) {
        $this->profileService = $profileService;
        $this->wishlistService = $wishlistService;
        $this->appointmentService = $appointmentService;
    }

    /**
     * Display member profile / dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Handle admin profile view
        if ($user->role === 'admin') {
            return view('profile', [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0077bb&color=fff',
                    'role' => 'Quản trị viên',
                    'join_date' => $user->created_at ? $user->created_at->format('d/m/Y') : '06/01/2015'
                ],
                'stats' => null,
                'properties' => collect(),
                'appointments' => collect()
            ]);
        }

        $favorites = $this->wishlistService->getUserFavorites($user->id);
        
        // Check user role
        if ($user->role === 'owner') {
            // Owner stats
            $totalProperties = $user->properties()->count();
            $totalViews = $user->properties()->sum('views');
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
            if (request('tab') === 'edit_property' && request('property_id')) {
                $editProperty = \App\Models\Property::find(request('property_id'));
                if ($editProperty) {
                    abort_if($editProperty->agent_id !== $user->id, 403, 'Bạn không có quyền chỉnh sửa tin đăng này.');
                }
            }
                
            return view('profile', [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0077bb&color=fff',
                    'role' => 'Chủ nhà / Môi giới',
                    'join_date' => $user->created_at ? $user->created_at->format('d/m/Y') : '06/01/2015'
                ],
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
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0077bb&color=fff',
                    'role' => $user->role === 'admin' ? 'Quản trị viên' : 'Thành viên thuê nhà',
                    'join_date' => $user->created_at ? $user->created_at->format('d/m/Y') : '06/01/2015'
                ],
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
     * Update user details and avatar.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng bởi thành viên khác.',
            'avatar.image' => 'Ảnh đại diện phải là định dạng hình ảnh.',
            'avatar.mimes' => 'Hỗ trợ các định dạng ảnh: jpeg, png, jpg, gif.',
            'avatar.max' => 'Dung lượng ảnh tối đa là 2MB.'
        ]);

        // Update fields
        $this->profileService->updateProfile($user->id, $request->only('name', 'email', 'phone'));

        // Handle Avatar upload
        if ($request->hasFile('avatar')) {
            $this->profileService->updateAvatar($user->id, $request->file('avatar'));
        }

        return redirect()->route('profile.index')->with('success', 'Hồ sơ cá nhân đã được cập nhật thành công!');
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

        return redirect()->route('profile.index')->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }
}

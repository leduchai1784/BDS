<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NksAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected NksAuthService $nksAuthService;

    public function __construct(NksAuthService $nksAuthService)
    {
        $this->nksAuthService = $nksAuthService;
    }

    /**
     * Display login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     * Tries NKS API first, falls back to local auth (for admin accounts).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Vui lòng nhập địa chỉ email.',
            'email.email'       => 'Định dạng email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->has('remember');

        // ── Step 1: Try NKS API login ──────────────────────────────────────
        $nksResult = $this->nksAuthService->login($credentials['email'], $credentials['password']);

        if ($nksResult['success']) {
            $nksUser  = $nksResult['user'];
            $nksToken = $nksResult['token'];

            // Map NKS data → local fields
            $localData = $this->nksAuthService->mapNksUserToLocal($nksUser, $nksToken);

            // Preserve existing role; default to 'tenant' for new users
            $existingUser = User::where('email', $nksUser['email'])->first();
            $role = $existingUser ? $existingUser->role : 'tenant';

            // Find or create local user by email
            $user = User::updateOrCreate(
                ['email' => $nksUser['email']],
                array_merge($localData, [
                    'role'     => $role,
                    'status'   => 'active',
                    'password' => Hash::make($credentials['password']),
                ])
            );

            // Always refresh token & info
            $user->nks_token   = $nksToken;
            $user->nks_user_id = (string) ($nksUser['id'] ?? '');
            $user->name        = $localData['name'];
            if (!empty($localData['phone'])) {
                $user->phone = $localData['phone'];
            }
            if (!empty($localData['avatar'])) {
                $user->avatar = $localData['avatar'];
            }
            $user->save();

            // Check if account is locked
            if ($user->status === 'locked') {
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ])->onlyInput('email');
            }

            // Merge guest wishlist
            $cookieData = $request->cookie('guest_wishlist');
            if ($cookieData) {
                app(\App\Services\WishlistService::class)->mergeGuestWishlist($user->id, $cookieData);
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('guest_wishlist'));
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->intended('/profile');
        }

        // ── Step 2: Fallback — local auth (for admin accounts) ─────────────
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status === 'locked') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ])->onlyInput('email');
            }

            // Merge guest wishlist
            $cookieData = $request->cookie('guest_wishlist');
            if ($cookieData) {
                app(\App\Services\WishlistService::class)->mergeGuestWishlist(Auth::id(), $cookieData);
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('guest_wishlist'));
            }

            $request->session()->regenerate();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->intended('/profile');
        }

        // ── Step 3: Both failed ────────────────────────────────────────────
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại email và mật khẩu.',
        ])->onlyInput('email');
    }

    /**
     * Display registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|string|in:tenant,owner',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Định dạng email không hợp lệ.',
            'email.unique'       => 'Email này đã được sử dụng.',
            'role.required'      => 'Vui lòng chọn loại tài khoản.',
            'role.in'            => 'Loại tài khoản không hợp lệ.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có độ dài tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

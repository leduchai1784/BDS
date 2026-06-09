<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by keyword (name, email, phone)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user details.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Fetch properties posted by this user if they are an owner
        $properties = [];
        if ($user->role === 'owner') {
            $properties = \App\Models\Property::where('agent_id', $user->id)
                ->with('category')
                ->latest()
                ->get();
        }

        // Fetch appointments scheduled by this user if they are a tenant
        $appointments = [];
        if ($user->role === 'tenant') {
            $appointments = \App\Models\Appointment::where('user_id', $user->id)
                ->with('property')
                ->latest()
                ->get();
        }

        return view('admin.users.show', compact('user', 'properties', 'appointments'));
    }

    /**
     * Toggle status (Lock/Unlock) of the user.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent locking oneself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        // Toggle status
        if ($user->status === 'locked') {
            $user->status = 'active';
            $message = 'Mở khóa tài khoản thành công!';
        } else {
            $user->status = 'locked';
            $message = 'Khóa tài khoản thành công!';
        }

        $user->save();

        return back()->with('success', $message);
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting oneself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa tài khoản thành viên thành công!');
    }
}

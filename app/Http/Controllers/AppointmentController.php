<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Book an appointment to view a property.
     */
    public function book(Request $request)
    {
        $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'message' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'date.required' => 'Vui lòng chọn ngày xem nhà.',
            'date.after_or_equal' => 'Ngày xem nhà phải từ hôm nay trở đi.',
            'time.required' => 'Vui lòng chọn khung giờ xem nhà.'
        ]);

        $property = Property::findOrFail($request->property_id);
        if (Auth::check() && Auth::id() === $property->owner_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể tự đặt lịch xem nhà trên tin đăng của chính mình.'
                ], 403);
            }
            return redirect()->back()->withErrors(['property_id' => 'Bạn không thể tự đặt lịch xem nhà trên tin đăng của chính mình.']);
        }

        $data = $request->only('property_id', 'name', 'phone', 'email', 'date', 'time', 'message');
        
        // Associate with logged-in user if authenticated
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        $this->appointmentService->createAppointment($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu đặt lịch hẹn xem nhà đã được gửi thành công!'
            ]);
        }

        return redirect()->back()->with('success', 'Yêu cầu đặt lịch hẹn xem nhà đã được gửi thành công! Người đại diện sẽ liên hệ sớm nhất.');
    }

    /**
     * Cancel the specified appointment by the tenant.
     */
    public function cancel($id)
    {
        $this->appointmentService->cancelAppointment($id, Auth::id());

        return redirect()->route('profile.index', ['tab' => 'appointments'])
            ->with('success', 'Hủy lịch hẹn thành công!');
    }
}

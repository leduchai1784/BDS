<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
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
            'property_id' => 'required|integer|exists:properties,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'message' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'date.required' => 'Vui lòng chọn ngày xem nhà.',
            'date.after_or_equal' => 'Ngày xem nhà phải từ hôm nay trở đi.',
            'time.required' => 'Vui lòng chọn khung giờ xem nhà.'
        ]);

        $data = $request->only('property_id', 'name', 'phone', 'date', 'time', 'message');
        
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
        $appointment = \App\Models\Appointment::findOrFail($id);

        // Security Check: Only the owner of the appointment can cancel it
        abort_if($appointment->user_id !== Auth::id(), 403, 'Bạn không có quyền hủy lịch hẹn này.');

        // Update status to rejected
        $appointment->update([
            'status' => 'rejected',
            'reject_reason' => 'Khách thuê hủy lịch hẹn'
        ]);

        return redirect()->route('profile.index', ['tab' => 'appointments'])
            ->with('success', 'Hủy lịch hẹn thành công!');
    }
}

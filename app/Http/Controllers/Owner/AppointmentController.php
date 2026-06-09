<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Approve the specified appointment.
     */
    public function approve($id)
    {
        $appointment = Appointment::with('property')->findOrFail($id);

        // Security Check
        abort_if($appointment->property->agent_id !== Auth::id(), 403, 'Bạn không có quyền quản lý lịch hẹn này.');

        $appointment->update([
            'status' => 'approved',
            'reject_reason' => null // Clear if previously rejected
        ]);

        return redirect()->route('profile.index', ['tab' => 'appointments'])
            ->with('success', 'Xác nhận lịch hẹn xem nhà thành công!');
    }

    /**
     * Reject the specified appointment with a reason.
     */
    public function reject(Request $request, $id)
    {
        $appointment = Appointment::with('property')->findOrFail($id);

        // Security Check
        abort_if($appointment->property->agent_id !== Auth::id(), 403, 'Bạn không có quyền quản lý lịch hẹn này.');

        $request->validate([
            'reject_reason' => 'required|string|max:255'
        ], [
            'reject_reason.required' => 'Vui lòng cung cấp lý do từ chối lịch hẹn.'
        ]);

        $appointment->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason
        ]);

        return redirect()->route('profile.index', ['tab' => 'appointments'])
            ->with('success', 'Từ chối lịch hẹn xem nhà thành công!');
    }

    /**
     * Mark the specified appointment as completed (viewed house).
     */
    public function complete($id)
    {
        $appointment = Appointment::with('property')->findOrFail($id);

        // Security Check
        abort_if($appointment->property->agent_id !== Auth::id(), 403, 'Bạn không có quyền quản lý lịch hẹn này.');

        $appointment->update([
            'status' => 'completed'
        ]);

        return redirect()->route('profile.index', ['tab' => 'appointments'])
            ->with('success', 'Đã đánh dấu hoàn thành cuộc xem nhà!');
    }
}

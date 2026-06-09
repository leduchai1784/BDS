<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments.
     */
    public function index(Request $request)
    {
        $query = Appointment::query()->with(['user', 'property.agent']);

        // Search by keyword (tenant name, phone, property title)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('property', function ($qp) use ($search) {
                      $qp->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $appointments = $query->latest()->paginate(10)->withQueryString();

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Cancel the specified appointment.
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        $appointment->status = 'cancelled';
        $appointment->save();

        return back()->with('success', 'Đã hủy lịch hẹn xem nhà thành công!');
    }
}

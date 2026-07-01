<?php

namespace App\Services;

use App\Services\PropertyService;
use App\Models\Appointment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminAppointmentNotification;
use App\Mail\TenantAppointmentConfirmation;
use App\Mail\OwnerAppointmentNotification;
use App\Mail\AdminAppointmentCancellation;
use App\Mail\TenantAppointmentCancellation;
use App\Mail\OwnerAppointmentCancellation;

class AppointmentService
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Create a new viewing appointment request.
     */
    public function createAppointment(array $data): Appointment
    {
        // Validate property exists (handles both database and NKS API properties)
        $propertyData = $this->propertyService->getPropertyById($data['property_id']);

        $appointment = Appointment::create([
            'user_id' => $data['user_id'] ?? null,
            'property_id' => $data['property_id'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'date' => $data['date'],
            'time' => $data['time'],
            'message' => $data['message'] ?? null,
            'status' => 'pending' // pending, confirmed, cancelled
        ]);

        // Send Email Notifications
        try {
            // 1. Send to Khách hàng (tenant)
            Mail::to($appointment->email)->send(new TenantAppointmentConfirmation($appointment));

            // 2. Send to Chủ nhà (owner) - only if it is a local property
            if (\Illuminate\Support\Str::isUuid($data['property_id'])) {
                $property = Property::find($data['property_id']);
                if ($property && $property->owner && $property->owner->email) {
                    Mail::to($property->owner->email)->send(new OwnerAppointmentNotification($appointment));
                }
            }

            // 3. Send to Admin(s)
            $admins = User::where('role', 'admin')->pluck('email');
            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(new AdminAppointmentNotification($appointment));
            }
        } catch (\Exception $e) {
            // Log mail sending errors to prevent breaking the application flow
            \Illuminate\Support\Facades\Log::error('Error sending appointment booking emails: ' . $e->getMessage());
        }

        return $appointment;
    }

    /**
     * Cancel an appointment by the tenant.
     */
    public function cancelAppointment(int $id, int $userId): Appointment
    {
        $appointment = Appointment::with(['property.owner'])->findOrFail($id);

        // Security Check: Only the owner of the appointment can cancel it
        abort_if($appointment->user_id !== $userId, 403, 'Bạn không có quyền hủy lịch hẹn này.');

        // Update status
        $appointment->update([
            'status' => 'rejected',
            'reject_reason' => 'Khách thuê hủy lịch hẹn'
        ]);

        // Send Email Notifications for Cancellation
        try {
            // 1. Send to Khách hàng (tenant)
            Mail::to($appointment->email)->send(new TenantAppointmentCancellation($appointment));

            // 2. Send to Chủ nhà (owner)
            if ($appointment->property && $appointment->property->owner && $appointment->property->owner->email) {
                Mail::to($appointment->property->owner->email)->send(new OwnerAppointmentCancellation($appointment));
            }

            // 3. Send to Admin(s)
            $admins = User::where('role', 'admin')->pluck('email');
            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(new AdminAppointmentCancellation($appointment));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending appointment cancellation emails: ' . $e->getMessage());
        }

        return $appointment;
    }

    /**
     * Get all appointments for a specific user.
     */
    public function getUserAppointments(int $userId)
    {
        return Appointment::with(['property.agent'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}

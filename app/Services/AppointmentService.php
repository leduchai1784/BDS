<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Property;

class AppointmentService
{
    /**
     * Create a new viewing appointment request.
     */
    public function createAppointment(array $data): Appointment
    {
        // Validate property exists
        if (!\Illuminate\Support\Str::isUuid($data['property_id'])) {
            throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel(Property::class, [$data['property_id']]);
        }
        Property::findOrFail($data['property_id']);

        return Appointment::create([
            'user_id' => $data['user_id'] ?? null,
            'property_id' => $data['property_id'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'date' => $data['date'],
            'time' => $data['time'],
            'message' => $data['message'] ?? null,
            'status' => 'pending' // pending, confirmed, cancelled
        ]);
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

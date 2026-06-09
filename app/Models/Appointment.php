<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 
    'property_id', 
    'name', 
    'phone', 
    'date', 
    'time', 
    'message', 
    'status',
    'reject_reason'
])]
class Appointment extends Model
{
    /**
     * Get the user who scheduled the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property scheduled for viewing.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}

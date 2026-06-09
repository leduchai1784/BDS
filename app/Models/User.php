<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the properties posted by the user (as agent).
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    /**
     * Get the user's wishlist properties.
     */
    public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'wishlists', 'user_id', 'property_id')->withTimestamps();
    }

    /**
     * Get the user's viewing appointments.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the viewing appointments booked for this owner's properties.
     */
    public function ownerAppointments()
    {
        return $this->hasManyThrough(Appointment::class, Property::class, 'agent_id', 'property_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

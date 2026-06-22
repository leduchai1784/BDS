<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'firstname', 'lastname', 'email', 'password', 'phone', 'avatar', 'gender', 'dob', 'pob',
    'id_number', 'id_date', 'id_place', 'cccd_front', 'cccd_back', 'add_street', 'add_ward',
    'add_district', 'add_province', 'permanent_address', 'zalo_id', 'zalo_key', 'intro', 'website', 'role', 'status',
    'nks_user_id', 'nks_token'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the properties posted by the user (as owner).
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
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
        return $this->hasManyThrough(Appointment::class, Property::class, 'owner_id', 'property_id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'title', 
    'type', 
    'price', 
    'price_label', 
    'area', 
    'bedrooms', 
    'bathrooms', 
    'location', 
    'district', 
    'lat', 
    'lng', 
    'image', 
    'images', 
    'direction', 
    'furniture', 
    'legal', 
    'is_vip', 
    'is_new', 
    'agent_id', 
    'description'
])]
class Property extends Model
{
    use HasFactory;

    /**
     * Get the agent who posted this property.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the users who added this property to their wishlist.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists', 'property_id', 'user_id')->withTimestamps();
    }

    /**
     * Get the appointments scheduled for this property.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_vip' => 'boolean',
            'is_new' => 'boolean',
            'price' => 'integer',
            'area' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'lat' => 'double',
            'lng' => 'double',
        ];
    }
}

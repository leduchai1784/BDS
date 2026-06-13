<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'owner_id',
    'category_id',
    'title',
    'slug',
    'description',
    'price',
    'price_label',
    'area',
    'bedroom',
    'bathroom',
    'address',
    'ward',
    'district',
    'city',
    'latitude',
    'longitude',
    'phone',
    'zalo',
    'status',
    'views_count',
    'direction',
    'furniture',
    'legal',
    'is_vip',
    'is_new',
    'meta_title',
    'meta_description'
])]
class Property extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected static function booted()
    {
        static::saving(function ($property) {
            // Auto-generate slug
            if (empty($property->slug) || $property->isDirty('title')) {
                $property->slug = Str::slug($property->title) . '-' . substr(uniqid(), -5);
            }
            
            // Auto-generate meta_title
            if (empty($property->meta_title)) {
                $property->meta_title = $property->title;
            }
            
            // Auto-generate meta_description
            if (empty($property->meta_description)) {
                $property->meta_description = Str::limit(strip_tags($property->description), 160);
            }
        });
    }

    /**
     * Get the category of the property.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the owner who posted this property.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the agent (alias for owner) who posted this property.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the property images.
     */
    public function propertyImages()
    {
        return $this->hasMany(PropertyImage::class);
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
     * Compatibility Accessors
     */
    public function getImageAttribute()
    {
        $primary = $this->propertyImages()->where('is_primary', true)->first();
        return $primary ? $primary->image_path : 'images/default-property.png';
    }

    public function getImagesAttribute()
    {
        return $this->propertyImages()->where('is_primary', false)->pluck('image_path')->toArray();
    }

    public function getAgentAttribute()
    {
        return $this->owner;
    }

    public function getAgentIdAttribute()
    {
        return $this->owner_id;
    }

    public function getLatAttribute()
    {
        return $this->latitude;
    }

    public function getLngAttribute()
    {
        return $this->longitude;
    }

    public function getLocationAttribute()
    {
        return $this->address;
    }

    public function getBedroomsAttribute()
    {
        return $this->bedroom;
    }

    public function getBathroomsAttribute()
    {
        return $this->bathroom;
    }

    public function getViewsAttribute()
    {
        return $this->views_count;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_vip' => 'boolean',
            'is_new' => 'boolean',
            'price' => 'integer',
            'area' => 'integer',
            'bedroom' => 'integer',
            'bathroom' => 'integer',
            'latitude' => 'double',
            'longitude' => 'double',
            'views_count' => 'integer',
        ];
    }
}

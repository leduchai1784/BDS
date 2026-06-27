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
    'project_id',
    'transaction_type',
    'property_type',
    'province',
    'title',
    'slug',
    'description',
    'price',
    'price_label',
    'deposit',
    'lease_term',
    'frontage',
    'road_width',
    'floors',
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

            // Auto-fill province from city
            if (empty($property->province) || $property->isDirty('city')) {
                $property->province = $property->city;
            }

            // Auto-fill transaction_type from price label or value
            if (empty($property->transaction_type) || $property->isDirty('price') || $property->isDirty('price_label')) {
                $isRent = (isset($property->price_label) && stripos($property->price_label, 'tháng') !== false) || 
                          ($property->price <= 150000000); // threshold of 150M for rental
                $property->transaction_type = $isRent ? 'rent' : 'sale';
            }

            // Auto-fill property_type from category
            if (empty($property->property_type) || $property->isDirty('category_id')) {
                $category = $property->category;
                if ($category) {
                    $property->property_type = $category->slug === 'chung-cu' ? 'apartment' :
                        ($category->slug === 'nha-nguyen-can' ? 'house' :
                        ($category->slug === 'phong-tro' ? 'room' :
                        ($category->slug === 'dat' ? 'land' :
                        ($category->slug === 'mat-bang' ? 'premises' :
                        ($category->slug === 'van-phong' ? 'office' : 'warehouse')))));
                }
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
     * Get the project of the property.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
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
            'deposit' => 'integer',
            'frontage' => 'double',
            'road_width' => 'double',
            'floors' => 'integer',
            'area' => 'integer',
            'bedroom' => 'integer',
            'bathroom' => 'integer',
            'latitude' => 'double',
            'longitude' => 'double',
            'views_count' => 'integer',
        ];
    }

    /**
     * Filter properties dynamically using Eloquent query builder.
     */
    public function scopeFilter($query, array $filters)
    {
        // 1. Keyword search
        if (!empty($filters['keyword']) || !empty($filters['search'])) {
            $keyword = !empty($filters['keyword']) ? $filters['keyword'] : $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('address', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
                  ->orWhere('district', 'like', '%' . $keyword . '%')
                  ->orWhere('province', 'like', '%' . $keyword . '%');
            });
        }

        // 2. Transaction type
        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        } elseif (!empty($filters['purpose'])) {
            $query->where('transaction_type', $filters['purpose']);
        }

        // 3. Property type
        if (!empty($filters['property_type'])) {
            $types = is_array($filters['property_type']) ? $filters['property_type'] : [$filters['property_type']];
            $types = array_filter($types);
            if (!empty($types)) {
                $query->whereIn('property_type', $types);
            }
        } elseif (!empty($filters['type'])) {
            $types = is_array($filters['type']) ? $filters['type'] : [$filters['type']];
            $types = array_filter($types);
            if (!empty($types)) {
                $query->whereIn('property_type', $types);
            }
        }

        // 4. Province
        if (!empty($filters['province'])) {
            $province = $filters['province'];
            $query->where(function ($q) use ($province) {
                $cleanP = str_replace(['Thành phố ', 'Tỉnh '], '', $province);
                $q->where('province', 'like', '%' . $cleanP . '%');
            });
        } elseif (!empty($filters['city'])) {
            $city = $filters['city'];
            $query->where(function ($q) use ($city) {
                $cleanC = str_replace(['Thành phố ', 'Tỉnh '], '', $city);
                $q->where('province', 'like', '%' . $cleanC . '%');
            });
        }

        // 5. District
        if (!empty($filters['district'])) {
            $districts = is_array($filters['district']) ? $filters['district'] : [$filters['district']];
            $districts = array_filter($districts);
            if (!empty($districts)) {
                $query->where(function ($q) use ($districts) {
                    $q->whereIn('district', $districts);
                    foreach ($districts as $d) {
                        $cleanD = str_replace(['Quận ', 'Huyện ', 'Thị xã ', 'Thành phố '], '', $d);
                        $q->orWhere('address', 'like', '%' . $cleanD . '%')
                          ->orWhere('district', 'like', '%' . $cleanD . '%');
                    }
                });
            }
        }

        // 6. Ward
        if (!empty($filters['ward'])) {
            $ward = $filters['ward'];
            $query->where(function ($q) use ($ward) {
                $cleanW = str_replace(['Phường ', 'Xã ', 'Thị trấn '], '', $ward);
                $q->where('ward', 'like', '%' . $cleanW . '%')
                  ->orWhere('address', 'like', '%' . $cleanW . '%');
            });
        }

        // 7. Price
        if (!empty($filters['price'])) {
            $price = $filters['price'];
            if ($price === 'under_3') {
                $query->where('price', '<', 3000000);
            } elseif ($price === '3_5') {
                $query->whereBetween('price', [3000000, 5000000]);
            } elseif ($price === '5_10') {
                $query->whereBetween('price', [5000000, 10000000]);
            } elseif ($price === '10_20') {
                $query->whereBetween('price', [10000000, 20000000]);
            } elseif ($price === 'above_20') {
                $query->where('price', '>', 20000000);
            } elseif ($price === 'under_1b') {
                $query->where('price', '<', 1000000000);
            } elseif ($price === '1b_3b') {
                $query->whereBetween('price', [1000000000, 3000000000]);
            } elseif ($price === '3b_5b') {
                $query->whereBetween('price', [3000000000, 5000000000]);
            } elseif ($price === '5b_10b') {
                $query->whereBetween('price', [5000000000, 10000000000]);
            } elseif ($price === 'above_10b') {
                $query->where('price', '>', 10000000000);
            }
        }

        // 8. Area
        if (!empty($filters['area'])) {
            $area = $filters['area'];
            if ($area === 'under_30') {
                $query->where('area', '<', 30);
            } elseif ($area === '30_50') {
                $query->whereBetween('area', [30, 50]);
            } elseif ($area === '50_80') {
                $query->whereBetween('area', [50, 80]);
            } elseif ($area === '80_120') {
                $query->whereBetween('area', [80, 120]);
            } elseif ($area === 'above_120') {
                $query->where('area', '>', 120);
            }
        }

        // 9. Bedrooms
        if (!empty($filters['bedrooms'])) {
            $query->where('bedroom', '>=', intval($filters['bedrooms']));
        } elseif (!empty($filters['bedroom'])) {
            $query->where('bedroom', '>=', intval($filters['bedroom']));
        }

        // 10. Bathrooms
        if (!empty($filters['bathrooms'])) {
            $query->where('bathroom', '>=', intval($filters['bathrooms']));
        } elseif (!empty($filters['bathroom'])) {
            $query->where('bathroom', '>=', intval($filters['bathroom']));
        }

        // 11. Furniture
        if (!empty($filters['furniture'])) {
            $furniture = $filters['furniture'];
            if ($furniture === 'full') {
                $query->where(function ($q) {
                    $q->where('furniture', 'like', '%đầy đủ%')
                      ->orWhere('furniture', 'like', '%full%');
                });
            } elseif ($furniture === 'basic') {
                $query->where('furniture', 'like', '%cơ bản%');
            } elseif ($furniture === 'none') {
                $query->where(function ($q) {
                    $q->where('furniture', 'like', '%không%')
                      ->orWhere('furniture', 'like', '%trống%')
                      ->orWhereNull('furniture');
                });
            }
        }

        // 12. Approved status only (default filter)
        if (!isset($filters['ignore_status'])) {
            $query->where('status', 'approved');
        }

        return $query;
    }

    /**
     * Sort properties dynamically.
     */
    public function scopeSort($query, $sortBy)
    {
        switch ($sortBy) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'area_asc':
                return $query->orderBy('area', 'asc');
            case 'area_desc':
                return $query->orderBy('area', 'desc');
            case 'latest':
            default:
                return $query->orderBy('created_at', 'desc');
        }
    }
}

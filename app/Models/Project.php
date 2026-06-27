<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'city',
        'district',
        'price_range',
        'scale',
        'investor',
        'status',
        'images',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($project) {
            if (empty($project->slug) || $project->isDirty('title')) {
                $project->slug = Str::slug($project->title) . '-' . substr(uniqid(), -5);
            }
        });
    }

    /**
     * Get properties associated with this project.
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'project_id');
    }
}

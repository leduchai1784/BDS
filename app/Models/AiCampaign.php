<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiCampaign extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'owner_id',
        'property_id',
        'type',
        'title',
        'goal',
        'tone',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the owner who generated this campaign.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the property associated with this campaign.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}

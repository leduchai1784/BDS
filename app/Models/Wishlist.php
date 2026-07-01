<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'property_id'])]
class Wishlist extends Model
{
    protected $table = 'wishlists';

    /**
     * Get the user who saved the property.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the saved property details.
     */
    public function property()
    {
        $instance = $this->newRelatedInstance(Property::class);
        return new \App\Relations\SafeUuidBelongsTo(
            $instance->newQuery(), $this, 'property_id', 'id', 'property'
        );
    }
}

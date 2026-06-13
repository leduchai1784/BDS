<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Wishlist;

class WishlistService
{
    /**
     * Toggle a property's favorite status for a user.
     * Returns true if added, false if removed.
     */
    public function toggleFavorite(int $userId, string $propertyId): bool
    {
        $user = User::findOrFail($userId);
        
        // Check if property exists
        Property::findOrFail($propertyId);

        // Check if already in wishlist
        $exists = $user->favoriteProperties()->where('property_id', $propertyId)->exists();

        if ($exists) {
            $user->favoriteProperties()->detach($propertyId);
            return false; // Removed
        } else {
            $user->favoriteProperties()->attach($propertyId);
            return true; // Added
        }
    }

    /**
     * Check if a property is favorited by a user.
     */
    public function isFavorite(int $userId, string $propertyId): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        return $user->favoriteProperties()->where('property_id', $propertyId)->exists();
    }

    /**
     * Get all properties favorited by a user.
     */
    public function getUserFavorites(int $userId)
    {
        $user = User::findOrFail($userId);
        return $user->favoriteProperties()->with('agent')->latest()->get();
    }
}

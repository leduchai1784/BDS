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
        if (!\Illuminate\Support\Str::isUuid($propertyId)) {
            throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel(Property::class, [$propertyId]);
        }

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
    public function isFavorite(?int $userId, string $propertyId): bool
    {
        if ($userId) {
            if (\Illuminate\Support\Str::isUuid($propertyId)) {
                $user = User::find($userId);
                if ($user && $user->favoriteProperties()->where('property_id', $propertyId)->exists()) {
                    return true;
                }
            }
        }

        // Check guest_wishlist cookie
        $cookieData = request()->cookie('guest_wishlist');
        if ($cookieData) {
            $wishlist = json_decode($cookieData, true);
            if (is_array($wishlist)) {
                return in_array($propertyId, $wishlist);
            }
        }

        return false;
    }

    /**
     * Get all properties favorited by a user.
     */
    public function getUserFavorites(int $userId)
    {
        $user = User::findOrFail($userId);
        return $user->favoriteProperties()->with('agent')->latest()->get();
    }

    /**
     * Merge wishlist from guest cookie to database for authenticated user.
     */
    public function mergeGuestWishlist(int $userId, ?string $cookieData): void
    {
        if (!$cookieData) {
            return;
        }

        $wishlist = json_decode($cookieData, true);
        if (!is_array($wishlist)) {
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            return;
        }

        foreach ($wishlist as $propertyId) {
            if (\Illuminate\Support\Str::isUuid($propertyId) && Property::where('id', $propertyId)->exists()) {
                if (!$user->favoriteProperties()->where('property_id', $propertyId)->exists()) {
                    $user->favoriteProperties()->attach($propertyId);
                }
            }
        }
    }
}

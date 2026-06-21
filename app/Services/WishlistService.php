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
        
        $exists = Wishlist::where('user_id', $userId)->where('property_id', $propertyId)->exists();

        if ($exists) {
            Wishlist::where('user_id', $userId)->where('property_id', $propertyId)->delete();
            return false; // Removed
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
            ]);
            return true; // Added
        }
    }

    /**
     * Check if a property is favorited by a user.
     */
    public function isFavorite(?int $userId, string $propertyId): bool
    {
        if ($userId) {
            $exists = Wishlist::where('user_id', $userId)->where('property_id', $propertyId)->exists();
            if ($exists) {
                return true;
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
        $propertyIds = Wishlist::where('user_id', $userId)->orderBy('created_at', 'desc')->pluck('property_id')->toArray();
        if (empty($propertyIds)) {
            return collect();
        }

        $allProperties = app(\App\Services\PropertyService::class)->getAllProperties();
        
        $favorites = collect($allProperties)->filter(function ($property) use ($propertyIds) {
            return in_array((string)$property['id'], $propertyIds);
        });

        $sortedFavorites = $favorites->sortBy(function ($property) use ($propertyIds) {
            return array_search((string)$property['id'], $propertyIds);
        });

        return $sortedFavorites->values();
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
            $exists = Wishlist::where('user_id', $userId)->where('property_id', $propertyId)->exists();
            if (!$exists) {
                Wishlist::create([
                    'user_id' => $userId,
                    'property_id' => $propertyId,
                ]);
            }
        }
    }
}

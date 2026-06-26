<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Toggle a property in the user's wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'property_id' => 'required|string'
        ]);

        $userId = Auth::id();
        $propertyId = $request->property_id;

        if ($userId) {
            $isAdded = $this->wishlistService->toggleFavorite($userId, $propertyId);

            return response()->json([
                'success' => true,
                'is_favorite' => $isAdded,
                'message' => $isAdded ? 'Đã thêm vào danh sách yêu thích!' : 'Đã bỏ khỏi danh sách yêu thích!'
            ]);
        } else {
            // Save to guest_wishlist cookie
            $cookieData = $request->cookie('guest_wishlist');
            $wishlist = $cookieData ? json_decode($cookieData, true) : [];
            if (!is_array($wishlist)) {
                $wishlist = [];
            }

            if (in_array($propertyId, $wishlist)) {
                $wishlist = array_values(array_diff($wishlist, [$propertyId]));
                $isAdded = false;
            } else {
                $wishlist[] = $propertyId;
                $isAdded = true;
            }

            $cookie = cookie('guest_wishlist', json_encode($wishlist), 43200); // 30 days

            return response()->json([
                'success' => true,
                'is_favorite' => $isAdded,
                'message' => $isAdded ? 'Đã thêm vào danh sách yêu thích!' : 'Đã bỏ khỏi danh sách yêu thích!'
            ])->withCookie($cookie);
        }
    }
}

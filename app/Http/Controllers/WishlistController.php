<?php

namespace App\Http\Controllers;

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
            'property_id' => 'required|integer|exists:properties,id'
        ]);

        $userId = Auth::id();
        $propertyId = $request->property_id;

        $isAdded = $this->wishlistService->toggleFavorite($userId, $propertyId);

        return response()->json([
            'success' => true,
            'is_favorite' => $isAdded,
            'message' => $isAdded ? 'Đã thêm vào danh sách yêu thích!' : 'Đã bỏ khỏi danh sách yêu thích!'
        ]);
    }
}

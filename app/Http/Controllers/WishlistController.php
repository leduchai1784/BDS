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
     * Display the wishlist page.
     */
    public function index()
    {
        $properties = collect();
        if (Auth::check()) {
            $properties = $this->wishlistService->getUserFavorites(Auth::id());
        }

        return view('wishlist', [
            'properties' => $properties
        ]);
    }

    /**
     * Render property card HTML for guest wishlist.
     */
    public function renderCards(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['html' => '']);
        }

        // Filter valid IDs (UUIDs or numeric IDs) to prevent database query errors
        $validIds = array_filter($ids, function($id) {
            return is_numeric($id) || \Illuminate\Support\Str::isUuid($id) || (is_string($id) && preg_match('/^[a-f\d]{8}-(?:[a-f\d]{4}-){3}[a-f\d]{12}$/i', $id));
        });

        if (empty($validIds)) {
            return response()->json(['html' => '']);
        }

        $properties = \App\Models\Property::with('agent')
            ->whereIn('id', $validIds)
            ->get();

        $html = '';
        foreach ($properties as $property) {
            $html .= view('components.property-card', ['property' => $property])->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Sync guest local wishlist items with the authenticated database.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string'
        ]);

        $userId = Auth::id();
        $ids = $request->input('ids', []);
        $syncedCount = 0;

        foreach ($ids as $propertyId) {
            $added = $this->wishlistService->addFavorite($userId, $propertyId);
            if ($added) {
                $syncedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'synced_count' => $syncedCount,
            'message' => 'Đồng bộ danh sách yêu thích thành công!'
        ]);
    }

    /**
     * Toggle a property in the user's wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'property_id' => 'required|uuid|exists:properties,id'
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

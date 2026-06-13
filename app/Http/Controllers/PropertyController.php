<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;
    protected WishlistService $wishlistService;

    public function __construct(PropertyService $propertyService, WishlistService $wishlistService)
    {
        $this->propertyService = $propertyService;
        $this->wishlistService = $wishlistService;
    }

    /**
     * Display a listing of properties.
     */
    public function index(Request $request)
    {
        $properties = $this->propertyService->search($request->all(), 6);
        
        return view('listings', [
            'properties' => $properties
        ]);
    }

    /**
     * Display the specified property detail page.
     */
    public function show(string $id)
    {
        $property = $this->propertyService->getPropertyById($id);
        $allProperties = $this->propertyService->searchAllForMap(); // For showing related/other listings

        $isLiked = false;
        if (auth()->check()) {
            $isLiked = $this->wishlistService->isFavorite(auth()->id(), $id);
        }

        return view('detail', [
            'property' => $property,
            'properties' => $allProperties,
            'isLiked' => $isLiked
        ]);
    }

    /**
     * Display map search page.
     */
    public function map(Request $request)
    {
        $properties = $this->propertyService->searchAllForMap($request->all());

        return view('map', [
            'properties' => $properties
        ]);
    }
}

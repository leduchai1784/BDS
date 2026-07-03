<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Get provinces and districts from NKS API.
     */
    public function getProvinces()
    {
        $provinces = $this->propertyService->getNksProvinces();
        return response()->json($provinces);
    }

    /**
     * Get wards (flat list) from NKS API.
     */
    public function getWards()
    {
        $wards = $this->propertyService->getNksWards();
        
        // Filter out districts and provinces to keep only actual wards
        $filteredWards = array_values(array_filter($wards, function($item) {
            $title = $item['title'] ?? '';
            return stripos($title, 'Thị xã') === false 
                && stripos($title, 'Huyện') === false 
                && stripos($title, 'Quận') === false 
                && stripos($title, 'Thành phố') === false;
        }));

        return response()->json($filteredWards);
    }
}

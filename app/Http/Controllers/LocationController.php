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
     * Get provinces list from NKS API.
     * Returns [{id, title}] array.
     */
    public function getProvinces()
    {
        $provinces = $this->propertyService->getNksProvinces();

        // Return only id and title to keep payload small
        $simplified = array_map(fn($p) => [
            'id'    => $p['id'],
            'title' => $p['title'],
        ], $provinces);

        return response()->json($simplified);
    }

    /**
     * Get wards for a given province_id from NKS API.
     * Example: GET /api/locations/wards?province_id=1
     * Returns [{id, title}] array.
     */
    public function getWards(Request $request)
    {
        $provinceId = (int) $request->query('province_id', 0);

        if ($provinceId <= 0) {
            return response()->json([]);
        }

        $wards = $this->propertyService->getNksWardsByProvince($provinceId);

        $simplified = array_map(fn($w) => [
            'id'    => $w['id'],
            'title' => $w['title'],
        ], $wards);

        return response()->json($simplified);
    }
}

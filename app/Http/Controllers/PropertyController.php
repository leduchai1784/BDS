<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;
use App\Services\WishlistService;
use App\Http\Requests\SearchPropertyRequest;
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
    public function index(SearchPropertyRequest $request)
    {
        // Use all input (validated query string)
        $properties = $this->propertyService->search($request->validated(), 12);
        
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

    /**
     * Get autocomplete suggestions.
     */
    public function autocomplete(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen(trim($q)) < 2) {
            return response()->json([]);
        }

        $q = trim($q);
        $suggestions = [];

        // 1. Tỉnh/Thành phố & Quận/Huyện từ NKS Provinces API
        $nksProvinces = $this->propertyService->getNksProvinces();
        $matchedProvincesCount = 0;
        $matchedDistrictsCount = 0;
        $seenDistricts = [];

        foreach ($nksProvinces as $prov) {
            $provTitle = $prov['title'] ?? '';
            // Match Province
            if ($matchedProvincesCount < 3 && stripos($provTitle, $q) !== false) {
                $suggestions[] = [
                    'type' => 'city',
                    'label' => $provTitle,
                    'sublabel' => 'Tỉnh / Thành phố',
                    'value' => $provTitle
                ];
                $matchedProvincesCount++;
            }

            // Match Districts (under administratives)
            if (isset($prov['administratives']) && is_array($prov['administratives'])) {
                foreach ($prov['administratives'] as $dist) {
                    $distTitle = $dist['title'] ?? '';
                    if (stripos($distTitle, $q) !== false) {
                        $isWard = (stripos($distTitle, 'Phường ') === 0 || stripos($distTitle, 'Xã ') === 0 || stripos($distTitle, 'Thị trấn ') === 0);
                        if ($isWard) {
                            $suggestions[] = [
                                'type' => 'ward',
                                'label' => $distTitle,
                                'sublabel' => 'Phường / Xã (' . $provTitle . ')',
                                'value' => $distTitle
                            ];
                        } else {
                            if ($matchedDistrictsCount < 3) {
                                $key = $distTitle . '|' . $provTitle;
                                if (!in_array($key, $seenDistricts)) {
                                    $seenDistricts[] = $key;
                                    $suggestions[] = [
                                        'type' => 'district',
                                        'label' => $distTitle,
                                        'sublabel' => 'Quận / Huyện (' . $provTitle . ')',
                                        'value' => $distTitle
                                    ];
                                    $matchedDistrictsCount++;
                                }
                            }
                        }
                    }
                }
            }
        }

        // 2. Phường/Xã từ NKS Administratives (Wards) API
        $nksWards = $this->propertyService->getNksWards();
        $matchedWardsCount = 0;
        $seenWards = [];

        foreach ($nksWards as $ward) {
            $wardTitle = $ward['title'] ?? '';
            // Skip district/province level entries in administratives
            if (stripos($wardTitle, 'Thị xã') !== false || stripos($wardTitle, 'Huyện') !== false || stripos($wardTitle, 'Quận') !== false || stripos($wardTitle, 'Thành phố') !== false) {
                continue;
            }
            if ($matchedWardsCount < 3 && stripos($wardTitle, $q) !== false) {
                if (!in_array($wardTitle, $seenWards)) {
                    $seenWards[] = $wardTitle;
                    $suggestions[] = [
                        'type' => 'ward',
                        'label' => $wardTitle,
                        'sublabel' => 'Phường / Xã',
                        'value' => $wardTitle
                    ];
                    $matchedWardsCount++;
                }
            }
        }

        // 3. Đường / Địa chỉ cụ thể từ Database
        $addresses = \App\Models\Property::select('address')
            ->distinct()
            ->where('address', 'ILIKE', "%{$q}%")
            ->limit(3)
            ->get();
        foreach ($addresses as $addr) {
            $suggestions[] = [
                'type' => 'address',
                'label' => $addr->address,
                'sublabel' => 'Địa chỉ cụ thể',
                'value' => $addr->address
            ];
        }

        // 4. Tên bất động sản (Tiêu đề tin đăng) từ Database
        $titles = \App\Models\Property::select('id', 'title', 'address')
            ->where('title', 'ILIKE', "%{$q}%")
            ->limit(4)
            ->get();
        foreach ($titles as $t) {
            $suggestions[] = [
                'type' => 'property',
                'label' => $t->title,
                'sublabel' => 'Bất động sản (' . $t->address . ')',
                'value' => $t->title,
                'id' => $t->id
            ];
        }

        return response()->json($suggestions);
    }

    /**
     * Get property details as JSON for map modal.
     */
    public function getDetailsJson(string $id)
    {
        try {
            $property = $this->propertyService->getPropertyById($id);
            
            $isLiked = false;
            if (auth()->check()) {
                $isLiked = $this->wishlistService->isFavorite(auth()->id(), $id);
            }
            
            return response()->json([
                'success' => true,
                'property' => $property,
                'isLiked' => $isLiked,
                'current_user_id' => auth()->id()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bất động sản yêu cầu.'
            ], 404);
        }
    }
}

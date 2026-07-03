<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PropertyService
{
    public const DISTRICT_MAP = [
        'GL' => 'Gia Lâm',
        'BD' => 'Ba Đình',
        'TH' => 'Tây Hồ',
        'CG' => 'Cầu Giấy',
        'DD' => 'Đống Đa',
        'HK' => 'Hoàn Kiếm',
        'NTL' => 'Nam Từ Liêm',
        'HD' => 'Hà Đông',
        'Q1' => 'Quận 1',
        'Q3' => 'Quận 3',
        'Q10' => 'Quận 10',
        'BT' => 'Bình Thạnh',
        'TD' => 'Thủ Đức',
    ];

    /**
     * Cache TTL in seconds (1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * Get all properties from the NKS API or cache, with local mock fallback.
     */
    /**
     * Fetch only NKS API properties (cached / with local mock fallback).
     */
    public function getApiOnlyProperties(): array
    {
        return Cache::remember('nks_api_only_properties', $this->cacheTtl, function () {
            try {
                // Fetch from NKS API with timeout and no SSL verification for safety on local machines
                $response = Http::withoutVerifying()
                    ->timeout(10)
                    ->post('https://online.nks.vn/api/nks/rsitems', []);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['success']) && $json['success'] && !empty($json['data'])) {
                        return $this->transformApiData($json['data']);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch properties from NKS API: ' . $e->getMessage());
            }

            // Fallback to empty array if the API request fails (no mock data fallback)
            return [];
        });
    }

    /**
     * Get all properties from the NKS API or cache, with local mock fallback.
     */
    public function getAllProperties(bool $ignoreStatus = false): array
    {
        $dbProperties = [];
        try {
            $query = Property::with(['propertyImages', 'owner', 'category']);
            if (!$ignoreStatus) {
                $query->where('status', 'approved');
            }
            $dbProperties = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($property) {
                    return $this->transformDbProperty($property);
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load properties from DB in PropertyService: ' . $e->getMessage());
        }

        $apiProperties = $this->getApiOnlyProperties();

        return array_merge($dbProperties, $apiProperties);
    }

    /**
     * Fetch and cache provinces from NKS API.
     */
    public function getNksProvinces(): array
    {
        $cached = Cache::get('nks_provinces');
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->post('https://online.nks.vn/api/nks/provinces', []);

            if ($response->successful()) {
                $json = $response->json();
                if (isset($json['success']) && $json['success'] && !empty($json['data'])) {
                    Cache::put('nks_provinces', $json['data'], 86400);
                    return $json['data'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch provinces from NKS API: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Fetch wards/administratives for a specific province from NKS API.
     * Uses nks/administratives endpoint with province_id + slcBox=true param.
     */
    public function getNksWardsByProvince(int $provinceId): array
    {
        $cacheKey = 'nks_wards_province_' . $provinceId;
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->post('https://online.nks.vn/api/nks/administratives', [
                    'province_id' => $provinceId,
                    'slcBox'      => true,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                if (isset($json['success']) && $json['success'] && !empty($json['data'])) {
                    Cache::put($cacheKey, $json['data'], 86400);
                    return $json['data'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch wards for province ' . $provinceId . ' from NKS API: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Get VIP properties.
     */
    public function getFeaturedProperties(int $limit = 6): array
    {
        $properties = $this->getAllProperties();
        
        return collect($properties)
            ->where('is_vip', true)
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get latest properties.
     */
    public function getLatestProperties(int $limit = 3): array
    {
        $properties = $this->getAllProperties();
        
        return collect($properties)
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get system statistics for the homepage.
     */
    public function getSystemStats(): array
    {
        $properties = $this->getAllProperties();
        $propertiesCollection = collect($properties);
        
        // Dynamic counts from API data
        $totalProperties = $propertiesCollection->count();
        $totalLocations = $propertiesCollection->pluck('district')->unique()->count();
        
        // Agent names count
        $totalAgents = $propertiesCollection->pluck('agent.name')->unique()->count();

        // Safe database count for appointments or fallback
        try {
            $totalAppointments = Appointment::count() ?: 185;
        } catch (\Exception $e) {
            $totalAppointments = 185;
        }

        return [
            'total_properties' => $totalProperties ?: 100,
            'total_agents' => $totalAgents ?: 12,
            'total_locations' => $totalLocations ?: 6,
            'total_appointments' => $totalAppointments
        ];
    }

    /**
     * Get single property details by ID.
     */
    public function getPropertyById(string $id): array
    {
        $properties = $this->getAllProperties();
        $property = collect($properties)->first(function ($p) use ($id) {
            return (string)$p['id'] === (string)$id;
        });
        
        if (!$property) {
            abort(404, 'Không tìm thấy bất động sản yêu cầu.');
        }
        
        return $property;
    }

    /**
     * Search and filter properties with database query.
     */
    public function search(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        // 1. Get filtered DB properties
        $dbProperties = [];
        try {
            $dbProperties = $this->buildSearchQuery($filters)->get()->map(function ($property) {
                return $this->transformDbProperty($property);
            })->toArray();
        } catch (\Exception $e) {
            Log::error('DB search error in PropertyService: ' . $e->getMessage());
        }

        // 2. Get filtered API properties
        $apiProperties = $this->getApiOnlyProperties();
        $filteredApi = $this->filterPropertiesInPhp($apiProperties, $filters);

        // 3. Merge
        $allMerged = array_merge($dbProperties, $filteredApi);

        // 4. Sort merged list (VIP first, then based on the sorting type)
        $sortBy = $filters['sort'] ?? 'latest';
        usort($allMerged, function ($a, $b) use ($sortBy) {
            $vipA = $a['is_vip'] ?? false;
            $vipB = $b['is_vip'] ?? false;
            if ($vipA !== $vipB) {
                return $vipB ? 1 : -1;
            }

            if ($sortBy === 'price_asc') {
                return ($a['price_raw'] ?? 0) <=> ($b['price_raw'] ?? 0);
            } elseif ($sortBy === 'price_desc') {
                return ($b['price_raw'] ?? 0) <=> ($a['price_raw'] ?? 0);
            } elseif ($sortBy === 'area_asc') {
                return ($a['area'] ?? 0) <=> ($b['area'] ?? 0);
            } elseif ($sortBy === 'area_desc') {
                return ($b['area'] ?? 0) <=> ($a['area'] ?? 0);
            } else {
                // latest (descending ID/date)
                return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
            }
        });

        // 5. Paginate manually
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $collection = collect($allMerged);
        $slice = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return $paginator;
    }

    /**
     * Search and return all matching properties without pagination (for Map).
     */
    public function searchAllForMap(array $filters = []): array
    {
        // 1. Get filtered DB properties
        $dbProperties = [];
        try {
            $dbProperties = $this->buildSearchQuery($filters)->get()->map(function ($property) {
                return $this->transformDbProperty($property);
            })->toArray();
        } catch (\Exception $e) {
            Log::error('DB searchAllForMap error in PropertyService: ' . $e->getMessage());
        }

        // 2. Get filtered API properties
        $apiProperties = $this->getApiOnlyProperties();
        $filteredApi = $this->filterPropertiesInPhp($apiProperties, $filters);

        // 3. Merge
        $allMerged = array_merge($dbProperties, $filteredApi);

        // 4. Sort (VIP first)
        usort($allMerged, function ($a, $b) {
            $vipA = $a['is_vip'] ?? false;
            $vipB = $b['is_vip'] ?? false;
            if ($vipA !== $vipB) {
                return $vipB ? 1 : -1;
            }
            return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
        });

        return $allMerged;
    }

    /**
     * Filter properties in memory using PHP.
     */
    private function filterPropertiesInPhp(array $properties, array $filters): array
    {
        return array_filter($properties, function ($p) use ($filters) {
            // 1. Keyword search
            if (!empty($filters['keyword']) || !empty($filters['search'])) {
                $keyword = !empty($filters['keyword']) ? $filters['keyword'] : $filters['search'];
                $keyword = trim($keyword);
                
                $match = false;
                $fields = ['title', 'city', 'district', 'ward', 'address', 'description'];
                foreach ($fields as $field) {
                    if (isset($p[$field]) && stripos($p[$field], $keyword) !== false) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) return false;
            }

            // 2. Transaction type (rent / sale)
            $purpose = !empty($filters['transaction_type']) ? $filters['transaction_type'] : (!empty($filters['purpose']) ? $filters['purpose'] : null);
            if (!empty($purpose) && isset($p['transaction_type']) && $p['transaction_type'] !== $purpose) {
                return false;
            }

            // 3. Property type
            $propType = !empty($filters['property_type']) ? $filters['property_type'] : (!empty($filters['type']) ? $filters['type'] : null);
            if (!empty($propType)) {
                $types = is_array($propType) ? $propType : [$propType];
                $types = array_filter($types);
                if (!empty($types) && isset($p['property_type']) && !in_array($p['property_type'], $types)) {
                    return false;
                }
            }

            // 4. Province / City
            $provinceFilter = !empty($filters['province']) ? $filters['province'] : (!empty($filters['city']) ? $filters['city'] : null);
            if (!empty($provinceFilter)) {
                $cleanP = str_replace(['Thành phố ', 'Tỉnh '], '', $provinceFilter);
                if (isset($p['city']) && stripos($p['city'], $cleanP) === false) {
                    return false;
                }
            }

            // 5. District
            if (!empty($filters['district'])) {
                $districts = is_array($filters['district']) ? $filters['district'] : [$filters['district']];
                $districts = array_filter($districts);
                if (!empty($districts)) {
                    $distMatch = false;
                    foreach ($districts as $d) {
                        $cleanD = str_replace(['Quận ', 'Huyện ', 'Thị xã ', 'Thành phố '], '', $d);
                        
                        $abbr = null;
                        foreach (self::DISTRICT_MAP as $key => $val) {
                            if (stripos($val, $cleanD) !== false || stripos($cleanD, $val) !== false) {
                                $abbr = $key;
                                break;
                            }
                        }

                        if (isset($p['district'])) {
                            if (stripos($p['district'], $cleanD) !== false) {
                                $distMatch = true;
                                break;
                            }
                            if ($abbr && stripos($p['district'], $abbr) !== false) {
                                $distMatch = true;
                                break;
                            }
                        }
                        if (isset($p['address']) && stripos($p['address'], $cleanD) !== false) {
                            $distMatch = true;
                            break;
                        }
                    }
                    if (!$distMatch) return false;
                }
            }

            // 6. Ward
            if (!empty($filters['ward'])) {
                $cleanW = str_replace(['Phường ', 'Xã ', 'Thị trấn '], '', $filters['ward']);
                $wardMatch = false;
                if (isset($p['ward']) && stripos($p['ward'], $cleanW) !== false) {
                    $wardMatch = true;
                }
                if (isset($p['address']) && stripos($p['address'], $cleanW) !== false) {
                    $wardMatch = true;
                }
                if (!$wardMatch) return false;
            }

            // 7. Price
            if (!empty($filters['price'])) {
                $priceFilter = $filters['price'];
                $priceRaw = $p['price_raw'] ?? 0;
                
                if ($priceFilter === 'under_3' && $priceRaw >= 3000000) return false;
                if ($priceFilter === '3_5' && ($priceRaw < 3000000 || $priceRaw > 5000000)) return false;
                if ($priceFilter === '5_10' && ($priceRaw < 5000000 || $priceRaw > 10000000)) return false;
                if ($priceFilter === '10_20' && ($priceRaw < 10000000 || $priceRaw > 20000000)) return false;
                if ($priceFilter === 'above_20' && $priceRaw <= 20000000) return false;
                if ($priceFilter === 'above_10' && $priceRaw <= 10000000) return false;
                if ($priceFilter === 'under_1b' && $priceRaw >= 1000000000) return false;
                if ($priceFilter === '1b_3b' && ($priceRaw < 1000000000 || $priceRaw > 3000000000)) return false;
                if ($priceFilter === '3b_5b' && ($priceRaw < 3000000000 || $priceRaw > 5000000000)) return false;
                if ($priceFilter === '5b_10b' && ($priceRaw < 5000000000 || $priceRaw > 10000000000)) return false;
                if ($priceFilter === 'above_10b' && $priceRaw <= 10000000000) return false;
            }

            // 8. Area
            if (!empty($filters['area'])) {
                $areaFilter = $filters['area'];
                $area = $p['area'] ?? 0;
                
                if ($areaFilter === 'under_30' && $area >= 30) return false;
                if ($areaFilter === '30_50' && ($area < 30 || $area > 50)) return false;
                if ($areaFilter === '50_80' && ($area < 50 || $area > 80)) return false;
                if ($areaFilter === '80_150' && ($area < 80 || $area > 150)) return false;
                if ($areaFilter === 'above_150' && $area <= 150) return false;
            }

            // 9. Bedrooms
            if (!empty($filters['bedrooms'])) {
                $bed = $filters['bedrooms'];
                $pBed = $p['bedrooms'] ?? 0;
                if ($bed === '5+' && $pBed < 5) return false;
                if ($bed !== '5+' && (int)$bed !== (int)$pBed) return false;
            }

            // 10. Bathrooms
            if (!empty($filters['bathrooms'])) {
                $bath = $filters['bathrooms'];
                $pBath = $p['bathrooms'] ?? 0;
                if ($bath === '5+' && $pBath < 5) return false;
                if ($bath !== '5+' && (int)$bath !== (int)$pBath) return false;
            }

            return true;
        });
    }

    /**
     * Private helper to build database search query using PostgreSQL.
     */
    private function buildSearchQuery(array $filters)
    {
        $query = Property::query()->with(['propertyImages', 'owner', 'category']);

        if (!isset($filters['ignore_status'])) {
            $query->where('status', 'approved');
        }

        // 1. Keyword search (fuzzy / partial substring matching using GIN trigram index)
        if (!empty($filters['keyword']) || !empty($filters['search'])) {
            $keyword = !empty($filters['keyword']) ? $filters['keyword'] : $filters['search'];
            $keyword = trim($keyword);
            
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'ILIKE', "%{$keyword}%")
                  ->orWhere('city', 'ILIKE', "%{$keyword}%")
                  ->orWhere('district', 'ILIKE', "%{$keyword}%")
                  ->orWhere('ward', 'ILIKE', "%{$keyword}%")
                  ->orWhere('address', 'ILIKE', "%{$keyword}%")
                  ->orWhere('description', 'ILIKE', "%{$keyword}%");
            });
        }

        // 2. Transaction type (rent / sale)
        $purpose = !empty($filters['transaction_type']) ? $filters['transaction_type'] : (!empty($filters['purpose']) ? $filters['purpose'] : null);
        if (!empty($purpose)) {
            $query->where('transaction_type', $purpose);
        }

        // 3. Property type (apartment, house, room, etc.)
        $propType = !empty($filters['property_type']) ? $filters['property_type'] : (!empty($filters['type']) ? $filters['type'] : null);
        if (!empty($propType)) {
            $types = is_array($propType) ? $propType : [$propType];
            $types = array_filter($types);
            if (!empty($types)) {
                $query->whereIn('property_type', $types);
            }
        }

        // 4. Province / City
        $provinceFilter = !empty($filters['province']) ? $filters['province'] : (!empty($filters['city']) ? $filters['city'] : null);
        if (!empty($provinceFilter)) {
            $cleanP = str_replace(['Thành phố ', 'Tỉnh '], '', $provinceFilter);
            $query->where('city', 'ILIKE', "%{$cleanP}%");
        }

        // 5. District
        if (!empty($filters['district'])) {
            $districts = is_array($filters['district']) ? $filters['district'] : [$filters['district']];
            $districts = array_filter($districts);
            if (!empty($districts)) {
                $query->where(function ($q) use ($districts) {
                    foreach ($districts as $index => $d) {
                        $cleanD = str_replace(['Quận ', 'Huyện ', 'Thị xã ', 'Thành phố '], '', $d);
                        
                        $abbr = null;
                        foreach (self::DISTRICT_MAP as $key => $val) {
                            if (stripos($val, $cleanD) !== false || stripos($cleanD, $val) !== false) {
                                $abbr = $key;
                                break;
                            }
                        }
                        
                        if ($index === 0) {
                            $q->where('district', 'ILIKE', "%{$cleanD}%")
                              ->orWhere('address', 'ILIKE', "%{$cleanD}%");
                            if ($abbr) {
                                $q->orWhere('district', $abbr);
                            }
                        } else {
                            $q->orWhere('district', 'ILIKE', "%{$cleanD}%")
                              ->orWhere('address', 'ILIKE', "%{$cleanD}%");
                            if ($abbr) {
                                $q->orWhere('district', $abbr);
                            }
                        }
                    }
                });
            }
        }

        // 6. Ward
        if (!empty($filters['ward'])) {
            $cleanW = str_replace(['Phường ', 'Xã ', 'Thị trấn '], '', $filters['ward']);
            $query->where(function ($q) use ($cleanW) {
                $q->where('ward', 'ILIKE', "%{$cleanW}%")
                  ->orWhere('address', 'ILIKE', "%{$cleanW}%");
            });
        }

        // 7. Price filters
        if (!empty($filters['price'])) {
            $priceFilter = $filters['price'];
            if ($priceFilter === 'under_3') {
                $query->where('price', '<', 3000000);
            } elseif ($priceFilter === '3_5') {
                $query->whereBetween('price', [3000000, 5000000]);
            } elseif ($priceFilter === '5_10') {
                $query->whereBetween('price', [5000000, 10000000]);
            } elseif ($priceFilter === '10_20') {
                $query->whereBetween('price', [10000000, 20000000]);
            } elseif ($priceFilter === 'above_20') {
                $query->where('price', '>', 20000000);
            } elseif ($priceFilter === 'above_10') {
                $query->where('price', '>', 10000000);
            } elseif ($priceFilter === 'under_1b') {
                $query->where('price', '<', 1000000000);
            } elseif ($priceFilter === '1b_3b') {
                $query->whereBetween('price', [1000000000, 3000000000]);
            } elseif ($priceFilter === '3b_5b') {
                $query->whereBetween('price', [3000000000, 5000000000]);
            } elseif ($priceFilter === '5b_10b') {
                $query->whereBetween('price', [5000000000, 10000000000]);
            } elseif ($priceFilter === 'above_10b') {
                $query->where('price', '>', 10000000000);
            }
        }

        // 8. Area filters
        if (!empty($filters['area'])) {
            $areaFilter = $filters['area'];
            if ($areaFilter === 'under_30') {
                $query->where('area', '<', 30);
            } elseif ($areaFilter === '30_50') {
                $query->whereBetween('area', [30, 50]);
            } elseif ($areaFilter === '50_80') {
                $query->whereBetween('area', [50, 80]);
            } elseif ($areaFilter === '80_120') {
                $query->whereBetween('area', [80, 120]);
            } elseif ($areaFilter === 'above_120') {
                $query->where('area', '>', 120);
            }
        }

        // 9. Bedrooms
        $bedroomsFilter = !empty($filters['bedrooms']) ? $filters['bedrooms'] : (!empty($filters['bedroom']) ? $filters['bedroom'] : null);
        if (!empty($bedroomsFilter)) {
            $query->where('bedroom', '>=', intval($bedroomsFilter));
        }

        // 10. Bathrooms
        $bathroomsFilter = !empty($filters['bathrooms']) ? $filters['bathrooms'] : (!empty($filters['bathroom']) ? $filters['bathroom'] : null);
        if (!empty($bathroomsFilter)) {
            $query->where('bathroom', '>=', intval($bathroomsFilter));
        }

        // 11. Furniture
        if (!empty($filters['furniture'])) {
            $query->where('furniture', 'ILIKE', "%{$filters['furniture']}%");
        }

        // 12. Direction
        if (!empty($filters['direction'])) {
            $query->where('direction', 'ILIKE', "%{$filters['direction']}%");
        }

        return $query;
    }

    /**
     * Transform raw API data into the exact format the frontend expects.
     */
    protected function transformApiData(array $data): array
    {
        $transformed = [];
        
        foreach ($data as $item) {
            $transformed[] = $this->transformItem($item);
        }
        
        return $transformed;
    }

    /**
     * Transform a single raw API item.
     */
    protected function transformItem(array $item): array
    {
        $id = isset($item['id']) ? (int)$item['id'] : rand(100, 999);
        $title = isset($item['title']) ? $item['title'] : 'Bất động sản BDS Rental';
        
        // Parse geolocation (lat,lng string)
        $lat = 10.7822; // Default to HCMC center
        $lng = 106.6704;
        if (!empty($item['geolocation'])) {
            $coords = explode(',', $item['geolocation']);
            if (count($coords) === 2) {
                $lat = floatval(trim($coords[0]));
                $lng = floatval(trim($coords[1]));
            }
        }
        
        // Map property type from rstype
        $rsType = isset($item['rstype']) ? trim($item['rstype']) : '';
        $type = 'Căn hộ chung cư'; // Fallback
        if (stripos($rsType, 'Căn hộ') !== false || stripos($rsType, 'Chung cư') !== false) {
            $type = 'Căn hộ chung cư';
        } elseif (stripos($rsType, 'Biệt thự') !== false || stripos($rsType, 'Villa') !== false) {
            $type = 'Biệt thự / Villa';
        } elseif (stripos($rsType, 'Văn phòng') !== false) {
            $type = 'Văn phòng cho thuê';
        } elseif (stripos($rsType, 'Nhà') !== false || stripos($rsType, 'Đất') !== false) {
            $type = 'Nhà nguyên căn';
        } elseif (stripos($rsType, 'Mặt bằng') !== false) {
            $type = 'Mặt bằng kinh doanh';
        } else {
            $type = !empty($rsType) ? $rsType : 'Bất động sản cho thuê';
        }
        
        // Parse Area
        $area = isset($item['total_area']) && $item['total_area'] > 0 ? (int)$item['total_area'] : 55;
        
        // Parse prices & format them
        $priceRaw = 0;
        $priceFormatted = 'Liên hệ';
        $priceLabel = 'Thỏa thuận';
        
        if (!empty($item['rentprice']) && $item['rentprice'] > 0) {
            $priceRaw = (int)$item['rentprice'];
        } elseif (!empty($item['price']) && $item['price'] > 0) {
            $priceRaw = (int)$item['price'];
        }

        // Map formatted prices from API if available, else calculate
        if (!empty($item['formatedRentPrice'])) {
            $cleanPrice = str_replace(' ', '', $item['formatedRentPrice']);
            $priceFormatted = $cleanPrice . '/tháng';
            if (stripos($priceFormatted, 'triệu') === false && is_numeric(filter_var($priceFormatted, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION))) {
                $priceFormatted .= ' triệu/tháng';
            }
            $priceLabel = str_ireplace('triệu', 'tr', $item['formatedRentPrice']);
        } elseif (!empty($item['formatedPrice'])) {
            $priceFormatted = $item['formatedPrice'];
            $priceLabel = str_ireplace('triệu', 'tr', $item['formatedPrice']);
        } else {
            // Manual formatting
            if ($priceRaw >= 1000000000) {
                $priceFormatted = number_format($priceRaw / 1000000000, 1, ',', '.') . ' tỷ/tháng';
                $priceLabel = number_format($priceRaw / 1000000000, 1, ',', '.') . ' tỷ';
                $priceLabel = str_replace(',0', '', $priceLabel);
                $priceFormatted = str_replace(',0', '', $priceFormatted);
            } elseif ($priceRaw >= 1000000) {
                $priceFormatted = number_format($priceRaw / 1000000, 1, ',', '.') . ' triệu/tháng';
                $priceLabel = number_format($priceRaw / 1000000, 1, ',', '.') . 'tr';
                $priceLabel = str_replace(',0', '', $priceLabel);
                $priceFormatted = str_replace(',0', '', $priceFormatted);
            } elseif ($priceRaw > 0) {
                $priceFormatted = number_format($priceRaw, 0, ',', '.') . ' đ/tháng';
                $priceLabel = number_format($priceRaw / 1000, 0, ',', '.') . 'k';
            }
        }
        
        // Clean location string
        $address = isset($item['address']) ? $item['address'] : '';
        $location = str_replace(', Việt Nam', '', $address);
        $location = str_replace(', Afghanistan', '', $location); // Clean test addresses
        if (empty($location)) {
            $location = isset($item['province']) ? $item['province'] : 'Thành phố Hồ Chí Minh';
        }
        
        // Map district code for filters
        $district = 'HCMC';
        $province = isset($item['province']) ? $item['province'] : '';
        if (stripos($province, 'Đồng Nai') !== false) {
            $district = 'ĐN';
        } elseif (stripos($location, 'Quận 1') !== false || stripos($location, 'Q.1') !== false) {
            $district = 'Q1';
        } elseif (stripos($location, 'Quận 3') !== false || stripos($location, 'Q.3') !== false) {
            $district = 'Q3';
        } elseif (stripos($location, 'Quận 10') !== false || stripos($location, 'Q.10') !== false) {
            $district = 'Q10';
        } elseif (stripos($location, 'Bình Thạnh') !== false) {
            $district = 'BT';
        } elseif (stripos($location, 'Thủ Đức') !== false) {
            $district = 'TD';
        } elseif (stripos($location, 'Gia Lâm') !== false) {
            $district = 'GL';
        } elseif (stripos($location, 'Cầu Giấy') !== false) {
            $district = 'CG';
        } elseif (stripos($location, 'Ba Đình') !== false) {
            $district = 'BD';
        } elseif (stripos($location, 'Tây Hồ') !== false) {
            $district = 'TH';
        } elseif (stripos($location, 'Đống Đa') !== false) {
            $district = 'DD';
        } elseif (stripos($location, 'Hoàn Kiếm') !== false) {
            $district = 'HK';
        }
        
        // Map feature image and gallery
        $featureImg = isset($item['featureimg']) ? $item['featureimg'] : 'images/apartment_1.png';
        $featureImg = str_replace('//storage', '/storage', $featureImg);
        
        $images = [$featureImg];
        
        // Parse gallery array
        if (!empty($item['gallery']) && is_array($item['gallery'])) {
            foreach ($item['gallery'] as $gal) {
                if (!empty($gal['image'])) {
                    $imgUrl = str_replace('//storage', '/storage', $gal['image']);
                    $images[] = $imgUrl;
                }
            }
        }
        
        // Parse images string
        if (!empty($item['images']) && is_string($item['images'])) {
            $parsedImages = json_decode($item['images'], true);
            if (is_array($parsedImages)) {
                foreach ($parsedImages as $pImg) {
                    if (stripos($pImg, 'http') === 0) {
                        $images[] = $pImg;
                    } else {
                        $images[] = 'https://data.nks.vn/storage/' . ltrim($pImg, '/');
                    }
                }
            }
        }
        
        // Ensure at least 4 images exist in details gallery for premium UI look
        if (count($images) < 4) {
            $fallbacks = ['images/apartment_1.png', 'images/apartment_2.png', 'images/apartment_3.png', 'images/hero_bg.png'];
            foreach ($fallbacks as $fb) {
                if (!in_array($fb, $images)) {
                    $images[] = $fb;
                }
            }
        }
        $images = array_unique($images);
        
        // Agent mapping
        $agentName = 'Môi giới BDS';
        $agentPhone = '0977.758.217';
        $agentAvatar = 'https://ui-avatars.com/api/?name=BDS&background=0077bb&color=fff';
        
        if (!empty($item['sale']) && is_array($item['sale'])) {
            $agentName = isset($item['sale']['name']) ? $item['sale']['name'] : $agentName;
            $agentPhone = isset($item['sale']['phone']) ? $item['sale']['phone'] : $agentPhone;
            $agentAvatar = isset($item['sale']['avatar']) ? $item['sale']['avatar'] : $agentAvatar;
            $agentAvatar = str_replace('//storage', '/storage', $agentAvatar);
        } elseif (!empty($item['phone'])) {
            $agentPhone = $item['phone'];
            if (!empty($item['email'])) {
                $agentName = strstr($item['email'], '@', true);
                $agentName = ucfirst($agentName);
            }
            $agentAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($agentName) . '&background=0077bb&color=fff';
        }
        
        // Dynamic premium description
        $direction = isset($item['direction']) ? $item['direction'] : 'Đông Nam';
        $description = "Căn nhà/căn hộ tọa lạc tại vị trí đắc địa tại khu vực " . $location . ". Bất động sản này sở hữu hướng " . $direction . " thông thoáng, đón gió tự nhiên. Diện tích rộng rãi " . $area . "m2 phù hợp làm không gian sinh sống hoặc văn phòng kinh doanh.\n\nTiện ích xung quanh đầy đủ, giao thông thuận tiện di chuyển nhanh chóng. Liên hệ ngay với môi giới phụ trách để biết thêm chi tiết và đặt lịch xem nhà trực tiếp.";
        
        // Determine transaction type (rent vs sale)
        $isRent = (stripos($priceFormatted, 'tháng') !== false) || 
                  (stripos($priceLabel, 'tháng') !== false) || 
                  ($priceRaw <= 150000000); // 150M threshold
        $transactionType = $isRent ? 'rent' : 'sale';

        return [
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'price' => $priceFormatted,
            'price_label' => $priceLabel,
            'price_raw' => $priceRaw,
            'transaction_type' => $transactionType,
            'area' => $area,
            'bedrooms' => isset($item['bed']) ? (int)$item['bed'] : 0,
            'bathrooms' => isset($item['bath']) ? (int)$item['bath'] : 0,
            'floors' => isset($item['floors']) ? (int)$item['floors'] : (isset($item['floor']) ? (int)$item['floor'] : 0),
            'property_type' => $this->resolvePropertyTypeCode($type),
            'location' => $location,
            'district' => $district,
            'lat' => $lat,
            'lng' => $lng,
            'image' => $featureImg,
            'images' => array_values($images),
            'direction' => $direction,
            'furniture' => !empty($item['road']['title']) ? "Mặt tiền đường " . $item['road']['title'] . ", nội thất đầy đủ" : "Đầy đủ nội thất",
            'legal' => "Sổ hồng chính chủ, hợp đồng cho thuê tối thiểu 1 năm",
            'is_vip' => ($id % 2 === 0),
            'is_new' => true,
            'owner_id' => null,
            'agent' => [
                'name' => $agentName,
                'phone' => $agentPhone,
                'avatar' => $agentAvatar,
                'zalo' => null
            ],
            'created_at' => 'Vừa cập nhật',
            'description' => $description
        ];
    }

    /**
     * Apply filter criteria to the collection.
     */
    protected function applyFilters($properties, array $filters)
    {
        // 1. Filter by Purpose (Rent vs Sale)
        if (!empty($filters['purpose'])) {
            $purpose = $filters['purpose'];
            $properties = $properties->filter(function ($item) use ($purpose) {
                $isRent = (isset($item['price_label']) && stripos($item['price_label'], 'tháng') !== false) || 
                          (isset($item['price']) && stripos($item['price'], 'tháng') !== false) ||
                          ($item['price_raw'] <= 150000000); // threshold of 150M for rental

                if ($purpose === 'rent') {
                    return $isRent;
                } elseif ($purpose === 'sale') {
                    return !$isRent;
                }
                return true;
            });
        }

        // 2. Filter by District
        if (!empty($filters['district'])) {
            $districts = is_array($filters['district']) ? $filters['district'] : [$filters['district']];
            $districts = array_filter($districts);
            if (!empty($districts)) {
                $properties = $properties->filter(function ($item) use ($districts) {
                    return in_array($item['district'], $districts);
                });
            }
        }

        // 3. Filter by Province/City (location parameter from Hero Search)
        if (!empty($filters['location'])) {
            $locCode = $filters['location'];
            $locName = '';
            if ($locCode === 'HN') {
                $locName = 'Hà Nội';
            } elseif ($locCode === 'HCM') {
                $locName = 'Hồ Chí Minh';
            } elseif ($locCode === 'DN') {
                $locName = 'Đà Nẵng';
            } elseif ($locCode === 'BD') {
                $locName = 'Bình Dương';
            } elseif ($locCode === 'DNai') {
                $locName = 'Đồng Nai';
            }

            if (!empty($locName)) {
                $properties = $properties->filter(function ($item) use ($locName) {
                    return (isset($item['location']) && stripos($item['location'], $locName) !== false) ||
                           (isset($item['address']) && stripos($item['address'], $locName) !== false) ||
                           (isset($item['province']) && stripos($item['province'], $locName) !== false);
                });
            }
        }

        // 4. Filter by Keyword (search text)
        if (!empty($filters['keyword']) || !empty($filters['search'])) {
            $keyword = !empty($filters['keyword']) ? $filters['keyword'] : $filters['search'];
            $properties = $properties->filter(function ($item) use ($keyword) {
                return (isset($item['title']) && stripos($item['title'], $keyword) !== false) ||
                       (isset($item['location']) && stripos($item['location'], $keyword) !== false) ||
                       (isset($item['description']) && stripos($item['description'], $keyword) !== false);
            });
        }

        // 5. Filter by Property Type
        if (!empty($filters['type'])) {
            $types = is_array($filters['type']) ? $filters['type'] : [$filters['type']];
            $types = array_filter($types);
            if (!empty($types)) {
                $properties = $properties->filter(function ($item) use ($types) {
                    foreach ($types as $type) {
                        if ($type === 'apartment') {
                            if (stripos($item['type'], 'Căn hộ') !== false || stripos($item['type'], 'Chung cư') !== false) {
                                return true;
                            }
                        } elseif ($type === 'house') {
                            if ((stripos($item['type'], 'Nhà') !== false && stripos($item['type'], 'nhà xưởng') === false && stripos($item['type'], 'nhà trọ') === false) || stripos($item['type'], 'Biệt thự') !== false || stripos($item['type'], 'Villa') !== false) {
                                return true;
                            }
                        } elseif ($type === 'room') {
                            if (stripos($item['type'], 'Phòng trọ') !== false || stripos($item['type'], 'Nhà trọ') !== false) {
                                return true;
                            }
                        } elseif ($type === 'land') {
                            if (stripos($item['type'], 'Đất') !== false) {
                                return true;
                            }
                        } elseif ($type === 'premises') {
                            if (stripos($item['type'], 'Mặt bằng') !== false) {
                                return true;
                            }
                        } elseif ($type === 'office') {
                            if (stripos($item['type'], 'Văn phòng') !== false) {
                                return true;
                            }
                        } elseif ($type === 'warehouse') {
                            if (stripos($item['type'], 'Kho') !== false || stripos($item['type'], 'Nhà xưởng') !== false || stripos($item['type'], 'Xưởng') !== false) {
                                return true;
                            }
                        } else {
                            if (stripos($item['type'], $type) !== false) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
            }
        }

        // 6. Filter by Price Range
        if (!empty($filters['price'])) {
            $price = $filters['price'];
            $properties = $properties->filter(function ($item) use ($price) {
                $priceRaw = $item['price_raw'];
                if ($price === 'under_5') {
                    return $priceRaw < 5000000;
                } elseif ($price === '5_10') {
                    return $priceRaw >= 5000000 && $priceRaw <= 10000000;
                } elseif ($price === '10_20') {
                    return $priceRaw >= 10000000 && $priceRaw <= 20000000;
                } elseif ($price === 'above_20') {
                    return $priceRaw > 20000000;
                }
                // map filters
                elseif ($price === 'under_10') {
                    return $priceRaw < 10000000;
                } elseif ($price === '10_25') {
                    return $priceRaw >= 10000000 && $priceRaw <= 25000000;
                } elseif ($price === 'above_25') {
                    return $priceRaw > 25000000;
                }
                // Sale price ranges (in Billions)
                elseif ($price === 'under_2b') {
                    return $priceRaw < 2000000000;
                } elseif ($price === '2b_5b') {
                    return $priceRaw >= 2000000000 && $priceRaw <= 5000000000;
                } elseif ($price === '5b_10b') {
                    return $priceRaw >= 5000000000 && $priceRaw <= 10000000000;
                } elseif ($price === 'above_10b') {
                    return $priceRaw > 10000000000;
                }
                return true;
            });
        }

        // 7. Filter by Area Range
        if (!empty($filters['area'])) {
            $area = $filters['area'];
            $properties = $properties->filter(function ($item) use ($area) {
                $areaVal = (int)$item['area'];
                if ($area === 'under_30') {
                    return $areaVal < 30;
                } elseif ($area === '30_50') {
                    return $areaVal >= 30 && $areaVal <= 50;
                } elseif ($area === '50_80') {
                    return $areaVal >= 50 && $areaVal <= 80;
                } elseif ($area === 'above_80') {
                    return $areaVal > 80;
                }
                return true;
            });
        }

        // 8. Filter by Bedrooms
        if (!empty($filters['bedrooms'])) {
            $bedrooms = $filters['bedrooms'];
            $properties = $properties->filter(function ($item) use ($bedrooms) {
                $itemBedrooms = isset($item['bedrooms']) ? (int)$item['bedrooms'] : 0;
                if ($bedrooms === '1') {
                    return $itemBedrooms === 1;
                } elseif ($bedrooms === '2') {
                    return $itemBedrooms === 2;
                } elseif ($bedrooms === '3') {
                    return $itemBedrooms >= 3;
                }
                return true;
            });
        }

        // 9. Filter by Bathrooms
        if (!empty($filters['bathrooms'])) {
            $bathrooms = $filters['bathrooms'];
            $properties = $properties->filter(function ($item) use ($bathrooms) {
                $itemBathrooms = isset($item['bathrooms']) ? (int)$item['bathrooms'] : 0;
                if ($bathrooms === '1') {
                    return $itemBathrooms === 1;
                } elseif ($bathrooms === '2') {
                    return $itemBathrooms >= 2;
                }
                return true;
            });
        }

        // 10. Filter by Direction
        if (!empty($filters['direction'])) {
            $direction = $filters['direction'];
            // Map direction value (English code) to Vietnamese text
            $dirMap = [
                'east' => 'Đông',
                'west' => 'Tây',
                'south' => 'Nam',
                'north' => 'Bắc',
                'southeast' => 'Đông Nam',
                'southwest' => 'Tây Nam',
                'northeast' => 'Đông Bắc',
                'northwest' => 'Tây Bắc',
            ];
            $dirText = isset($dirMap[$direction]) ? $dirMap[$direction] : $direction;
            $properties = $properties->filter(function ($item) use ($dirText) {
                return isset($item['direction']) && stripos($item['direction'], $dirText) !== false;
            });
        }

        return $properties;
    }


    /**
     * Apply sorting to the collection.
     */
    protected function applySorting($properties, string $sortBy)
    {
        switch ($sortBy) {
            case 'price_asc':
                return $properties->sortBy('price_raw');
            case 'price_desc':
                return $properties->sortByDesc('price_raw');
            case 'area_asc':
                return $properties->sortBy('area');
            case 'area_desc':
                return $properties->sortByDesc('area');
            case 'latest':
            default:
                return $properties->sortByDesc('id');
        }
    }



    /**
     * Transform database Property model into array structure.
     */
    protected function transformDbProperty($property): array
    {
        $primaryImg = $property->propertyImages()->where('is_primary', true)->first();
        $featureImg = $primaryImg ? $primaryImg->image_path : 'images/default-property.png';
        if ($featureImg !== 'images/default-property.png' && stripos($featureImg, 'http') !== 0 && stripos($featureImg, 'storage/') !== 0) {
            $featureImg = 'storage/' . $featureImg;
        }

        $galleryImages = [$featureImg];
        foreach ($property->propertyImages as $img) {
            $path = $img->image_path;
            if (stripos($path, 'http') !== 0 && stripos($path, 'storage/') !== 0) {
                $path = 'storage/' . $path;
            }
            $galleryImages[] = $path;
        }
        $galleryImages = array_values(array_unique($galleryImages));

        $priceRaw = $property->price;
        $priceFormatted = $property->price_label;
        if (empty($priceFormatted)) {
            $priceFormatted = $this->formatPriceLabel($priceRaw);
        }

        $type = $property->category ? $property->category->name : 'Căn hộ chung cư';

        $agentName = $property->owner ? $property->owner->name : 'Chủ nhà';
        $agentPhone = $property->phone ?: ($property->owner ? $property->owner->phone : '0977.758.217');
        $agentAvatar = $property->owner && $property->owner->avatar ? $property->owner->avatar : 'https://ui-avatars.com/api/?name=' . urlencode($agentName) . '&background=0077bb&color=fff';

        return [
            'id' => $property->id,
            'title' => $property->title,
            'type' => $type,
            'transaction_type' => $property->transaction_type,
            'property_type' => $property->property_type,
            'province' => $property->province,
            'ward' => $property->ward,
            'price' => $priceFormatted,
            'price_label' => $property->price_label ?: 'Liên hệ',
            'price_raw' => $priceRaw,
            'area' => $property->area,
            'bedrooms' => $property->bedroom,
            'bathrooms' => $property->bathroom,
            'floors' => $property->floors,
            'frontage' => $property->frontage,
            'road_width' => $property->road_width,
            'deposit' => $property->deposit,
            'lease_term' => $property->lease_term,
            'location' => $property->address,
            'district' => $property->district,
            'lat' => $property->latitude,
            'lng' => $property->longitude,
            'image' => $featureImg,
            'images' => $galleryImages,
            'direction' => $property->direction ?: 'Chưa cập nhật',
            'furniture' => $property->furniture ?: 'Chưa cập nhật',
            'legal' => $property->legal ?: 'Chưa cập nhật',
            'is_vip' => (bool)$property->is_vip,
            'is_new' => (bool)$property->is_new,
            'owner_id' => $property->owner_id,
            'agent' => [
                'name' => $agentName,
                'phone' => $agentPhone,
                'avatar' => $agentAvatar,
                'zalo' => $property->zalo
            ],
            'created_at' => $property->created_at ? $property->created_at->diffForHumans() : 'Vừa cập nhật',
            'description' => $property->description
        ];
    }

    /**
     * Format raw price into millions/billions/VND label.
     */
    protected function formatPriceLabel(int $price): string
    {
        if ($price >= 1000000000) {
            $value = $price / 1000000000;
            return round($value, 1) . ' tỷ/tháng';
        } elseif ($price >= 1000000) {
            $value = $price / 1000000;
            return round($value, 1) . ' triệu/tháng';
        }
        return number_format($price) . 'đ/tháng';
    }

    /**
     * Helper to filter a single transformed property item in-memory.
     */
    protected function filterItem(array $item, array $filters): bool
    {
        // 1. Keyword search
        if (!empty($filters['keyword']) || !empty($filters['search'])) {
            $keyword = !empty($filters['keyword']) ? $filters['keyword'] : $filters['search'];
            $keyword = mb_strtolower(trim($keyword), 'UTF-8');
            
            $title = mb_strtolower($item['title'] ?? '', 'UTF-8');
            $location = mb_strtolower($item['location'] ?? '', 'UTF-8');
            $description = mb_strtolower($item['description'] ?? '', 'UTF-8');
            $district = mb_strtolower($item['district'] ?? '', 'UTF-8');
            $province = mb_strtolower($item['province'] ?? '', 'UTF-8');
            
            if (strpos($title, $keyword) === false && 
                strpos($location, $keyword) === false && 
                strpos($description, $keyword) === false && 
                strpos($district, $keyword) === false && 
                strpos($province, $keyword) === false) {
                return false;
            }
        }

        // 2. Transaction type
        $purpose = !empty($filters['transaction_type']) ? $filters['transaction_type'] : (!empty($filters['purpose']) ? $filters['purpose'] : null);
        if (!empty($purpose)) {
            if (($item['transaction_type'] ?? '') !== $purpose) {
                return false;
            }
        }

        // 3. Property type
        $propType = !empty($filters['property_type']) ? $filters['property_type'] : (!empty($filters['type']) ? $filters['type'] : null);
        if (!empty($propType)) {
            $types = is_array($propType) ? $propType : [$propType];
            $types = array_filter($types);
            if (!empty($types)) {
                if (!in_array($item['property_type'] ?? '', $types)) {
                    return false;
                }
            }
        }

        // 4. Province
        $provinceFilter = !empty($filters['province']) ? $filters['province'] : (!empty($filters['city']) ? $filters['city'] : null);
        if (!empty($provinceFilter)) {
            $cleanP = mb_strtolower(str_replace(['Thành phố ', 'Tỉnh '], '', $provinceFilter), 'UTF-8');
            $provinceVal = mb_strtolower($item['province'] ?? '', 'UTF-8');
            if (strpos($provinceVal, $cleanP) === false) {
                return false;
            }
        }

        // 5. District
        if (!empty($filters['district'])) {
            $districts = is_array($filters['district']) ? $filters['district'] : [$filters['district']];
            $districts = array_filter($districts);
            if (!empty($districts)) {
                $matched = false;
                foreach ($districts as $d) {
                    $cleanD = mb_strtolower(str_replace(['Quận ', 'Huyện ', 'Thị xã ', 'Thành phố '], '', $d), 'UTF-8');
                    $districtVal = mb_strtolower($item['district'] ?? '', 'UTF-8');
                    $locationVal = mb_strtolower($item['location'] ?? '', 'UTF-8');
                    if (strpos($districtVal, $cleanD) !== false || strpos($locationVal, $cleanD) !== false) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return false;
                }
            }
        }

        // 6. Ward
        if (!empty($filters['ward'])) {
            $cleanW = mb_strtolower(str_replace(['Phường ', 'Xã ', 'Thị trấn '], '', $filters['ward']), 'UTF-8');
            $wardVal = mb_strtolower($item['ward'] ?? '', 'UTF-8');
            $locationVal = mb_strtolower($item['location'] ?? '', 'UTF-8');
            if (strpos($wardVal, $cleanW) === false && strpos($locationVal, $cleanW) === false) {
                return false;
            }
        }

        // 7. Price
        if (!empty($filters['price'])) {
            $priceFilter = $filters['price'];
            $priceRaw = $item['price_raw'] ?? 0;
            if ($priceFilter === 'under_3' && $priceRaw >= 3000000) return false;
            if ($priceFilter === '3_5' && ($priceRaw < 3000000 || $priceRaw > 5000000)) return false;
            if ($priceFilter === '5_10' && ($priceRaw < 5000000 || $priceRaw > 10000000)) return false;
            if ($priceFilter === '10_20' && ($priceRaw < 10000000 || $priceRaw > 20000000)) return false;
            if ($priceFilter === 'above_20' && $priceRaw <= 20000000) return false;
            if ($priceFilter === 'under_1b' && $priceRaw >= 1000000000) return false;
            if ($priceFilter === '1b_3b' && ($priceRaw < 1000000000 || $priceRaw > 3000000000)) return false;
            if ($priceFilter === '3b_5b' && ($priceRaw < 3000000000 || $priceRaw > 5000000000)) return false;
            if ($priceFilter === '5b_10b' && ($priceRaw < 5000000000 || $priceRaw > 10000000000)) return false;
            if ($priceFilter === 'above_10b' && $priceRaw <= 10000000000) return false;
        }

        // 8. Area
        if (!empty($filters['area'])) {
            $areaFilter = $filters['area'];
            $areaRaw = $item['area'] ?? 0;
            if ($areaFilter === 'under_30' && $areaRaw >= 30) return false;
            if ($areaFilter === '30_50' && ($areaRaw < 30 || $areaRaw > 50)) return false;
            if ($areaFilter === '50_80' && ($areaRaw < 50 || $areaRaw > 80)) return false;
            if ($areaFilter === '80_120' && ($areaRaw < 80 || $areaRaw > 120)) return false;
            if ($areaFilter === 'above_120' && $areaRaw <= 120) return false;
        }

        // 9. Bedrooms
        $bedroomsFilter = !empty($filters['bedrooms']) ? $filters['bedrooms'] : (!empty($filters['bedroom']) ? $filters['bedroom'] : null);
        if (!empty($bedroomsFilter)) {
            if (($item['bedrooms'] ?? 0) < intval($bedroomsFilter)) {
                return false;
            }
        }

        // 10. Bathrooms
        $bathroomsFilter = !empty($filters['bathrooms']) ? $filters['bathrooms'] : (!empty($filters['bathroom']) ? $filters['bathroom'] : null);
        if (!empty($bathroomsFilter)) {
            if (($item['bathrooms'] ?? 0) < intval($bathroomsFilter)) {
                return false;
            }
        }

        // 11. Furniture
        if (!empty($filters['furniture'])) {
            $furnitureFilter = $filters['furniture'];
            $furnitureVal = mb_strtolower($item['furniture'] ?? '', 'UTF-8');
            if ($furnitureFilter === 'full') {
                if (strpos($furnitureVal, 'đầy đủ') === false && strpos($furnitureVal, 'full') === false) {
                    return false;
                }
            } elseif ($furnitureFilter === 'basic') {
                if (strpos($furnitureVal, 'cơ bản') === false) {
                    return false;
                }
            } elseif ($furnitureFilter === 'none') {
                if (strpos($furnitureVal, 'không') === false && strpos($furnitureVal, 'trống') === false && !empty($furnitureVal)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Resolve string property type label into frontend filter code.
     */
    protected function resolvePropertyTypeCode(string $typeLabel): string
    {
        $label = mb_strtolower($typeLabel, 'UTF-8');
        if (strpos($label, 'căn hộ') !== false || strpos($label, 'chung cư') !== false) {
            return 'apartment';
        }
        if (strpos($label, 'nhà') !== false || strpos($label, 'biệt thự') !== false || strpos($label, 'villa') !== false) {
            return 'house';
        }
        if (strpos($label, 'phòng trọ') !== false || strpos($label, 'nhà trọ') !== false) {
            return 'room';
        }
        if (strpos($label, 'đất') !== false) {
            return 'land';
        }
        if (strpos($label, 'mặt bằng') !== false || strpos($label, 'cửa hàng') !== false) {
            return 'premises';
        }
        if (strpos($label, 'văn phòng') !== false) {
            return 'office';
        }
        if (strpos($label, 'kho') !== false || strpos($label, 'xưởng') !== false) {
            return 'warehouse';
        }
        return '';
    }
}

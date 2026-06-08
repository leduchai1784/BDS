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
    /**
     * Cache TTL in seconds (1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * Get all properties from the NKS API or cache, with local mock fallback.
     */
    public function getAllProperties(): array
    {
        return Cache::remember('nks_api_properties', $this->cacheTtl, function () {
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

            // Fallback to local mock data if the API request fails
            return $this->getLocalMockProperties();
        });
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
    public function getPropertyById(int $id): array
    {
        $properties = $this->getAllProperties();
        $property = collect($properties)->firstWhere('id', $id);
        
        if (!$property) {
            abort(404, 'Không tìm thấy bất động sản yêu cầu.');
        }
        
        return $property;
    }

    /**
     * Search and filter properties with pagination and sorting.
     */
    public function search(array $filters = [], int $perPage = 6): LengthAwarePaginator
    {
        $properties = collect($this->getAllProperties());

        // Apply filters
        $properties = $this->applyFilters($properties, $filters);

        // Apply sorting
        $properties = $this->applySorting($properties, $filters['sort'] ?? 'latest');

        // Paginate collection manually
        $page = Paginator::resolveCurrentPage() ?: 1;
        $items = $properties->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $properties->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query()
            ]
        );
    }

    /**
     * Search and return all matching properties without pagination (for Map).
     */
    public function searchAllForMap(array $filters = []): array
    {
        $properties = collect($this->getAllProperties());

        // Apply filters
        $properties = $this->applyFilters($properties, $filters);

        return $properties->values()->toArray();
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
        
        return [
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'price' => $priceFormatted,
            'price_label' => $priceLabel,
            'price_raw' => $priceRaw,
            'area' => $area,
            'bedrooms' => isset($item['bed']) ? (int)$item['bed'] : 0,
            'bathrooms' => isset($item['bath']) ? (int)$item['bath'] : 0,
            'location' => $location,
            'district' => $district,
            'lat' => $lat,
            'lng' => $lng,
            'image' => $featureImg,
            'images' => array_values($images),
            'direction' => $direction,
            'furniture' => !empty($item['road']['title']) ? "Mặt tiền đường " . $item['road']['title'] . ", nội thất đầy đủ" : "Bàn giao cơ bản, nội thất đầy đủ",
            'legal' => "Sổ hồng chính chủ, hợp đồng cho thuê tối thiểu 1 năm",
            'is_vip' => ($id % 2 === 0),
            'is_new' => true,
            'agent' => [
                'name' => $agentName,
                'phone' => $agentPhone,
                'avatar' => $agentAvatar
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
        // 1. Filter by District
        if (!empty($filters['district'])) {
            $properties = $properties->where('district', $filters['district']);
        }

        // 2. Filter by Property Type
        if (!empty($filters['type'])) {
            $type = $filters['type'];
            $properties = $properties->filter(function ($item) use ($type) {
                if ($type === 'apartment') {
                    return stripos($item['type'], 'Căn hộ') !== false;
                } elseif ($type === 'house') {
                    return stripos($item['type'], 'Nhà') !== false;
                } elseif ($type === 'villa') {
                    return stripos($item['type'], 'Biệt thự') !== false;
                } elseif ($type === 'office') {
                    return stripos($item['type'], 'Văn phòng') !== false;
                }
                return stripos($item['type'], $type) !== false;
            });
        }

        // 3. Filter by Price Range
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
                return true;
            });
        }

        // 4. Filter by Area Range
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
            case 'area_desc':
                return $properties->sortByDesc('area');
            case 'latest':
            default:
                return $properties->sortByDesc('id');
        }
    }

    /**
     * Local mock properties fallback list.
     */
    protected function getLocalMockProperties(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Căn hộ chung cư Vinhomes Ocean Park Studio Full Nội Thất',
                'type' => 'Căn hộ chung cư',
                'price' => '6.5 triệu/tháng',
                'price_label' => '6.5tr',
                'price_raw' => 6500000,
                'area' => 35,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'location' => 'Gia Lâm, Hà Nội',
                'district' => 'GL',
                'lat' => 20.9944,
                'lng' => 105.9567,
                'image' => 'images/apartment_3.png',
                'images' => [
                    'images/apartment_3.png',
                    'images/apartment_1.png',
                    'images/apartment_2.png',
                    'images/hero_bg.png'
                ],
                'direction' => 'Đông Nam',
                'furniture' => 'Đầy đủ nội thất (Tivi, Tủ lạnh, Máy giặt, Điều hòa, Sofa, Giường nệm)',
                'legal' => 'Sổ hồng, Hợp đồng cho thuê tối thiểu 1 năm',
                'is_vip' => true,
                'is_new' => false,
                'agent' => [
                    'name' => 'Nguyễn Hải Đăng',
                    'phone' => '0987.654.321',
                    'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff'
                ],
                'created_at' => '2 giờ trước',
                'description' => 'Căn hộ Studio Vinhomes Ocean Park với thiết kế tối ưu, thoáng đãng, tận dụng tối đa ánh sáng tự nhiên. Căn hộ đã trang bị đầy đủ nội thất cao cấp chỉ việc xách vali vào ở. \n\nTiện ích nội khu đẳng cấp: Hồ nước mặn Ocean Park, biển hồ nước ngọt cát trắng mịn, bể bơi bốn mùa, sân tennis, cầu lông, khu BBQ ngoài trời. Hệ thống an ninh đa lớp, camera giám sát 24/7. Phù hợp cho người độc thân hoặc cặp vợ chồng trẻ.'
            ],
            [
                'id' => 2,
                'title' => 'Căn hộ Duplex Vinhomes Metropolis Liễu Giai view hồ cực đẹp',
                'type' => 'Căn hộ chung cư',
                'price' => '18 triệu/tháng',
                'price_label' => '18tr',
                'price_raw' => 18000000,
                'area' => 85,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'location' => 'Ba Đình, Hà Nội',
                'district' => 'BD',
                'lat' => 21.0315,
                'lng' => 105.8152,
                'image' => 'images/apartment_2.png',
                'images' => [
                    'images/apartment_2.png',
                    'images/apartment_1.png',
                    'images/apartment_3.png',
                    'images/hero_bg.png'
                ],
                'direction' => 'Tây Nam',
                'furniture' => 'Full nội thất sang trọng nhập khẩu Châu Âu',
                'legal' => 'Hợp đồng công chứng, cọc 2 tháng',
                'is_vip' => true,
                'is_new' => true,
                'agent' => [
                    'name' => 'Trần Thị Tuyết',
                    'phone' => '0912.345.678',
                    'avatar' => 'https://ui-avatars.com/api/?name=Tran+Thi+Tuyet&background=0077bb&color=fff'
                ],
                'created_at' => '4 giờ trước',
                'description' => 'Căn hộ thông tầng Duplex độc bản tại Vinhomes Metropolis Liễu Giai, sở hữu tầm nhìn panorama triệu đô hướng trực diện hồ Ngọc Khánh và hồ Tây lộng gió. Căn hộ được thiết kế thông tầng thoáng đãng với phòng khách cao 6m, nội thất hiện đại sang trọng.\n\nCư dân được tận hưởng toàn bộ dịch vụ tiêu chuẩn 5 sao chân tòa nhà, trung tâm thương mại Vincom sầm uất. Cọc 2 tháng thanh toán đầu tháng.'
            ],
            [
                'id' => 3,
                'title' => 'Biệt thự sân vườn Ciputra hiện đại có hồ bơi riêng biệt lập',
                'type' => 'Biệt thự / Villa',
                'price' => '45 triệu/tháng',
                'price_label' => '45tr',
                'price_raw' => 45000000,
                'area' => 250,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'location' => 'Tây Hồ, Hà Nội',
                'district' => 'TH',
                'lat' => 21.0722,
                'lng' => 105.7984,
                'image' => 'images/house_2.png',
                'images' => [
                    'images/house_2.png',
                    'images/house_1.png',
                    'images/hero_bg.png',
                    'images/apartment_1.png'
                ],
                'direction' => 'Nam',
                'furniture' => 'Nội thất liền tường cao cấp, khách thuê tự trang bị đồ rời',
                'legal' => 'Hợp đồng dài hạn từ 2 năm trở lên',
                'is_vip' => true,
                'is_new' => false,
                'agent' => [
                    'name' => 'Lê Hoàng Long',
                    'phone' => '0909.123.456',
                    'avatar' => 'https://ui-avatars.com/api/?name=Le+Hoang+Long&background=0077bb&color=fff'
                ],
                'created_at' => '1 ngày trước',
                'description' => 'Biệt thự đơn lập sân vườn tuyệt đẹp tọa lạc tại vị trí đắc địa quận Tây Hồ. Biệt thự có khuôn viên rộng rãi với sân cỏ xanh mướt, hồ bơi riêng biệt ngoài trời cực mát mẻ.\n\nThiết kế 3 tầng gồm 4 phòng ngủ rộng rãi ngập tràn ánh sáng, phòng khách rộng lớn liên thông bếp ăn hiện đại. Khu vực an ninh, yên tĩnh tuyệt đối, dân trí cao, rất thích hợp cho các chuyên gia nước ngoài hoặc gia đình thượng lưu sinh sống.'
            ],
            [
                'id' => 4,
                'title' => 'Nhà nguyên căn 3 tầng ngõ xe hơi Duy Tân thích hợp làm văn phòng',
                'type' => 'Nhà nguyên căn',
                'price' => '22 triệu/tháng',
                'price_label' => '22tr',
                'price_raw' => 22000000,
                'area' => 120,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'location' => 'Cầu Giấy, Hà Nội',
                'district' => 'CG',
                'lat' => 21.0362,
                'lng' => 105.7865,
                'image' => 'images/house_1.png',
                'images' => [
                    'images/house_1.png',
                    'images/house_2.png',
                    'images/hero_bg.png',
                    'images/apartment_3.png'
                ],
                'direction' => 'Đông Bắc',
                'furniture' => 'Cơ bản (Thiết bị vệ sinh, hệ thống đèn chiếu sáng, điều hòa các phòng)',
                'legal' => 'Chính chủ cho thuê, hợp đồng lâu dài',
                'is_vip' => false,
                'is_new' => true,
                'agent' => [
                    'name' => 'Phạm Minh Tuấn',
                    'phone' => '0888.777.999',
                    'avatar' => 'https://ui-avatars.com/api/?name=Pham+Minh+Tuan&background=0077bb&color=fff'
                ],
                'created_at' => '3 ngày trước',
                'description' => 'Nhà nguyên căn mặt tiền ngõ lớn xe hơi tránh nhau tại trung tâm Cầu Giấy. Nhà xây dựng kiên cố 1 trệt 2 lầu sân thượng thoáng mát. Mặt tiền rộng 6m đỗ xe thoải mái.\n\nKhông gian trống sàn rộng rãi, thích hợp làm văn phòng công ty, spa làm đẹp, trung tâm đào tạo hoặc kinh doanh online kết hợp ở gia đình. Vị trí giao thương thuận lợi, di chuyển nhanh sang các tuyến phố lớn Duy Tân, Xuân Thủy, Trần Thái Tông.'
            ],
            [
                'id' => 5,
                'title' => 'Căn hộ chung cư Sky City Láng Hạ nội thất tối giản hiện đại',
                'type' => 'Căn hộ chung cư',
                'price' => '12 triệu/tháng',
                'price_label' => '12tr',
                'price_raw' => 12000000,
                'area' => 72,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'location' => 'Đống Đa, Hà Nội',
                'district' => 'DD',
                'lat' => 21.0185,
                'lng' => 105.8159,
                'image' => 'images/apartment_1.png',
                'images' => [
                    'images/apartment_1.png',
                    'images/apartment_2.png',
                    'images/apartment_3.png',
                    'images/hero_bg.png'
                ],
                'direction' => 'Bắc',
                'furniture' => 'Đầy đủ nội thất thông minh tối giản diện tích',
                'legal' => 'Hợp đồng thuê 1 năm, cọc 1 tháng',
                'is_vip' => false,
                'is_new' => false,
                'agent' => [
                    'name' => 'Hoàng Thanh Mai',
                    'phone' => '0977.888.999',
                    'avatar' => 'https://ui-avatars.com/api/?name=Hoang+Thanh+Mai&background=0077bb&color=fff'
                ],
                'created_at' => '4 ngày trước',
                'description' => 'Căn hộ chung cư 2 phòng ngủ nằm trong tổ hợp chung cư cao cấp Sky City Láng Hạ. Căn hộ được decor theo phong cách Bắc Âu (Scandinavian) tối giản và hiện đại, mang lại cảm giác cực kỳ thoải mái và dễ chịu sau giờ làm việc căng thẳng.\n\nDịch vụ quản lý tòa nhà chuyên nghiệp, có phòng tập gym, yoga, siêu thị chân tòa nhà. Vị trí trung tâm Đống Đa dễ dàng kết nối đi Ba Đình, Cầu Giấy và Thanh Xuân.'
            ],
            [
                'id' => 6,
                'title' => 'Văn phòng hiện đại sẵn bàn ghế làm việc tại trung tâm Hoàn Kiếm',
                'type' => 'Văn phòng cho thuê',
                'price' => '35 triệu/tháng',
                'price_label' => '35tr',
                'price_raw' => 35000000,
                'area' => 110,
                'bedrooms' => 0,
                'bathrooms' => 2,
                'location' => 'Hoàn Kiếm, Hà Nội',
                'district' => 'HK',
                'lat' => 21.0285,
                'lng' => 105.8521,
                'image' => 'images/apartment_2.png',
                'images' => [
                    'images/apartment_2.png',
                    'images/apartment_3.png',
                    'images/hero_bg.png',
                    'images/house_1.png'
                ],
                'direction' => 'Đông',
                'furniture' => 'Bàn ghế làm việc cao cấp, tủ tài liệu, máy chiếu, bảng viết',
                'legal' => 'Hợp đồng xuất hóa đơn đỏ VAT đầy đủ',
                'is_vip' => false,
                'is_new' => false,
                'agent' => [
                    'name' => 'Nguyễn Hải Đăng',
                    'phone' => '0987.654.321',
                    'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff'
                ],
                'created_at' => '5 ngày trước',
                'description' => 'Văn phòng cho thuê cao cấp nằm tại tầng cao trung tâm sầm uất quận Hoàn Kiếm. Không gian văn phòng được thiết kế theo tiêu chuẩn quốc tế, trang bị sẵn đầy đủ hệ thống bàn ghế làm việc, tủ hồ sơ, thiết bị phòng họp hiện đại.\n\nGiá thuê đã bao gồm phí quản lý tòa nhà, nước sinh hoạt và dịch vụ dọn dẹp vệ sinh hàng ngày. Thích hợp cho doanh nghiệp start-up, văn phòng đại diện quy mô 15-20 nhân viên.'
            ]
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    /**
     * Get VIP properties with agent loaded.
     */
    public function getFeaturedProperties(int $limit = 6)
    {
        return Property::with('agent')
            ->where('is_vip', true)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get latest properties.
     */
    public function getLatestProperties(int $limit = 3)
    {
        return Property::with('agent')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get central system statistics for the homepage card stats.
     */
    public function getSystemStats()
    {
        return [
            'total_properties' => Property::count(),
            'total_agents' => User::where('role', 'agent')->count() ?: 12, // fallback count matching static stats
            'total_locations' => Property::distinct('district')->count() ?: 6,
            'total_appointments' => Appointment::count() ?: 185
        ];
    }

    /**
     * Get single property details.
     */
    public function getPropertyById(int $id)
    {
        return Property::with('agent')->findOrFail($id);
    }

    /**
     * Search and filter properties with pagination and sorting.
     */
    public function search(array $filters = [], int $perPage = 6)
    {
        $query = Property::with('agent');

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort'] ?? 'latest');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Search and return all matching properties without pagination (specifically for Map).
     */
    public function searchAllForMap(array $filters = [])
    {
        $query = Property::with('agent');

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Apply filter conditions to the query.
     */
    protected function applyFilters($query, array $filters)
    {
        // 1. Filter by District
        if (!empty($filters['district'])) {
            $query->where('district', $filters['district']);
        }

        // 2. Filter by Property Type (Support full type name or short key)
        if (!empty($filters['type'])) {
            $type = $filters['type'];
            if ($type === 'apartment') {
                $query->where('type', 'like', '%Căn hộ%');
            } elseif ($type === 'house') {
                $query->where('type', 'like', '%Nhà%');
            } elseif ($type === 'villa') {
                $query->where('type', 'like', '%Biệt thự%');
            } elseif ($type === 'office') {
                $query->where('type', 'like', '%Văn phòng%');
            } else {
                $query->where('type', 'like', '%' . $type . '%');
            }
        }

        // 3. Filter by Price Range
        if (!empty($filters['price'])) {
            $price = $filters['price'];
            // listings.blade.php filters
            if ($price === 'under_5') {
                $query->where('price', '<', 5000000);
            } elseif ($price === '5_10') {
                $query->whereBetween('price', [5000000, 10000000]);
            } elseif ($price === '10_20') {
                $query->whereBetween('price', [10000000, 20000000]);
            } elseif ($price === 'above_20') {
                $query->where('price', '>', 20000000);
            }
            // map.blade.php filters
            elseif ($price === 'under_10') {
                $query->where('price', '<', 10000000);
            } elseif ($price === '10_25') {
                $query->whereBetween('price', [10000000, 25000000]);
            } elseif ($price === 'above_25') {
                $query->where('price', '>', 25000000);
            }
        }

        // 4. Filter by Area Range
        if (!empty($filters['area'])) {
            $area = $filters['area'];
            if ($area === 'under_30') {
                $query->where('area', '<', 30);
            } elseif ($area === '30_50') {
                $query->whereBetween('area', [30, 50]);
            } elseif ($area === '50_80') {
                $query->whereBetween('area', [50, 80]);
            } elseif ($area === 'above_80') {
                $query->where('area', '>', 80);
            }
        }
    }

    /**
     * Apply sorting conditions to the query.
     */
    protected function applySorting($query, string $sortBy)
    {
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'area_desc':
                $query->orderBy('area', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    }
}

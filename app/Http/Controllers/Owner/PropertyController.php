<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Category;
use App\Http\Requests\Owner\StorePropertyRequest;
use App\Http\Requests\Owner\UpdatePropertyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        $categories = Category::all();
        return view('owner.properties.create', compact('categories'));
    }

    /**
     * Store a newly created property.
     */
    public function store(StorePropertyRequest $request)
    {
        // Format price label
        $priceLabel = $this->formatPriceLabel($request->price);

        // Resolve category_id based on type
        $categoryId = $request->category_id;
        if (empty($categoryId) && !empty($request->type)) {
            $categorySlug = match($request->type) {
                'Căn hộ chung cư' => 'chung-cu',
                'Nhà nguyên căn' => 'nha-nguyen-can',
                'Phòng trọ' => 'phong-tro',
                'Đất' => 'dat',
                'Mặt bằng' => 'mat-bang',
                'Văn phòng' => 'van-phong',
                'Kho, nhà xưởng' => 'kho-nha-xuong',
                default => 'chung-cu',
            };
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Create Property
        $property = Property::create([
            'owner_id' => Auth::id(),
            'category_id' => $categoryId,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'price_label' => $priceLabel,
            'deposit' => $request->deposit,
            'lease_term' => $request->lease_term,
            'frontage' => $request->frontage,
            'road_width' => $request->road_width,
            'floors' => $request->floors,
            'area' => $request->area,
            'bedroom' => $request->bedroom ?? 0,
            'bathroom' => $request->bathroom ?? 0,
            'direction' => $request->direction,
            'furniture' => $request->furniture,
            'legal' => $request->legal,
            'address' => $request->address,
            'ward' => $request->ward,
            'district' => $request->district,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone' => $request->phone,
            'zalo' => $request->zalo,
            'status' => 'pending', // Default is pending
            'is_vip' => false,
            'is_new' => true,
            'views_count' => 0,
        ]);

        // Upload main image
        if ($request->filled('image_url')) {
            $property->propertyImages()->create([
                'image_path' => $request->image_url,
                'is_primary' => true,
            ]);
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('properties', 'public');
            $property->propertyImages()->create([
                'image_path' => $path,
                'is_primary' => true,
            ]);
        }

        // Upload gallery images from URLs
        if ($request->filled('gallery_urls')) {
            $urls = preg_split('/[\n,\r]+/', $request->gallery_urls);
            foreach ($urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    $property->propertyImages()->create([
                        'image_path' => $url,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        // Upload gallery images from files
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('properties', 'public');
                $property->propertyImages()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Đăng tin mới thành công! Tin của bạn đang chờ kiểm duyệt từ Admin.');
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit($id)
    {
        $property = Property::findOrFail($id);
        
        // Authorization check
        abort_if($property->owner_id !== Auth::id(), 403, 'Bạn không có quyền chỉnh sửa tin đăng này.');

        $categories = Category::all();
        return view('owner.properties.edit', compact('property', 'categories'));
    }

    /**
     * Update the specified property.
     */
    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = Property::findOrFail($id);
        
        // Authorization check
        abort_if($property->owner_id !== Auth::id(), 403, 'Bạn không có quyền chỉnh sửa tin đăng này.');

        // Validate total images count <= 10
        $existingCount = $property->propertyImages()->count();
        $newFilesCount = $request->hasFile('images') ? count($request->file('images')) : 0;
        
        $newUrlsCount = 0;
        if ($request->filled('gallery_urls')) {
            $urls = preg_split('/[\n,\r]+/', $request->gallery_urls);
            foreach ($urls as $url) {
                if (!empty(trim($url))) {
                    $newUrlsCount++;
                }
            }
        }
        $newCount = $newFilesCount + $newUrlsCount;
        $deletedCount = $request->filled('delete_images') ? count($request->delete_images) : 0;
        $totalCount = $existingCount + $newCount - $deletedCount;

        if ($totalCount > 10) {
            return back()->withErrors(['images' => 'Tổng số hình ảnh của tin đăng không được vượt quá 10.'])->withInput();
        }

        // Ensure at least 1 image remains
        $newPrimaryUploaded = $request->hasFile('image') || $request->filled('image_url');
        $hasPrimaryRemaining = $property->propertyImages()->where('is_primary', true)->exists();
        if (!$newPrimaryUploaded && !$hasPrimaryRemaining) {
            return back()->withErrors(['image' => 'Tin đăng phải có ít nhất 1 ảnh đại diện.'])->withInput();
        }

        // Resolve category_id based on type
        $categoryId = $request->category_id;
        if (empty($categoryId) && !empty($request->type)) {
            $categorySlug = match($request->type) {
                'Căn hộ chung cư' => 'chung-cu',
                'Nhà nguyên căn' => 'nha-nguyen-can',
                'Phòng trọ' => 'phong-tro',
                'Đất' => 'dat',
                'Mặt bằng' => 'mat-bang',
                'Văn phòng' => 'van-phong',
                'Kho, nhà xưởng' => 'kho-nha-xuong',
                default => 'chung-cu',
            };
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Update basic details
        $property->update([
            'category_id' => $categoryId,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'price_label' => $this->formatPriceLabel($request->price),
            'deposit' => $request->deposit,
            'lease_term' => $request->lease_term,
            'frontage' => $request->frontage,
            'road_width' => $request->road_width,
            'floors' => $request->floors,
            'area' => $request->area,
            'bedroom' => $request->bedroom ?? 0,
            'bathroom' => $request->bathroom ?? 0,
            'direction' => $request->direction,
            'furniture' => $request->furniture,
            'legal' => $request->legal,
            'address' => $request->address,
            'ward' => $request->ward,
            'district' => $request->district,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone' => $request->phone,
            'zalo' => $request->zalo,
            'status' => in_array($property->status, ['approved', 'rented']) ? $property->status : 'pending',
        ]);

        // Update main image if new one is uploaded or URL is provided
        if ($request->filled('image_url')) {
            $oldPrimary = $property->propertyImages()->where('is_primary', true)->first();
            if ($oldPrimary) {
                if (!filter_var($oldPrimary->image_path, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($oldPrimary->image_path);
                }
                $oldPrimary->delete();
            }

            $property->propertyImages()->create([
                'image_path' => $request->image_url,
                'is_primary' => true,
            ]);
        } elseif ($request->hasFile('image')) {
            $oldPrimary = $property->propertyImages()->where('is_primary', true)->first();
            if ($oldPrimary) {
                if (!filter_var($oldPrimary->image_path, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($oldPrimary->image_path);
                }
                $oldPrimary->delete();
            }

            $path = $request->file('image')->store('properties', 'public');
            $property->propertyImages()->create([
                'image_path' => $path,
                'is_primary' => true,
            ]);
        }

        // Delete requested gallery images
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $delPath) {
                $cleanPath = str_replace('storage/', '', $delPath);
                $imgRecord = $property->propertyImages()
                    ->where('image_path', $cleanPath)
                    ->orWhere('image_path', $delPath)
                    ->first();
                if ($imgRecord) {
                    if (!filter_var($imgRecord->image_path, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($imgRecord->image_path);
                    }
                    $imgRecord->delete();
                }
            }
        }

        // Upload new gallery images from URLs
        if ($request->filled('gallery_urls')) {
            $urls = preg_split('/[\n,\r]+/', $request->gallery_urls);
            foreach ($urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    $property->propertyImages()->create([
                        'image_path' => $url,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        // Upload new gallery images from files
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('properties', 'public');
                $property->propertyImages()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Cập nhật tin đăng thành công! Tin đăng của bạn đang chờ kiểm duyệt lại.');
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        // Authorization check
        abort_if($property->owner_id !== Auth::id(), 403, 'Bạn không có quyền xóa tin đăng này.');

        // Delete image files physically
        foreach ($property->propertyImages as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        // Delete from database (SoftDeletes is configured, so this will soft delete)
        $property->delete();

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Xóa tin đăng thành công!');
    }

    /**
     * Extend property listing duration (push to top).
     */
    public function extend($id)
    {
        $property = Property::findOrFail($id);
        abort_if($property->owner_id !== Auth::id(), 403, 'Bạn không có quyền gia hạn tin đăng này.');

        $property->update([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Gia hạn tin đăng thành công! Tin đăng đã được đẩy lên đầu.');
    }

    /**
     * Hide/Show property listing (toggle rented status).
     */
    public function hide($id)
    {
        $property = Property::findOrFail($id);
        abort_if($property->owner_id !== Auth::id(), 403, 'Bạn không có quyền ẩn/hiện tin đăng này.');

        if ($property->status === 'rented') {
            $property->update(['status' => 'approved']);
            $msg = 'Đã hiện tin đăng thành công!';
        } else {
            $property->update(['status' => 'rented']);
            $msg = 'Đã ẩn tin đăng thành công!';
        }

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', $msg);
    }

    private function formatPriceLabel($price)
    {
        $purpose = request()->input('purpose', 'rent');
        $suffix = $purpose === 'sale' ? '' : '/tháng';

        if ($price >= 1000000000) {
            $value = $price / 1000000000;
            return round($value, 1) . ' tỷ' . $suffix;
        } elseif ($price >= 1000000) {
            $value = $price / 1000000;
            return round($value, 1) . ' triệu' . $suffix;
        }
        return number_format($price) . 'đ' . $suffix;
    }
}

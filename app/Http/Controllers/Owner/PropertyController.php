<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PropertyController extends Controller
{
    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        return redirect()->route('profile.index', ['tab' => 'create_property']);
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'type' => 'required|string',
            'district' => 'required|string|max:10',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'direction' => 'nullable|string|max:50',
            'furniture' => 'nullable|string',
            'legal' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'description.required' => 'Mô tả không được để trống.',
            'price.required' => 'Giá thuê không được để trống.',
            'price.numeric' => 'Giá thuê phải là số.',
            'area.required' => 'Diện tích không được để trống.',
            'area.numeric' => 'Diện tích phải là số.',
            'location.required' => 'Địa chỉ không được để trống.',
            'type.required' => 'Loại hình không được để trống.',
            'district.required' => 'Khu vực/Quận không được để trống.',
            'lat.required' => 'Vĩ độ (Latitude) không được để trống.',
            'lng.required' => 'Kinh độ (Longitude) không được để trống.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục chọn không hợp lệ.',
            'image.required' => 'Ảnh đại diện tin đăng là bắt buộc.',
            'image.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'image.max' => 'Ảnh đại diện tối đa 3MB.',
            'images.*.image' => 'Ảnh phụ phải là tệp hình ảnh.',
            'images.*.max' => 'Ảnh phụ tối đa 3MB.',
        ]);

        // Helper to format price label
        $priceLabel = $this->formatPriceLabel($request->price);

        // Upload main image
        $mainImage = $request->file('image');
        $mainFilename = 'prop_' . time() . '_' . uniqid() . '.' . $mainImage->getClientOriginalExtension();
        $mainImage->move(public_path('uploads/properties'), $mainFilename);
        $mainPath = 'uploads/properties/' . $mainFilename;

        // Upload extra gallery images
        $galleryPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = 'prop_gallery_' . time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('uploads/properties'), $filename);
                $galleryPaths[] = 'uploads/properties/' . $filename;
            }
        }

        Property::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'price_label' => $priceLabel,
            'area' => $request->area,
            'location' => $request->location,
            'type' => $request->type,
            'district' => $request->district,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'category_id' => $request->category_id,
            'image' => $mainPath,
            'images' => $galleryPaths,
            'bedrooms' => $request->bedrooms ?? 0,
            'bathrooms' => $request->bathrooms ?? 0,
            'direction' => $request->direction,
            'furniture' => $request->furniture,
            'legal' => $request->legal,
            'agent_id' => Auth::id(),
            'status' => 'pending', // Waiting for admin approval
            'is_vip' => false,
            'is_new' => true,
            'views' => 0,
        ]);

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Đăng tin mới thành công! Tin của bạn đang chờ phê duyệt từ quản trị viên.');
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit($id)
    {
        $property = Property::findOrFail($id);
        
        // Authorization check
        abort_if($property->agent_id !== Auth::id(), 403, 'Bạn không có quyền chỉnh sửa tin đăng này.');

        return redirect()->route('profile.index', [
            'tab' => 'edit_property',
            'property_id' => $id
        ]);
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        
        // Authorization check
        abort_if($property->agent_id !== Auth::id(), 403, 'Bạn không có quyền chỉnh sửa tin đăng này.');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'type' => 'required|string',
            'district' => 'required|string|max:10',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'direction' => 'nullable|string|max:50',
            'furniture' => 'nullable|string',
            'legal' => 'nullable|string|max:255',
            'delete_images' => 'nullable|array', // List of gallery image paths to delete
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'description.required' => 'Mô tả không được để trống.',
            'price.required' => 'Giá thuê không được để trống.',
            'price.numeric' => 'Giá thuê phải là số.',
            'area.required' => 'Diện tích không được để trống.',
            'area.numeric' => 'Diện tích phải là số.',
            'location.required' => 'Địa chỉ không được để trống.',
            'type.required' => 'Loại hình không được để trống.',
            'district.required' => 'Khu vực/Quận không được để trống.',
            'lat.required' => 'Vĩ độ không được để trống.',
            'lng.required' => 'Kinh độ không được để trống.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục chọn không hợp lệ.',
            'image.image' => 'Ảnh đại diện phải là hình ảnh.',
            'image.max' => 'Ảnh đại diện tối đa 3MB.',
            'images.*.image' => 'Ảnh phụ phải là hình ảnh.',
            'images.*.max' => 'Ảnh phụ tối đa 3MB.',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'price_label' => $this->formatPriceLabel($request->price),
            'area' => $request->area,
            'location' => $request->location,
            'type' => $request->type,
            'district' => $request->district,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'category_id' => $request->category_id,
            'bedrooms' => $request->bedrooms ?? 0,
            'bathrooms' => $request->bathrooms ?? 0,
            'direction' => $request->direction,
            'furniture' => $request->furniture,
            'legal' => $request->legal,
        ];

        // Handle main image update
        if ($request->hasFile('image')) {
            // Delete old file
            if ($property->image && File::exists(public_path($property->image))) {
                @unlink(public_path($property->image));
            }
            // Upload new file
            $mainImage = $request->file('image');
            $mainFilename = 'prop_' . time() . '_' . uniqid() . '.' . $mainImage->getClientOriginalExtension();
            $mainImage->move(public_path('uploads/properties'), $mainFilename);
            $data['image'] = 'uploads/properties/' . $mainFilename;
        }

        // Get existing gallery paths
        $currentGallery = $property->images ?? [];

        // Handle deleted gallery images
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $delPath) {
                if (in_array($delPath, $currentGallery)) {
                    if (File::exists(public_path($delPath))) {
                        @unlink(public_path($delPath));
                    }
                    $currentGallery = array_values(array_diff($currentGallery, [$delPath]));
                }
            }
        }

        // Handle newly uploaded gallery images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = 'prop_gallery_' . time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('uploads/properties'), $filename);
                $currentGallery[] = 'uploads/properties/' . $filename;
            }
        }

        $data['images'] = $currentGallery;

        // Reset status to pending so admin re-approves after edit (optional, but standard for security)
        $data['status'] = 'pending';

        $property->update($data);

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Cập nhật tin đăng thành công! Tin đăng đang chờ kiểm duyệt lại.');
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        // Authorization check
        abort_if($property->agent_id !== Auth::id(), 403, 'Bạn không có quyền xóa tin đăng này.');

        // Delete main image file
        if ($property->image && File::exists(public_path($property->image))) {
            @unlink(public_path($property->image));
        }

        // Delete gallery image files
        if (!empty($property->images)) {
            foreach ($property->images as $img) {
                if (File::exists(public_path($img))) {
                    @unlink(public_path($img));
                }
            }
        }

        $property->delete();

        return redirect()->route('profile.index', ['tab' => 'properties'])
            ->with('success', 'Xóa tin đăng thành công!');
    }

    /**
     * Helper to format price label (VND -> Million / Month)
     */
    private function formatPriceLabel($price)
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
}

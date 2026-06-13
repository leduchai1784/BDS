<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:10|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'area' => 'required|numeric|gt:0',
            'address' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'district' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'images' => 'nullable|array|max:9',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
            'bedroom' => 'nullable|integer|min:0',
            'bathroom' => 'nullable|integer|min:0',
            'direction' => 'nullable|string|max:50',
            'furniture' => 'nullable|string',
            'legal' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.min' => 'Tiêu đề phải dài ít nhất 10 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả không được để trống.',
            'price.required' => 'Giá thuê không được để trống.',
            'price.numeric' => 'Giá thuê phải là số.',
            'price.gt' => 'Giá thuê phải lớn hơn 0.',
            'area.required' => 'Diện tích không được để trống.',
            'area.numeric' => 'Diện tích phải là số.',
            'area.gt' => 'Diện tích phải lớn hơn 0.',
            'address.required' => 'Địa chỉ chi tiết không được để trống.',
            'ward.required' => 'Phường/Xã không được để trống.',
            'district.required' => 'Quận/Huyện không được để trống.',
            'city.required' => 'Tỉnh/Thành phố không được để trống.',
            'latitude.required' => 'Vĩ độ không được để trống.',
            'longitude.required' => 'Kinh độ không được để trống.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục chọn không hợp lệ.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'image.required' => 'Ảnh đại diện tin đăng là bắt buộc.',
            'image.image' => 'Ảnh đại diện phải là hình ảnh.',
            'image.max' => 'Ảnh đại diện tối đa 3MB.',
            'images.max' => 'Bạn chỉ được chọn tối đa 9 ảnh phụ.',
            'images.*.image' => 'Ảnh phụ phải là hình ảnh.',
            'images.*.max' => 'Mỗi ảnh phụ tối đa 3MB.',
        ];
    }
}

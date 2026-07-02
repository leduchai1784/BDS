<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
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
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'gallery_urls' => 'nullable|string',
            'bedroom' => 'nullable|integer|min:0',
            'bathroom' => 'nullable|integer|min:0',
            'direction' => 'nullable|string|max:50',
            'furniture' => 'nullable|string',
            'legal' => 'nullable|string|max:255',
            'deposit' => 'nullable|numeric|min:0',
            'lease_term' => 'nullable|string|max:255',
            'frontage' => 'nullable|numeric|min:0',
            'road_width' => 'nullable|numeric|min:0',
            'floors' => 'nullable|integer|min:0',
            'delete_images' => 'nullable|array',
        ];
    }

    /**
     * Log validation errors for easier debugging.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Log::warning('Update Property Validation Failed: ' . json_encode($validator->errors()->toArray()) . ' | Input: ' . json_encode($this->except(['image', 'images'])));
        parent::failedValidation($validator);
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
            'image_url.url' => 'Liên kết ảnh đại diện phải là một URL hợp lệ.',
            'image.image' => 'Ảnh đại diện phải là hình ảnh.',
            'image.max' => 'Ảnh đại diện tối đa 3MB.',
            'images.*.image' => 'Ảnh phụ phải là hình ảnh.',
            'images.*.max' => 'Mỗi ảnh phụ tối đa 3MB.',
        ];
    }
}

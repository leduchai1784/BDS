<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\AiCampaign;
use App\Services\AiCampaignService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiMarketingController extends Controller
{
    protected AiCampaignService $aiService;

    public function __construct(AiCampaignService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Helper giải quyết property_id thực tế hoặc mock.
     */
    protected function resolveProperty(string $propertyId): Property
    {
        if ($propertyId === 'mock_prop_1') {
            return new Property([
                'title' => 'Căn hộ dịch vụ Hà Đô Centrosa Quận 10',
                'transaction_type' => 'rent',
                'price' => 14500000,
                'price_label' => '14.5 triệu/tháng',
                'area' => 60,
                'bedroom' => 2,
                'bathroom' => 2,
                'address' => 'Đường Ba Tháng Hai, Phường 12',
                'ward' => 'Phường 12',
                'district' => 'Quận 10',
                'city' => 'Thành phố Hồ Chí Minh',
                'furniture' => 'Đầy đủ nội thất cao cấp: Tivi, Tủ lạnh, Máy giặt, Điều hòa, Giường đệm mới 100%',
                'direction' => 'Đông Nam',
                'description' => 'Căn hộ dịch vụ nằm tại tòa Jasmine thuộc dự án Hà Đô Centrosa. Thiết kế hiện đại, ban công rộng lộng gió. Tiện ích nội khu đẳng cấp: Hồ bơi vô cực, Gym miễn phí, Shophouse tiện lợi. An ninh bảo vệ và khóa thông minh 24/7.',
            ]);
        }

        if ($propertyId === 'mock_prop_2') {
            return new Property([
                'title' => 'Nhà nguyên căn Hẻm xe hơi Lê Quang Định Bình Thạnh',
                'transaction_type' => 'sale',
                'price' => 4200000000,
                'price_label' => '4.2 tỷ',
                'area' => 75,
                'bedroom' => 3,
                'bathroom' => 3,
                'address' => 'Đường Lê Quang Định, Phường 11',
                'ward' => 'Phường 11',
                'district' => 'Quận Bình Thạnh',
                'city' => 'Thành phố Hồ Chí Minh',
                'furniture' => 'Nội thất cơ bản, hệ thống đèn led thông minh, kệ bếp tủ bếp gỗ tự nhiên',
                'direction' => 'Tây Nam',
                'description' => 'Nhà phố đúc 2 lầu kiên cố, hẻm xe hơi đỗ cửa, khu phân lô dân trí cao cực kỳ yên tĩnh. Rất gần chợ Bà Chiểu, công viên Gia Định và các trường học điểm. Sổ hồng chính chủ hoàn công đầy đủ, công chứng ngay.',
            ]);
        }

        // Với BĐS thực tế trong DB, kiểm tra quyền sở hữu
        $property = Property::findOrFail($propertyId);
        if ($property->owner_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập thông tin bất động sản này.');
        }

        return $property;
    }

    /**
     * Tên đẹp cho Tone/Goal tiếng Việt.
     */
    protected function getReadableGoal(string $goal): string
    {
        return match($goal) {
            'rent_fast' => 'Cho thuê nhanh',
            'luxury_brand' => 'Quảng bá thương hiệu cao cấp',
            'price_deal' => 'Cắt lỗ gấp / Ưu đãi tốt',
            'review_detail' => 'Review chi tiết trải nghiệm',
            default => 'Quảng cáo tổng hợp',
        };
    }

    protected function getReadableTone(string $tone): string
    {
        return match($tone) {
            'friendly' => 'Thân thiện, gần gũi',
            'professional' => 'Chuyên nghiệp, tin cậy',
            'funny' => 'Hài hước, bắt trend',
            'emotional' => 'Gợi mở cảm xúc',
            default => 'Tự nhiên',
        };
    }

    /**
     * AJAX Endpoint: Sinh Facebook posts.
     */
    public function generateFacebook(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|string',
            'campaign_goal' => 'required|string',
            'campaign_tone' => 'required|string',
        ]);

        try {
            $property = $this->resolveProperty($request->property_id);
            $posts = $this->aiService->generateFacebook(
                $property,
                $request->campaign_goal,
                $request->campaign_tone
            );

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX Endpoint: Sinh TikTok scripts.
     */
    public function generateTiktok(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|string',
            'campaign_goal' => 'required|string',
            'campaign_tone' => 'required|string',
        ]);

        try {
            $property = $this->resolveProperty($request->property_id);
            $scripts = $this->aiService->generateTiktok(
                $property,
                $request->campaign_goal,
                $request->campaign_tone
            );

            return response()->json([
                'success' => true,
                'data' => $scripts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX Endpoint: Sinh SEO articles.
     */
    public function generateSeo(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|string',
            'campaign_goal' => 'required|string',
            'campaign_tone' => 'required|string',
        ]);

        try {
            $property = $this->resolveProperty($request->property_id);
            $articles = $this->aiService->generateSeo(
                $property,
                $request->campaign_goal,
                $request->campaign_tone
            );

            return response()->json([
                'success' => true,
                'data' => $articles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX Endpoint: Sinh Email, SMS & Banners.
     */
    public function generateEmailSms(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|string',
            'campaign_goal' => 'required|string',
            'campaign_tone' => 'required|string',
        ]);

        try {
            $property = $this->resolveProperty($request->property_id);
            $content = $this->aiService->generateEmailSms(
                $property,
                $request->campaign_goal,
                $request->campaign_tone
            );

            return response()->json([
                'success' => true,
                'data' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX Endpoint: Sinh trọn bộ AI Content Studio (Tự do).
     */
    public function generateContentStudio(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'transaction_type' => 'required|string|in:rent,sale',
            'property_type' => 'required|string',
            'price' => 'nullable|string|max:50',
            'area' => 'nullable|string|max:50',
            'address' => 'required|string|max:255',
            'highlights' => 'nullable|string|max:1000',
            'tone' => 'required|string',
        ]);

        try {
            $result = $this->aiService->generateContentStudio($validated);
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu chiến dịch AI vào DB.
     */
    public function saveCampaign(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:marketing,content_studio',
            'property_id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'tone' => 'required|string',
            'content' => 'required|array',
        ]);

        try {
            // Xác thực property_id dạng UUID thực tế (nếu có, không lưu mock string vào FK)
            $dbPropertyId = null;
            if ($request->filled('property_id') && \Illuminate\Support\Str::isUuid($request->property_id)) {
                $dbPropertyId = $request->property_id;
            }

            $campaign = AiCampaign::create([
                'owner_id' => Auth::id(),
                'property_id' => $dbPropertyId,
                'type' => $request->type,
                'title' => $request->title,
                'goal' => $request->goal ? $this->getReadableGoal($request->goal) : null,
                'tone' => $this->getReadableTone($request->tone),
                'content' => $request->content,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lưu chiến dịch thành công.',
                'campaign' => $campaign
            ]);
        } catch (\Exception $e) {
            Log::error('saveCampaign failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu chiến dịch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách lịch sử chiến dịch của chủ nhà.
     */
    public function getHistory(): JsonResponse
    {
        try {
            $campaigns = AiCampaign::where('owner_id', Auth::id())
                ->with('property')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'campaigns' => $campaigns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa một chiến dịch.
     */
    public function deleteCampaign($id): JsonResponse
    {
        try {
            $campaign = AiCampaign::where('id', $id)
                ->where('owner_id', Auth::id())
                ->firstOrFail();

            $campaign->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa chiến dịch thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}

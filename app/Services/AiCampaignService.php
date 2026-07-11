<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCampaignService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = trim(config('services.gemini.key', ''));
        $this->model = trim(config('services.gemini.model', 'gemini-3.1-flash-lite'));
    }

    /**
     * Gửi request gọi API Gemini và parse JSON kết quả.
     */
    protected function callGemini(string $systemInstruction, string $prompt, int $maxTokens = 4096): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Chưa cấu hình API Key cho Gemini trong file .env');
        }

        try {
            $cleanModel = ltrim($this->model, '/');
            if (str_starts_with($cleanModel, 'models/')) {
                $cleanModel = substr($cleanModel, 7);
            }
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$cleanModel}:generateContent?key={$this->apiKey}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(60) // Tăng timeout cho các tác vụ sinh nội dung lớn
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.6,
                    'topP' => 0.95,
                    'maxOutputTokens' => $maxTokens,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error in AiCampaignService: ' . $response->body());
                throw new \Exception('Lỗi phản hồi từ Gemini API: ' . $response->status());
            }

            $json = $response->json();
            $replyText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Dọn dẹp markdown code blocks nếu có
            $replyText = preg_replace('/^```json\s*/i', '', $replyText);
            $replyText = preg_replace('/```$/', '', $replyText);
            $replyText = trim($replyText);

            $decoded = json_decode($replyText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode failed for Gemini response: ' . json_last_error_msg(), [
                    'raw_response' => $replyText
                ]);
                throw new \Exception('Không thể phân tích dữ liệu JSON trả về từ AI.');
            }

            return $decoded;

        } catch (\Exception $e) {
            Log::error('AiCampaignService callGemini failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy chuỗi thông tin tóm tắt của Bất động sản.
     */
    protected function getPropertyDetailsString(Property $property): string
    {
        return sprintf(
            "Tiêu đề: %s\nLoại hình: %s\nGiá: %s\nDiện tích: %d m2\nĐịa chỉ: %s, Phường %s, Quận %s, Tỉnh/Thành %s\nPhòng ngủ: %d, Phòng tắm: %d\nHướng: %s\nNội thất: %s\nPháp lý: %s\nMô tả chi tiết: %s",
            $property->title,
            $property->transaction_type === 'rent' ? 'Cho thuê' : 'Bán',
            $property->price_label ?? ($property->price . ' VNĐ'),
            $property->area,
            $property->address,
            $property->ward,
            $property->district,
            $property->city,
            $property->bedroom,
            $property->bathroom,
            $property->direction ?? 'Không xác định',
            $property->furniture ?? 'Không xác định',
            $property->legal ?? 'Sổ hồng riêng/Hợp pháp',
            strip_tags($property->description)
        );
    }

    /**
     * Sinh 20 bài viết Facebook.
     */
    public function generateFacebook(Property $property, string $goal, string $tone): array
    {
        $details = $this->getPropertyDetailsString($property);

        $systemInstruction = "Bạn là một chuyên gia viết Content Marketing Bất động sản hàng đầu tại Việt Nam. Nhiệm vụ của bạn là tạo ra đúng 20 bài đăng Facebook độc đáo, thu hút khách hàng dựa trên thông tin bất động sản được cung cấp.\n\n"
            . "Yêu cầu nội dung:\n"
            . "1. Đa dạng hóa góc nhìn cho 20 bài đăng (Ví dụ: Góc nhìn lợi ích đầu tư, góc nhìn gia đình tìm tổ ấm, góc nhìn tiện ích nội/ngoại khu, bài viết dạng danh sách tiện ích, bài đăng dạng kêu gọi khẩn cấp ưu đãi, bài review chi tiết, bài viết ngắn gọn dí dỏm, v.v.).\n"
            . "2. Sử dụng tiếng Việt tự nhiên, trẻ trung hoặc chuyên nghiệp phù hợp với cấu hình tone giọng và mục tiêu chiến dịch.\n"
            . "3. Mỗi bài viết cần chèn các emoji sinh động, hashtag phù hợp, lời kêu gọi hành động CTA rõ ràng.\n"
            . "4. Đầu ra bắt buộc phải là một mảng JSON chứa đúng 20 phần tử, mỗi phần tử có cấu trúc: {\"id\": số thứ tự từ 1 đến 20, \"title\": \"Tiêu đề bài đăng\", \"content\": \"Nội dung chi tiết bài đăng Facebook\"}.";

        $prompt = "Thông tin bất động sản nguồn:\n{$details}\n\nMục tiêu chiến dịch: {$goal}\nGiọng điệu/Tone: {$tone}\n\nHãy tạo 20 bài đăng Facebook đa góc nhìn và trả về mảng JSON chuẩn.";

        // Thiết lập maxTokens lớn (8000) để đảm bảo sinh đủ 20 bài mà không bị cắt ngang
        return $this->callGemini($systemInstruction, $prompt, 8192);
    }

    /**
     * Sinh 10 kịch bản video TikTok/Youtube Short.
     */
    public function generateTiktok(Property $property, string $goal, string $tone): array
    {
        $details = $this->getPropertyDetailsString($property);

        $systemInstruction = "Bạn là chuyên gia sáng tạo nội dung video ngắn (TikTok, YouTube Shorts, Reels) chuyên nghiệp về Bất động sản tại Việt Nam. Nhiệm vụ của bạn là soạn thảo đúng 10 kịch bản video ngắn có tính lan truyền cao dựa trên thông tin bất động sản.\n\n"
            . "Yêu cầu kịch bản:\n"
            . "1. Độ dài mỗi kịch bản khoảng 30 - 60 giây, kịch tính, lôi cuốn ngay từ 3 giây đầu tiên.\n"
            . "2. Mỗi kịch bản bao gồm:\n"
            . "   - id: Số thứ tự (1-10).\n"
            . "   - title: Tiêu đề kịch bản (ví dụ: 'Review 60s căn hộ Quận 10', 'Bí mật đằng sau căn nhà 4 tỷ').\n"
            . "   - visual: Mô tả phân cảnh hình ảnh/cảnh quay gợi ý cho người quay phim (đặt trong ngoặc vuông).\n"
            . "   - audio: Lời thoại Voiceover đầy cảm xúc, tự nhiên để đọc hoặc thu âm.\n"
            . "   - overlay: Các dòng chữ chạy trên màn hình (Text overlay) để giữ chân người xem.\n"
            . "3. Trả về dưới dạng một mảng JSON chứa đúng 10 kịch bản có định dạng: [{\"id\": 1, \"title\": \"...\", \"visual\": \"...\", \"audio\": \"...\", \"overlay\": \"...\"}].";

        $prompt = "Thông tin bất động sản:\n{$details}\n\nMục tiêu chiến dịch: {$goal}\nTone giọng: {$tone}\n\nHãy tạo 10 kịch bản video ngắn TikTok/Shorts.";

        return $this->callGemini($systemInstruction, $prompt, 8192);
    }

    /**
     * Sinh 5 bài viết chuẩn SEO Website.
     */
    public function generateSeo(Property $property, string $goal, string $tone): array
    {
        $details = $this->getPropertyDetailsString($property);

        $systemInstruction = "Bạn là một chuyên gia SEO Copywriter chuyên nghiệp về Bất động sản tại Việt Nam. Hãy dựa trên thông tin bất động sản để tạo ra đúng 5 bài viết chuẩn SEO Website chất lượng cao.\n\n"
            . "Yêu cầu bài viết:\n"
            . "1. Mỗi bài viết cần giải quyết một chủ đề cụ thể liên quan đến bất động sản đó (Ví dụ: Phân tích tiềm năng đầu tư, Đánh giá chi tiết các tiện ích sống, Hướng dẫn pháp lý và quy trình giao dịch, So sánh giá cả khu vực, Cẩm nang định cư cho gia đình trẻ tại địa phương).\n"
            . "2. Định dạng bài viết: Phần thân bài viết cần được viết dưới dạng HTML chuẩn sử dụng các thẻ <h2>, <h3>, <p>, <ul>, <li> để trình bày khoa học và dễ đọc.\n"
            . "3. Mỗi bài viết bao gồm:\n"
            . "   - title: Tiêu đề bài viết thu hút click chuẩn SEO.\n"
            . "   - meta: Thẻ Meta Description tóm tắt nội dung hấp dẫn dưới 160 ký tự.\n"
            . "   - content: Nội dung bài viết định dạng HTML chi tiết (khoảng 300 - 500 từ mỗi bài để tối ưu và tránh ngắt quãng).\n"
            . "4. Trả về dưới dạng một mảng JSON chứa đúng 5 bài viết có cấu trúc: [{\"title\": \"...\", \"meta\": \"...\", \"content\": \"...\"}].";

        $prompt = "Thông tin bất động sản:\n{$details}\n\nMục tiêu chiến dịch: {$goal}\nTone giọng: {$tone}\n\nHãy tạo 5 bài viết SEO Website định dạng HTML.";

        return $this->callGemini($systemInstruction, $prompt, 8192);
    }

    /**
     * Sinh Email, SMS & Banner Prompts.
     */
    public function generateEmailSms(Property $property, string $goal, string $tone): array
    {
        $details = $this->getPropertyDetailsString($property);

        $systemInstruction = "Bạn là một chuyên gia Email Marketing và Copywriting. Dựa trên thông tin bất động sản được cung cấp, hãy biên soạn các tài liệu marketing bổ trợ sau:\n\n"
            . "1. 1 mẫu Email chào mời khách hàng hoặc chăm sóc khách quan tâm (gồm subject và content chi tiết).\n"
            . "2. 3 biến thể tin nhắn SMS ngắn gọn hoặc Zalo ZNS (dưới 250 ký tự, không dấu hoặc có dấu nhưng cực kỳ súc tích kèm link demo/hotline).\n"
            . "3. 2 đoạn Prompt mô tả hình ảnh bằng tiếng Anh chi tiết để đưa vào các công cụ sinh ảnh AI (như Midjourney, DALL-E, Stable Diffusion) để vẽ hình ảnh minh họa cho căn nhà này.\n"
            . "4. Trả về dưới dạng một đối tượng JSON có cấu trúc cụ thể:\n"
            . "{\n"
            . "  \"emailTemplates\": [{\"subject\": \"Tiêu đề email\", \"content\": \"Nội dung chi tiết email\"}],\n"
            . "  \"smsTemplates\": [\"Nội dung SMS 1\", \"Nội dung SMS 2\", \"Nội dung SMS 3\"],\n"
            . "  \"prompts\": [\"Prompt tiếng Anh 1\", \"Prompt tiếng Anh 2\"]\n"
            . "}";

        $prompt = "Thông tin bất động sản:\n{$details}\n\nMục tiêu chiến dịch: {$goal}\nTone giọng: {$tone}\n\nHãy sinh nội dung Email, SMS và Prompts ảnh.";

        return $this->callGemini($systemInstruction, $prompt, 4096);
    }

    /**
     * AI Content Studio - Tạo gộp trọn bộ từ thông tin nhập tự do của người dùng.
     */
    public function generateContentStudio(array $inputs): array
    {
        $systemInstruction = "Bạn là Giám đốc Sáng tạo AI Content cho một đại lý bất động sản cao cấp. Dựa trên thông tin bất động sản do người dùng nhập tự do, hãy thiết kế một gói nội dung truyền thông toàn diện.\n\n"
            . "Gói nội dung bao gồm:\n"
            . "1. 3 bài đăng mạng xã hội (Facebook/Zalo) chất lượng cao.\n"
            . "2. 2 kịch bản video TikTok ngắn (gồm title, visual, audio, overlay).\n"
            . "3. 1 kịch bản lời thoại thu âm (Voiceover) liền mạch, KHÔNG chứa emoji hay ký tự đặc biệt, viết bằng văn nói tự nhiên trôi chảy để máy đọc (Text-to-Speech) phát âm tiếng Việt chuẩn xác (độ dài 120-180 từ).\n"
            . "4. 1 đoạn Prompt vẽ ảnh bằng tiếng Anh (Dùng tạo Thumbnail căn hộ, mô tả không gian thực tế, ánh sáng, góc chụp rộng, chất lượng photorealistic, cinematic).\n"
            . "5. Danh sách 10 hashtags thịnh hành liên quan.\n"
            . "6. 1 bài viết chuẩn SEO Website ngắn (tiêu đề, mô tả meta, nội dung HTML) kèm theo danh sách 5 từ khóa SEO đề xuất.\n\n"
            . "Trả về bắt buộc là một đối tượng JSON chuẩn có cấu trúc chính xác sau:\n"
            . "{\n"
            . "  \"posts\": [{\"id\": 1, \"title\": \"...\", \"content\": \"...\"}],\n"
            . "  \"videos\": [{\"id\": 1, \"title\": \"...\", \"visual\": \"...\", \"audio\": \"...\", \"overlay\": \"...\"}],\n"
            . "  \"voice_script\": \"nội dung văn bản thoại thu âm...\",\n"
            . "  \"thumbnail_prompt\": \"Đoạn prompt tiếng Anh để sinh ảnh thumbnail...\",\n"
            . "  \"hashtags\": [\"tag1\", \"tag2\", ...],\n"
            . "  \"seo\": {\n"
            . "     \"title\": \"Tiêu đề bài SEO\",\n"
            . "     \"meta\": \"Mô tả meta\",\n"
            . "     \"content\": \"Nội dung bài viết chuẩn SEO dạng HTML\",\n"
            . "     \"keywords\": [\"keyword1\", \"keyword2\", ...]\n"
            . "  }\n"
            . "}";

        // Định dạng thông tin đầu vào
        $details = sprintf(
            "Tên/Tiêu đề BĐS: %s\nLoại giao dịch: %s\nLoại hình: %s\nGiá: %s\nDiện tích: %s\nĐịa điểm: %s\nĐặc điểm nổi bật: %s\nTone giọng AI: %s",
            $inputs['title'] ?? 'Chưa đặt tên',
            ($inputs['transaction_type'] ?? 'rent') === 'rent' ? 'Cho thuê' : 'Bán',
            $inputs['property_type'] ?? 'Chung cư',
            $inputs['price'] ?? 'Thương lượng',
            $inputs['area'] ?? 'Chưa rõ',
            $inputs['address'] ?? 'Việt Nam',
            $inputs['highlights'] ?? 'Đầy đủ tiện nghi',
            $inputs['tone'] ?? 'Thân thiện'
        );

        $prompt = "Thông tin nhập vào từ người dùng:\n{$details}\n\nHãy tạo gói nội dung AI Content Studio.";

        return $this->callGemini($systemInstruction, $prompt, 8192);
    }
}

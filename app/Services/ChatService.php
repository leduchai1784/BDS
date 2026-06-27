<?php

namespace App\Services;

use App\Services\PropertyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected PropertyService $propertyService;
    protected string $apiKey;
    protected string $model;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
        $this->apiKey = config('services.gemini.key', '');
        $this->model = config('services.gemini.model', 'gemini-3.1-flash-lite');
    }

    /**
     * Xử lý tin nhắn chat của người dùng và trả về phản hồi từ AI kèm theo BĐS gợi ý.
     *
     * @param string $message
     * @param array $history
     * @return array
     */
    public function chat(string $message, array $history = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'reply'      => 'Hệ thống chưa cấu hình API Key cho Chatbot AI. Vui lòng liên hệ quản trị viên.',
                'properties' => [],
            ];
        }

        // 1. Lấy danh sách BĐS từ NKS API (đã được cache ở PropertyService)
        $allProperties = $this->propertyService->getApiOnlyProperties();

        // 2. Tạo danh sách BĐS rút gọn để gửi làm ngữ cảnh cho Gemini
        $compactProperties = array_map(function ($p) {
            return [
                'id'       => $p['id'],
                'title'    => $p['title'],
                'type'     => $p['type'],
                'price'    => $p['price'],
                'area'     => $p['area'] . 'm2',
                'location' => $p['location'],
                'district' => $p['district'],
            ];
        }, $allProperties);

        // 3. Xây dựng prompt hệ thống (System Instruction)
        $systemInstruction = "Bạn là Trợ lý ảo AI của BDS NKS, chuyên tư vấn và gợi ý bất động sản cho thuê tại Việt Nam.\n"
            . "Hãy trả lời một cách tự nhiên, lịch sự, thân thiện bằng tiếng Việt và hỗ trợ khách hàng tìm kiếm bất động sản phù hợp.\n\n"
            . "Dưới đây là danh sách bất động sản hiện có trong hệ thống (dữ liệu từ API NKS):\n"
            . json_encode($compactProperties, JSON_UNESCAPED_UNICODE) . "\n\n"
            . "Nhiệm vụ của bạn:\n"
            . "1. Trả lời câu hỏi của người dùng và tư vấn dựa trên nhu cầu của họ (khu vực, giá cả, loại hình, diện tích).\n"
            . "2. Chọn lọc và gợi ý các bất động sản phù hợp nhất từ danh sách trên (tối đa 3 BĐS).\n"
            . "3. Ở cuối phản hồi, bạn bắt buộc phải đính kèm danh sách các ID của những bất động sản mà bạn gợi ý cho khách hàng trong thẻ XML sau: <recommendations>[ID1, ID2, ...]</recommendations>.\n"
            . "Ví dụ: Nếu gợi ý căn hộ có ID 123 và 456, hãy viết ở cuối phản hồi là: <recommendations>[123, 456]</recommendations>\n"
            . "Nếu không tìm thấy bất động sản nào phù hợp, hãy trả lời lịch sự và để trống recommendations: <recommendations>[]</recommendations>";

        // 4. Định dạng lịch sử chat và tin nhắn hiện tại theo cấu trúc của Gemini API
        $contents = [];
        
        // Thêm lịch sử chat
        foreach ($history as $chat) {
            $role = $chat['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => $chat['content']]],
            ];
        }

        // Thêm tin nhắn hiện tại
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $message]],
        ];

        // 5. Gửi request đến Gemini API
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
            ->timeout(15)
            ->post($url, [
                'contents'          => $contents,
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'generationConfig' => [
                    'temperature'     => 0.3,
                    'topP'            => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error Response: ' . $response->body());
                return [
                    'reply'      => 'Không thể kết nối với trí tuệ nhân tạo lúc này. Vui lòng thử lại sau ít phút.',
                    'properties' => [],
                ];
            }

            $json = $response->json();
            $replyText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // 6. Trích xuất recommended IDs từ phản hồi của AI
            $recommendedIds = [];
            if (preg_match('/<recommendations>\[(.*?)\]<\/recommendations>/s', $replyText, $matches)) {
                $idsString = $matches[1];
                if (!empty(trim($idsString))) {
                    $recommendedIds = array_map('intval', explode(',', $idsString));
                }
                
                // Loại bỏ thẻ XML khỏi tin nhắn trả về cho người dùng để giữ giao diện sạch đẹp
                $replyText = preg_replace('/<recommendations>.*?<\/recommendations>/s', '', $replyText);
            }

            // Trích xuất thêm phòng hờ nếu AI viết dạng JSON ngoài thẻ
            $replyText = trim($replyText);

            // 7. Lấy thông tin đầy đủ của các BĐS được gợi ý
            $recommendedProperties = [];
            if (!empty($recommendedIds)) {
                foreach ($allProperties as $prop) {
                    if (in_array((int)$prop['id'], $recommendedIds, true)) {
                        $recommendedProperties[] = $prop;
                    }
                }
            }

            return [
                'reply'      => $replyText,
                'properties' => array_slice($recommendedProperties, 0, 3), // Giới hạn tối đa 3 BĐS
            ];

        } catch (\Exception $e) {
            Log::error('Gemini API Exception in ChatService: ' . $e->getMessage());
            return [
                'reply'      => 'Đã xảy ra sự cố khi trao đổi với trợ lý ảo. Xin lỗi vì sự bất tiện này.',
                'properties' => [],
            ];
        }
    }
}

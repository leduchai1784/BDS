<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Xử lý yêu cầu chat từ người dùng.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chat(Request $request): JsonResponse
    {
        try {
            // Rate limiting: tối đa 30 request/phút cho mỗi IP
            $key = 'chat:' . ($request->user()?->id ?? $request->ip());

            if (RateLimiter::tooManyAttempts($key, 30)) {
                $seconds = RateLimiter::availableIn($key);

                return response()->json([
                    'success' => false,
                    'reply'   => "Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau {$seconds} giây.",
                ], 429);
            }

            RateLimiter::hit($key, 60);

            // Validate dữ liệu đầu vào
            $validated = $request->validate([
                'message'           => 'required|string|max:500',
                'history'           => 'nullable|array|max:20',
                'history.*.role'    => 'nullable|string|in:user,assistant,model',
                'history.*.content' => 'nullable|string|max:1000',
            ]);

            $message = $validated['message'];
            $history = $validated['history'] ?? [];

            // Gọi ChatService để xử lý
            $result = $this->chatService->chat($message, $history);

            return response()->json([
                'success'    => true,
                'reply'      => $result['reply'] ?? '',
                'properties' => $result['properties'] ?? [],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'reply'   => 'Dữ liệu không hợp lệ: ' . collect($e->errors())->flatten()->first(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('ChatController error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'reply'   => 'Xin lỗi, đã xảy ra lỗi khi xử lý tin nhắn. Vui lòng thử lại sau.',
            ], 500);
        }
    }
}

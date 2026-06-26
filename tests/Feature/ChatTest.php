<?php

namespace Tests\Feature;

use App\Services\ChatService;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ChatTest extends TestCase
{
    /**
     * Test chat API validation error when message is missing.
     */
    public function test_chat_api_requires_message(): void
    {
        $response = $this->postJson(route('api.chat'), []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure(['success', 'reply']);
    }

    /**
     * Test chat API message validation rules (max length).
     */
    public function test_chat_api_validation_limits(): void
    {
        $response = $this->postJson(route('api.chat'), [
            'message' => str_repeat('a', 501), // exceeds 500 characters limit
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test chat API successfully calls ChatService and returns correct format.
     */
    public function test_chat_api_returns_success_response(): void
    {
        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->with('Tìm nhà Quận 7', [])
                ->andReturn([
                    'reply' => 'Tôi đề xuất các bất động sản tại Quận 7 sau:',
                    'properties' => [
                        [
                            'id' => 1,
                            'title' => 'Căn hộ Vinhomes Quận 7',
                            'price' => '10 triệu/tháng',
                            'area' => 60,
                            'location' => 'Quận 7, TP.HCM',
                        ]
                    ]
                ]);
        });

        $response = $this->postJson(route('api.chat'), [
            'message' => 'Tìm nhà Quận 7',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'reply' => 'Tôi đề xuất các bất động sản tại Quận 7 sau:',
                'properties' => [
                    [
                        'id' => 1,
                        'title' => 'Căn hộ Vinhomes Quận 7',
                        'price' => '10 triệu/tháng',
                        'area' => 60,
                        'location' => 'Quận 7, TP.HCM',
                    ]
                ]
            ]);
    }

    /**
     * Test rate limiting.
     */
    public function test_chat_api_rate_limiting(): void
    {
        RateLimiter::shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(true);

        RateLimiter::shouldReceive('availableIn')
            ->once()
            ->andReturn(35);

        $response = $this->postJson(route('api.chat'), [
            'message' => 'Hello',
        ]);

        $response->assertStatus(429)
            ->assertJson([
                'success' => false,
                'reply' => 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau 35 giây.',
            ]);
    }
}

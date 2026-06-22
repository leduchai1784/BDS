<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NksAuthService
{
    protected string $baseUrl = 'https://account.nks.vn/api/nks/user';

    /**
     * Đăng nhập bằng tài khoản NKS.
     * Trả về mảng ['success' => bool, 'token' => string, 'user' => array, 'message' => string]
     */
    public function login(string $email, string $password): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->post("{$this->baseUrl}/login", [
                    'email'    => $email,
                    'password' => $password,
                ]);

            $json = $response->json();

            if ($response->successful() && !empty($json['success']) && !empty($json['data']['access_token'])) {
                return [
                    'success' => true,
                    'token'   => $json['data']['access_token'],
                    'user'    => $json['data']['user'],
                    'message' => $json['message'] ?? 'Đăng nhập thành công.',
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'Thông tin đăng nhập NKS không chính xác.',
            ];
        } catch (\Exception $e) {
            Log::warning('NKS login failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể kết nối đến máy chủ NKS. Vui lòng thử lại sau.',
            ];
        }
    }

    /**
     * Cập nhật thông tin cá nhân lên NKS.
     */
    public function updateInfo(string $token, array $data): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withToken($token)
                ->post("{$this->baseUrl}/updateInfo", $data);

            $json = $response->json();

            return [
                'success' => $response->successful() && !empty($json['success']),
                'message' => $json['message'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::warning('NKS updateInfo failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cập nhật avatar lên NKS.
     */
    public function updateAvatar(string $token, UploadedFile $file): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withToken($token)
                ->attach('avatar', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("{$this->baseUrl}/updateAvatar");

            $json = $response->json();

            return [
                'success' => $response->successful() && !empty($json['success']),
                'message' => $json['message'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::warning('NKS updateAvatar failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cập nhật thông tin CCCD lên NKS.
     */
    public function updateCccd(string $token, array $data): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withToken($token)
                ->post("{$this->baseUrl}/updateCccd", $data);

            $json = $response->json();

            return [
                'success' => $response->successful() && !empty($json['success']),
                'message' => $json['message'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::warning('NKS updateCccd failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Map NKS user array sang data để tạo/cập nhật User local.
     */
    public function mapNksUserToLocal(array $nksUser, string $token): array
    {
        $name = trim(($nksUser['firstname'] ?? '') . ' ' . ($nksUser['lastname'] ?? ''));
        if (empty($name)) {
            $name = $nksUser['name'] ?? 'NKS User';
        }

        return [
            'name'         => $name,
            'phone'        => $nksUser['phone'] ?? null,
            'avatar'       => $nksUser['avatar'] ?? null,
            'nks_user_id'  => (string) ($nksUser['id'] ?? ''),
            'nks_token'    => $token,
        ];
    }
}

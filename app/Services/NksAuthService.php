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
                    'username' => $email,
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
            $payload = array_merge($data, ['access_token' => $token]);

            $response = Http::withoutVerifying()
                ->timeout(10)
                ->post("{$this->baseUrl}/updateInfo", $payload);

            $json = $response->json();

            Log::info('NKS updateInfo Request:', [
                'url' => "{$this->baseUrl}/updateInfo",
                'payload' => $payload,
                'status' => $response->status(),
                'response' => $json
            ]);

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
    public function updateAvatar(string $token, string $base64Data): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->post("{$this->baseUrl}/updateAvatar", [
                    'avatar' => $base64Data,
                    'access_token' => $token
                ]);

            $json = $response->json();

            Log::info('NKS updateAvatar Request:', [
                'url' => "{$this->baseUrl}/updateAvatar",
                'status' => $response->status(),
                'response' => $json
            ]);

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
                ->timeout(20)
                ->post("{$this->baseUrl}/updateCccd", [
                    'front'        => $data['cccd_front'] ?? '',
                    'back'         => $data['cccd_back'] ?? '',
                    'number'       => $data['id_number'] ?? '',
                    'date'         => $data['id_date'] ?? '',
                    'place'        => $data['id_place'] ?? '',
                    'access_token' => $token
                ]);

            $json = $response->json();

            Log::info('NKS updateCccd Request:', [
                'url' => "{$this->baseUrl}/updateCccd",
                'status' => $response->status(),
                'response' => $json
            ]);

            return [
                'success' => $response->successful() && !empty($json['success']),
                'message' => $json['message'] ?? '',
                'data'    => $json['data'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::warning('NKS updateCccd failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Lấy thông tin chi tiết người dùng từ NKS.
     */
    public function getUserInfo(string $token): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->post("{$this->baseUrl}", [
                    'access_token' => $token
                ]);

            if ($response->successful()) {
                $json = $response->json();
                if (!empty($json['success']) && !empty($json['data'])) {
                    return [
                        'success' => true,
                        'user'    => $json['data']
                    ];
                }
            }
            return ['success' => false];
        } catch (\Exception $e) {
            Log::warning('NKS getUserInfo failed: ' . $e->getMessage());
            return ['success' => false];
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

        $dob = $nksUser['dob'] ?? null;
        if (!empty($dob) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            try {
                $dob = \Carbon\Carbon::createFromFormat('Y-m-d', $dob)->format('d/m/Y');
            } catch (\Exception $e) {}
        }

        $idDate = $nksUser['id_date'] ?? null;
        if (!empty($idDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $idDate)) {
            try {
                $idDate = \Carbon\Carbon::createFromFormat('Y-m-d', $idDate)->format('d/m/Y');
            } catch (\Exception $e) {}
        }

        return [
            'name'         => $name,
            'firstname'    => $nksUser['firstname'] ?? null,
            'lastname'     => $nksUser['lastname'] ?? null,
            'phone'        => $nksUser['phone'] ?? null,
            'avatar'       => $nksUser['avatar'] ?? null,
            'nks_user_id'  => (string) ($nksUser['id'] ?? ''),
            'nks_token'    => $token,
            'gender'       => $nksUser['gender'] ?? 0,
            'dob'          => $dob,
            'pob'          => $nksUser['pob'] ?? null,
            'id_number'    => $nksUser['id_number'] ?? null,
            'id_date'      => $idDate,
            'id_place'     => $nksUser['id_place'] ?? null,
            'cccd_front'   => $nksUser['cccd_front'] ?? null,
            'cccd_back'    => $nksUser['cccd_back'] ?? null,
            'add_street'   => $nksUser['add_street'] ?? null,
            'add_ward'     => $nksUser['add_ward'] ?? null,
            'add_district' => $nksUser['add_district'] ?? null,
            'add_province' => $nksUser['add_province'] ?? null,
            'permanent_address' => $nksUser['permanent_address'] ?? null,
            'zalo_id'      => $nksUser['zalo_id'] ?? null,
            'zalo_key'     => $nksUser['zalo_key'] ?? null,
            'intro'        => $nksUser['intro'] ?? null,
            'website'      => $nksUser['website'] ?? null,
        ];
    }
}

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
    public function updateAvatar(string $token, UploadedFile $file): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->attach('avatar', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("{$this->baseUrl}/updateAvatar", [
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
            $request = Http::withoutVerifying()
                ->timeout(20);

            $hasFiles = false;
            $multipartData = [];

            foreach ($data as $key => $value) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $hasFiles = true;
                    $request->attach($key, file_get_contents($value->getRealPath()), $value->getClientOriginalName());
                } else {
                    $multipartData[$key] = $value;
                }
            }

            $multipartData['access_token'] = $token;

            if ($hasFiles) {
                $response = $request->post("{$this->baseUrl}/updateCccd", $multipartData);
            } else {
                $response = Http::withoutVerifying()
                    ->timeout(20)
                    ->post("{$this->baseUrl}/updateCccd", $multipartData);
            }

            $json = $response->json();

            Log::info('NKS updateCccd Request:', [
                'url' => "{$this->baseUrl}/updateCccd",
                'hasFiles' => $hasFiles,
                'payload' => $multipartData,
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
            'firstname'    => $nksUser['firstname'] ?? null,
            'lastname'     => $nksUser['lastname'] ?? null,
            'phone'        => $nksUser['phone'] ?? null,
            'avatar'       => $nksUser['avatar'] ?? null,
            'nks_user_id'  => (string) ($nksUser['id'] ?? ''),
            'nks_token'    => $token,
            'gender'       => $nksUser['gender'] ?? 0,
            'dob'          => $nksUser['dob'] ?? null,
            'pob'          => $nksUser['pob'] ?? null,
            'id_number'    => $nksUser['id_number'] ?? null,
            'id_date'      => $nksUser['id_date'] ?? null,
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

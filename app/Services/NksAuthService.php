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
            // First, fetch current NKS user details to prevent partial updates from resetting/nullifying other fields
            $currentNksData = [];
            $nksInfo = $this->getUserInfo($token);
            if ($nksInfo['success'] && !empty($nksInfo['user'])) {
                $currentNksData = $nksInfo['user'];
            }

            // Updatable fields on NKS
            $updatableKeys = [
                'name', 'firstname', 'lastname', 'phone', 'email', 'gender', 'dob', 'pob',
                'id_number', 'id_date', 'id_place', 'permanent_address',
                'add_street', 'add_ward', 'add_district', 'add_province',
                'intro', 'website', 'zalo_id', 'zalo_key'
            ];

            // Build merged payload
            $mergedData = [];
            foreach ($updatableKeys as $key) {
                if (array_key_exists($key, $currentNksData)) {
                    $mergedData[$key] = $currentNksData[$key];
                }
            }

            // If NKS user fetch failed or returned empty, we can fallback to the local authenticated user's data to merge
            if (empty($mergedData) && auth()->check()) {
                $localUser = auth()->user();
                if ($localUser->nks_token === $token) {
                    $localUserMap = $this->mapLocalUserToNks($localUser);
                    foreach ($updatableKeys as $key) {
                        if (array_key_exists($key, $localUserMap) && !is_null($localUserMap[$key])) {
                            $mergedData[$key] = $localUserMap[$key];
                        }
                    }
                }
            }

            // Overwrite with the newly provided update data
            foreach ($data as $key => $val) {
                if (in_array($key, $updatableKeys)) {
                    $mergedData[$key] = $val;
                }
            }

            // Sanitize values
            if (isset($mergedData['id_place'])) {
                $mergedData['id_place'] = $this->sanitizeNksString($mergedData['id_place'], 50);
            }
            if (isset($mergedData['pob'])) {
                $mergedData['pob'] = $this->sanitizeNksString($mergedData['pob'], 100);
            }

            $payload = array_merge($mergedData, ['access_token' => $token]);

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
            $idPlace = $this->sanitizeNksString($data['id_place'] ?? $data['place'] ?? '', 50);
            $response = Http::withoutVerifying()
                ->timeout(20)
                ->post("{$this->baseUrl}/updateCccd", [
                    'front'        => $data['cccd_front'] ?? '',
                    'back'         => $data['cccd_back'] ?? '',
                    'number'       => $data['id_number'] ?? '',
                    'date'         => $data['id_date'] ?? '',
                    'place'        => $idPlace,
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

    /**
     * Sanitize and truncate a string to fit NKS API limits.
     */
    private function sanitizeNksString(?string $str, int $maxLength = 50): string
    {
        if (empty($str)) {
            return '';
        }
        
        $str = trim($str);
        
        // Map common long Vietnamese ID places to standard short forms
        if ($maxLength === 50) {
            $lowerStr = mb_strtolower($str);
            if (str_contains($lowerStr, 'cục cảnh sát quản lý hành chính về trật tự xã hội') || 
                str_contains($lowerStr, 'cục cảnh sát qlhc về ttxh')) {
                return 'Cục Cảnh sát QLHC về TTXH';
            }
            if (str_contains($lowerStr, 'cục cảnh sát đăng ký quản lý cư trú và dữ liệu quốc gia về dân cư') || 
                str_contains($lowerStr, 'cục cảnh sát đkql cư trú và dlqg về dân cư')) {
                return 'Cục Cảnh sát ĐKQL cư trú & DLQG dân cư';
            }
        }
        
        if (mb_strlen($str) > $maxLength) {
            return mb_substr($str, 0, $maxLength);
        }
        
        return $str;
    }

    /**
     * Map local User model fields back to NKS API keys.
     */
    public function mapLocalUserToNks($user): array
    {
        $dob = $user->dob;
        if (!empty($dob) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dob)) {
            try {
                $dob = \Carbon\Carbon::createFromFormat('d/m/Y', $dob)->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        $idDate = $user->id_date;
        if (!empty($idDate) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $idDate)) {
            try {
                $idDate = \Carbon\Carbon::createFromFormat('d/m/Y', $idDate)->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        return [
            'name'              => $user->name,
            'firstname'         => $user->firstname,
            'lastname'          => $user->lastname,
            'phone'             => $user->phone,
            'email'             => $user->email,
            'gender'            => $user->gender,
            'dob'               => $dob,
            'pob'               => $user->pob,
            'id_number'         => $user->id_number,
            'id_date'           => $idDate,
            'id_place'          => $user->id_place,
            'permanent_address' => $user->permanent_address,
            'add_street'        => $user->add_street,
            'add_ward'          => $user->add_ward,
            'add_district'      => $user->add_district,
            'add_province'      => $user->add_province,
            'zalo_id'           => $user->zalo_id,
            'zalo_key'          => $user->zalo_key,
            'intro'             => $user->intro,
            'website'           => $user->website,
        ];
    }
}

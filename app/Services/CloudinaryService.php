<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected ?string $cloudName;
    protected ?string $apiKey;
    protected ?string $apiSecret;

    public function __construct()
    {
        $this->cloudName = env('CLOUDINARY_CLOUD_NAME') ? trim(env('CLOUDINARY_CLOUD_NAME')) : null;
        $this->apiKey = env('CLOUDINARY_API_KEY') ? trim(env('CLOUDINARY_API_KEY')) : null;
        $this->apiSecret = env('CLOUDINARY_API_SECRET') ? trim(env('CLOUDINARY_API_SECRET')) : null;
    }

    /**
     * Upload a file to Cloudinary.
     * Returns secure_url if successful, null otherwise.
     */
    public function upload(UploadedFile $file, string $folder = 'properties'): ?string
    {
        if (empty($this->cloudName) || empty($this->apiKey) || empty($this->apiSecret)) {
            Log::warning('Cloudinary credentials are not fully configured in env.');
            return null;
        }

        try {
            $timestamp = time();
            $params = [
                'folder' => $folder,
                'timestamp' => $timestamp,
                'public_id' => 'img_' . time() . '_' . rand(1000, 9999),
                'display_name' => 'img_' . time(),
            ];
            
            // Generate signature
            ksort($params);
            $sigString = '';
            foreach ($params as $key => $value) {
                $sigString .= "{$key}={$value}&";
            }
            $sigString = rtrim($sigString, '&') . $this->apiSecret;
            $signature = sha1($sigString);

            // Make API request using real path of UploadedFile
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", array_merge($params, [
                    'api_key' => $this->apiKey,
                    'signature' => $signature,
                ]));

            if ($response->successful()) {
                $data = $response->json();
                return $data['secure_url'] ?? null;
            }

            Log::error('Cloudinary upload error response: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Cloudinary upload exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload base64 image data to Cloudinary.
     * Returns secure_url if successful, null otherwise.
     */
    public function uploadBase64(string $base64Data, string $folder = 'users'): ?string
    {
        if (empty($this->cloudName) || empty($this->apiKey) || empty($this->apiSecret)) {
            Log::warning('Cloudinary credentials are not fully configured in env.');
            return null;
        }

        try {
            $timestamp = time();
            $params = [
                'folder' => $folder,
                'timestamp' => $timestamp,
                'public_id' => 'img_' . time() . '_' . rand(1000, 9999),
                'display_name' => 'img_' . time(),
            ];
            
            // Generate signature
            ksort($params);
            $sigString = '';
            foreach ($params as $key => $value) {
                $sigString .= "{$key}={$value}&";
            }
            $sigString = rtrim($sigString, '&') . $this->apiSecret;
            $signature = sha1($sigString);

            // Make API request with base64 data passed directly as file parameter
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asForm()
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", array_merge($params, [
                    'file' => $base64Data,
                    'api_key' => $this->apiKey,
                    'signature' => $signature,
                ]));

            if ($response->successful()) {
                $data = $response->json();
                return $data['secure_url'] ?? null;
            }

            Log::error('Cloudinary base64 upload error response: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Cloudinary base64 upload exception: ' . $e->getMessage());
            return null;
        }
    }
}

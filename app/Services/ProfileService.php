<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    /**
     * Update user profile information.
     */
    public function updateProfile(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'name' => $data['name'],
            'firstname' => $data['firstname'] ?? null,
            'lastname' => $data['lastname'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? $user->email,
            'gender' => $data['gender'] ?? 0,
            'dob' => $data['dob'] ?? null,
            'pob' => $data['pob'] ?? null,
            'add_street' => $data['add_street'] ?? null,
            'add_ward' => $data['add_ward'] ?? null,
            'add_district' => $data['add_district'] ?? null,
            'add_province' => $data['add_province'] ?? null,
            'zalo_id' => $data['zalo_id'] ?? null,
            'zalo_key' => $data['zalo_key'] ?? null,
            'intro' => $data['intro'] ?? null,
            'website' => $data['website'] ?? null,
        ]);

        return $user;
    }

    /**
     * Update user CCCD details locally.
     */
    public function updateCccd(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);

        $updateData = [
            'id_number' => $data['id_number'] ?? null,
            'id_date' => $data['id_date'] ?? null,
            'id_place' => $data['id_place'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
        ];

        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['firstname'])) $updateData['firstname'] = $data['firstname'];
        if (isset($data['lastname'])) $updateData['lastname'] = $data['lastname'];
        if (isset($data['gender'])) $updateData['gender'] = $data['gender'];
        if (isset($data['dob'])) $updateData['dob'] = $data['dob'];
        if (isset($data['pob'])) $updateData['pob'] = $data['pob'];

        if (!empty($data['cccd_front'])) {
            $updateData['cccd_front'] = $data['cccd_front'];
        }
        if (!empty($data['cccd_back'])) {
            $updateData['cccd_back'] = $data['cccd_back'];
        }

        $user->update($updateData);

        return $user;
    }

    /**
     * Upload and update user avatar.
     */
    public function updateAvatar(int $userId, string $base64Data): string
    {
        $user = User::findOrFail($userId);

        // Delete old avatar file if it exists and is not a default UI avatar
        if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com') && !str_contains($user->avatar, 'http')) {
            $oldPath = public_path($user->avatar);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Decode base64
        if (preg_match('/^data:image\/(\w+);base64,(.+)$/i', $base64Data, $matches)) {
            $imageType = $matches[1];
            $decodedData = base64_decode($matches[2]);
        } else {
            $imageType = 'jpg';
            $decodedData = base64_decode($base64Data);
        }

        // Store new avatar in public/uploads/avatars directory
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $imageType;
        $dir = public_path('uploads/avatars');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($dir . '/' . $filename, $decodedData);
        $avatarPath = 'uploads/avatars/' . $filename;

        // Update database
        $user->update([
            'avatar' => $avatarPath
        ]);

        return $avatarPath;
    }

    /**
     * Change user password after verifying current password.
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = User::findOrFail($userId);

        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không chính xác.']
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }
}

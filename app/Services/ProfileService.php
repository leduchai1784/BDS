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
            'phone' => $data['phone'] ?? null,
            // Only update email if it changed, to avoid unique constraints
            'email' => $data['email'] ?? $user->email,
        ]);

        return $user;
    }

    /**
     * Upload and update user avatar.
     */
    public function updateAvatar(int $userId, UploadedFile $file): string
    {
        $user = User::findOrFail($userId);

        // Delete old avatar file if it exists and is not a default UI avatar
        if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com') && !str_contains($user->avatar, 'http')) {
            $oldPath = public_path($user->avatar);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Store new avatar in public/uploads/avatars directory
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/avatars'), $filename);
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

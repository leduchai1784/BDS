<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test guest can view login form.
     */
    public function test_guest_can_view_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Đăng nhập');
    }

    /**
     * Test user can log in with correct credentials.
     */
    public function test_user_can_login_with_correct_credentials(): void
    {
        $email = 'test.login.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user cannot log in with incorrect credentials.
     */
    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $email = 'test.login.fail.' . time() . '@nks.com.vn';
        User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test guest can view register form.
     */
    public function test_guest_can_view_register_form(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Đăng ký');
    }

    /**
     * Test guest can register.
     */
    public function test_guest_can_register(): void
    {
        $email = 'new.user.' . time() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => $email,
            'phone' => '0912345678',
            'role' => 'tenant',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'New User',
            'role' => 'tenant',
        ]);
        $this->assertGuest();
    }

    /**
     * Test route protection.
     */
    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    /**
     * Test logged in user can view profile.
     */
    public function test_logged_in_user_can_view_profile(): void
    {
        $email = 'test.profile.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /**
     * Test user can logout.
     */
    public function test_user_can_logout(): void
    {
        $email = 'test.logout.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test user can update profile.
     */
    public function test_user_can_update_profile(): void
    {
        $email = 'test.profile.update.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $newEmail = 'updated.profile.' . time() . '@nks.com.vn';
        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Nguyễn Văn Hùng Updated',
            'email' => $newEmail,
            'phone' => '0988888888',
        ]);

        $response->assertRedirect('/profile?tab=profile&subtab=info');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nguyễn Văn Hùng Updated',
            'email' => $newEmail,
            'phone' => '0988888888',
        ]);
    }

    /**
     * Test user can update password.
     */
    public function test_user_can_update_password(): void
    {
        $email = 'test.password.update.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $response = $this->actingAs($user)->post('/profile/password', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/profile?tab=profile&subtab=password');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /**
     * Test profile update preserves POB (Place of Birth) data.
     */
    public function test_user_profile_update_preserves_pob(): void
    {
        $email = 'test.profile.pob.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'pob' => 'Hải Phòng',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Nguyễn Văn Hùng Updated',
            'email' => $email,
            'phone' => '0988888888',
        ]);

        $response->assertRedirect('/profile?tab=profile&subtab=info');
        
        $user->refresh();
        $this->assertEquals('Hải Phòng', $user->pob);
    }

    /**
     * Test tenant can register as owner.
     */
    public function test_tenant_can_register_as_owner(): void
    {
        $email = 'tenant.reg.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Khách Hàng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $response = $this->actingAs($user)->post('/profile/register-owner', [
            'name' => 'Chủ Nhà Mới',
            'phone' => '0912345678',
            'company' => 'Công Ty Bất Động Sản NKS',
        ]);

        $response->assertRedirect('/profile?tab=profile');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('owner', $user->role);
        $this->assertEquals('Chủ Nhà Mới', $user->name);
        $this->assertEquals('0912345678', $user->phone);
        $this->assertEquals('Công Ty Bất Động Sản NKS', $user->company);
    }

    /**
     * Test non-tenant users cannot register as owner.
     */
    public function test_non_tenant_cannot_register_as_owner(): void
    {
        $email = 'admin.reg.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Admin User',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/profile/register-owner', [
            'name' => 'Admin Attempt',
            'phone' => '0912345678',
        ]);

        $response->assertSessionHasErrors(['role']);
        
        $user->refresh();
        $this->assertEquals('admin', $user->role);
    }
}

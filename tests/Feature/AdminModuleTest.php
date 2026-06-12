<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Property;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $tenantUser;
    protected User $ownerUser;
    protected User $lockedUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin User
        $this->adminUser = User::create([
            'name' => 'Test Admin',
            'email' => 'admin.test.' . time() . '@nks.com.vn',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Tenant User
        $this->tenantUser = User::create([
            'name' => 'Test Tenant',
            'email' => 'tenant.test.' . time() . '@nks.com.vn',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'status' => 'active',
        ]);

        // Owner User
        $this->ownerUser = User::create([
            'name' => 'Test Owner',
            'email' => 'owner.test.' . time() . '@nks.com.vn',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        // Locked User
        $this->lockedUser = User::create([
            'name' => 'Test Locked',
            'email' => 'locked.test.' . time() . '@nks.com.vn',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'status' => 'locked',
        ]);
    }

    /**
     * Test guest cannot access admin dashboard.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /**
     * Test tenant cannot access admin dashboard.
     */
    public function test_tenant_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->tenantUser)->get('/admin');
        $response->assertStatus(403);
    }

    /**
     * Test owner cannot access admin dashboard.
     */
    public function test_owner_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->ownerUser)->get('/admin');
        $response->assertStatus(403);
    }

    /**
     * Test admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /**
     * Test locked user is blocked during login.
     */
    public function test_locked_user_is_blocked_during_login(): void
    {
        $response = $this->post('/login', [
            'email' => $this->lockedUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test locked user is logged out when trying to access admin.
     */
    public function test_locked_user_is_logged_out_when_accessing_admin(): void
    {
        // Force authentication session for locked user
        $response = $this->actingAs($this->lockedUser)->get('/admin');
        
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Test admin can view users list and search.
     */
    public function test_admin_can_view_users_list_and_search(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee($this->tenantUser->name);

        // Search
        $responseSearch = $this->actingAs($this->adminUser)->get('/admin/users?search=' . urlencode($this->ownerUser->name));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee($this->ownerUser->name);
    }

    /**
     * Test admin can view user details.
     */
    public function test_admin_can_view_user_details(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users/' . $this->tenantUser->id);
        $response->assertStatus(200);
        $response->assertSee($this->tenantUser->name);
    }

    /**
     * Test admin can toggle user status.
     */
    public function test_admin_can_toggle_user_status(): void
    {
        // Toggle from active to locked
        $response = $this->actingAs($this->adminUser)->post('/admin/users/' . $this->tenantUser->id . '/toggle-status');
        $response->assertRedirect();
        
        $this->tenantUser->refresh();
        $this->assertEquals('locked', $this->tenantUser->status);

        // Toggle from locked to active
        $response2 = $this->actingAs($this->adminUser)->post('/admin/users/' . $this->tenantUser->id . '/toggle-status');
        $response2->assertRedirect();
        
        $this->tenantUser->refresh();
        $this->assertEquals('active', $this->tenantUser->status);
    }

    /**
     * Test admin cannot lock themselves.
     */
    public function test_admin_cannot_lock_themselves(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/users/' . $this->adminUser->id . '/toggle-status');
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->adminUser->refresh();
        $this->assertEquals('active', $this->adminUser->status);
    }

    /**
     * Test admin can view properties.
     */
    public function test_admin_can_view_properties(): void
    {
        $category = Category::create([
            'name' => 'Category Test ' . time(),
            'slug' => 'cat-test-' . time(),
            'description' => 'Desc',
        ]);

        $property = Property::create([
            'title' => 'House Test ' . time(),
            'type' => 'House',
            'price' => 10000000,
            'price_label' => '10tr',
            'area' => 50,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Hanoi',
            'district' => 'CG',
            'lat' => 21.0,
            'lng' => 105.0,
            'image' => 'images/house.png',
            'images' => ['images/house.png'],
            'direction' => 'South',
            'furniture' => 'Full',
            'legal' => 'Sổ đỏ',
            'is_vip' => false,
            'is_new' => true,
            'category_id' => $category->id,
            'status' => 'pending',
            'views' => 0,
            'agent_id' => $this->ownerUser->id,
            'description' => 'Test description',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/properties');
        $response->assertStatus(200);
        $response->assertSee($property->title);

        $responseDetail = $this->actingAs($this->adminUser)->get('/admin/properties/' . $property->id);
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee($property->title);
    }

    /**
     * Test admin can update property status.
     */
    public function test_admin_can_update_property_status(): void
    {
        $category = Category::create([
            'name' => 'Category Test ' . time(),
            'slug' => 'cat-test-' . time(),
            'description' => 'Desc',
        ]);

        $property = Property::create([
            'title' => 'House Test ' . time(),
            'type' => 'House',
            'price' => 10000000,
            'price_label' => '10tr',
            'area' => 50,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Hanoi',
            'district' => 'CG',
            'lat' => 21.0,
            'lng' => 105.0,
            'image' => 'images/house.png',
            'images' => ['images/house.png'],
            'category_id' => $category->id,
            'status' => 'pending',
            'agent_id' => $this->ownerUser->id,
            'description' => 'Test description',
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/properties/' . $property->id . '/status', [
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $property->refresh();
        $this->assertEquals('approved', $property->status);
    }

    /**
     * Test admin can delete a property.
     */
    public function test_admin_can_delete_property(): void
    {
        $category = Category::create([
            'name' => 'Category Test ' . time(),
            'slug' => 'cat-test-' . time(),
            'description' => 'Desc',
        ]);

        $property = Property::create([
            'title' => 'House Test ' . time(),
            'type' => 'House',
            'price' => 10000000,
            'price_label' => '10tr',
            'area' => 50,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Hanoi',
            'district' => 'CG',
            'lat' => 21.0,
            'lng' => 105.0,
            'image' => 'images/house.png',
            'images' => ['images/house.png'],
            'category_id' => $category->id,
            'status' => 'pending',
            'agent_id' => $this->ownerUser->id,
            'description' => 'Test description',
        ]);

        $response = $this->actingAs($this->adminUser)->delete('/admin/properties/' . $property->id);
        $response->assertRedirect();
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    /**
     * Test admin can view appointments and cancel.
     */
    public function test_admin_can_view_appointments_and_cancel(): void
    {
        $category = Category::create([
            'name' => 'Category Test ' . time(),
            'slug' => 'cat-test-' . time(),
            'description' => 'Desc',
        ]);

        $property = Property::create([
            'title' => 'House Test ' . time(),
            'type' => 'House',
            'price' => 10000000,
            'price_label' => '10tr',
            'area' => 50,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Hanoi',
            'district' => 'CG',
            'lat' => 21.0,
            'lng' => 105.0,
            'image' => 'images/house.png',
            'images' => ['images/house.png'],
            'category_id' => $category->id,
            'status' => 'approved',
            'agent_id' => $this->ownerUser->id,
            'description' => 'Test description',
        ]);

        $appointment = Appointment::create([
            'user_id' => $this->tenantUser->id,
            'property_id' => $property->id,
            'name' => 'Test Tenant',
            'phone' => '0912345678',
            'date' => '2026-06-15',
            'time' => '10:00:00',
            'message' => 'Hẹn xem nhà',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/appointments');
        $response->assertStatus(200);
        $response->assertSee($appointment->name);

        // Cancel appointment
        $responseCancel = $this->actingAs($this->adminUser)->post('/admin/appointments/' . $appointment->id . '/cancel');
        $responseCancel->assertRedirect();
        
        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);
    }

    /**
     * Test Category CRUD.
     */
    public function test_category_crud(): void
    {
        // 1. Create (Store)
        $categoryName = 'CRUD Test ' . time();
        $responseStore = $this->actingAs($this->adminUser)->post('/admin/categories', [
            'name' => $categoryName,
            'description' => 'CRUD Description',
        ]);
        $responseStore->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => $categoryName]);

        $category = Category::where('name', $categoryName)->first();

        // 2. Edit View
        $responseEdit = $this->actingAs($this->adminUser)->get('/admin/categories/' . $category->id . '/edit');
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee($categoryName);

        // 3. Update
        $updatedName = 'CRUD Updated ' . time();
        $responseUpdate = $this->actingAs($this->adminUser)->put('/admin/categories/' . $category->id, [
            'name' => $updatedName,
            'description' => 'Updated Description',
        ]);
        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => $updatedName]);
        $this->assertDatabaseMissing('categories', ['name' => $categoryName]);

        // 4. Delete
        $responseDelete = $this->actingAs($this->adminUser)->delete('/admin/categories/' . $category->id);
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test admin can view reports statistics.
     */
    public function test_admin_can_view_reports(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/reports');
        $response->assertStatus(200);
        $response->assertSee('Báo cáo thống kê');
    }

    /**
     * Test admin redirected to admin dashboard upon successful login.
     */
    public function test_admin_redirected_to_admin_dashboard_upon_login(): void
    {
        $response = $this->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    /**
     * Test admin can view profile page.
     */
    public function test_admin_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Ban quản trị');
        $response->assertSee('Quay lại Admin Panel');
        // Admin should not see Tenant or Owner specific tabs
        $response->assertDontSee('Lịch hẹn xem nhà');
    }

    /**
     * Test admin can update profile details.
     */
    public function test_admin_can_update_profile_details(): void
    {
        $updatedName = 'Admin Updated Name';
        $updatedEmail = 'admin.updated.' . time() . '@nks.com.vn';
        $updatedPhone = '0987654321';

        $response = $this->actingAs($this->adminUser)->post('/profile', [
            'name' => $updatedName,
            'email' => $updatedEmail,
            'phone' => $updatedPhone,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->adminUser->refresh();
        $this->assertEquals($updatedName, $this->adminUser->name);
        $this->assertEquals($updatedEmail, $this->adminUser->email);
        $this->assertEquals($updatedPhone, $this->adminUser->phone);
    }

    /**
     * Test admin can change password.
     */
    public function test_admin_can_change_password(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/profile/password', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->adminUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->adminUser->password));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $tenant;
    protected User $owner;
    protected User $otherTenant;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a category
        $category = Category::firstOrCreate(
            ['slug' => 'chung-cu-test'],
            ['name' => 'Chung cư Test']
        );

        // Create Owner
        $this->owner = User::firstOrCreate(
            ['email' => 'owner.test@nks.com.vn'],
            [
                'name' => 'Chủ nhà Test',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // Create Property belonging to Owner
        $this->property = Property::firstOrCreate(
            ['title' => 'Căn hộ chung cư cao cấp'],
            [
                'category_id' => $category->id,
                'price' => 10000000,
                'price_label' => '10 triệu/tháng',
                'area' => 50,
                'address' => 'Hà Nội',
                'ward' => 'Dịch Vọng',
                'district' => 'HN',
                'city' => 'Hà Nội',
                'latitude' => 21.0,
                'longitude' => 105.0,
                'owner_id' => $this->owner->id,
                'phone' => '0987654321',
                'status' => 'approved',
                'description' => 'Mô tả căn hộ',
            ]
        );

        // Create Tenant
        $this->tenant = User::firstOrCreate(
            ['email' => 'tenant.test@nks.com.vn'],
            [
                'name' => 'Khách Thuê Test',
                'password' => Hash::make('password'),
                'role' => 'tenant',
            ]
        );

        // Create Other Tenant
        $this->otherTenant = User::firstOrCreate(
            ['email' => 'tenant.other@nks.com.vn'],
            [
                'name' => 'Khách Thuê Khác',
                'password' => Hash::make('password'),
                'role' => 'tenant',
            ]
        );
    }

    /**
     * Test tenant can toggle property in wishlist.
     */
    public function test_tenant_can_toggle_wishlist(): void
    {
        // 1. Add to wishlist
        $response = $this->actingAs($this->tenant)->postJson(route('wishlist.toggle'), [
            'property_id' => $this->property->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => true,
        ]);

        $this->assertTrue($this->tenant->favoriteProperties()->where('property_id', $this->property->id)->exists());

        // 2. Remove from wishlist
        $response = $this->actingAs($this->tenant)->postJson(route('wishlist.toggle'), [
            'property_id' => $this->property->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => false,
        ]);

        $this->assertFalse($this->tenant->favoriteProperties()->where('property_id', $this->property->id)->exists());
    }

    /**
     * Test owner and admin can toggle wishlist.
     */
    public function test_owner_and_admin_can_toggle_wishlist(): void
    {
        // 1. Owner toggles wishlist (Add)
        $response = $this->actingAs($this->owner)->postJson(route('wishlist.toggle'), [
            'property_id' => $this->property->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => true,
        ]);
        $this->assertTrue($this->owner->favoriteProperties()->where('property_id', $this->property->id)->exists());

        // 2. Owner toggles wishlist again (Remove)
        $response = $this->actingAs($this->owner)->postJson(route('wishlist.toggle'), [
            'property_id' => $this->property->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => false,
        ]);
        $this->assertFalse($this->owner->favoriteProperties()->where('property_id', $this->property->id)->exists());

        // 3. Admin toggles wishlist (Add)
        $admin = User::firstOrCreate(
            ['email' => 'admin.test@nks.com.vn'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $response = $this->actingAs($admin)->postJson(route('wishlist.toggle'), [
            'property_id' => $this->property->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => true,
        ]);
        $this->assertTrue($admin->favoriteProperties()->where('property_id', $this->property->id)->exists());
    }

    /**
     * Test tenant can book an appointment.
     */
    public function test_tenant_can_book_appointment(): void
    {
        $response = $this->actingAs($this->tenant)->postJson(route('appointments.book'), [
            'property_id' => $this->property->id,
            'name' => 'Khách thuê',
            'phone' => '0987654321',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
            'message' => 'Tôi muốn xem nhà vào lúc 10h sáng.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('appointments', [
            'user_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'name' => 'Khách thuê',
            'phone' => '0987654321',
            'status' => 'pending',
        ]);
    }

    /**
     * Test non-tenant cannot book an appointment.
     */
    public function test_non_tenant_cannot_book_appointment(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('appointments.book'), [
            'property_id' => $this->property->id,
            'name' => 'Chủ nhà',
            'phone' => '0987654321',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test tenant can cancel their own appointment.
     */
    public function test_tenant_can_cancel_own_appointment(): void
    {
        $appointment = Appointment::create([
            'user_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'name' => 'Khách thuê',
            'phone' => '0987654321',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->tenant)->post(route('appointments.cancel', $appointment->id));

        $response->assertRedirect(route('profile.index', ['tab' => 'appointments']));
        $response->assertSessionHas('success', 'Hủy lịch hẹn thành công!');

        $appointment->refresh();
        $this->assertEquals('rejected', $appointment->status);
        $this->assertEquals('Khách thuê hủy lịch hẹn', $appointment->reject_reason);
    }

    /**
     * Test tenant cannot cancel someone else's appointment.
     */
    public function test_tenant_cannot_cancel_others_appointment(): void
    {
        $appointment = Appointment::create([
            'user_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'name' => 'Khách thuê',
            'phone' => '0987654321',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->otherTenant)->post(route('appointments.cancel', $appointment->id));

        $response->assertStatus(403);
    }
}

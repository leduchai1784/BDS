<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Property;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $owner1;
    protected User $owner2;
    protected User $tenant;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Owner 1
        $this->owner1 = User::create([
            'name' => 'Owner One',
            'email' => 'owner1.' . time() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        // Owner 2
        $this->owner2 = User::create([
            'name' => 'Owner Two',
            'email' => 'owner2.' . time() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        // Tenant
        $this->tenant = User::create([
            'name' => 'Renter User',
            'email' => 'renter.' . time() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'status' => 'active',
        ]);

        // Category
        $this->category = Category::create([
            'name' => 'Test Studio',
            'slug' => 'test-studio',
            'description' => 'Test Studio Description',
            'icon' => 'fa-home',
        ]);
    }

    /**
     * Test guest cannot access property create route.
     */
    public function test_guest_cannot_access_property_create(): void
    {
        $response = $this->get(route('properties.create'));
        $response->assertRedirect('/login');
    }

    /**
     * Test tenant cannot access property create route.
     */
    public function test_tenant_cannot_access_property_create(): void
    {
        $response = $this->actingAs($this->tenant)->get(route('properties.create'));
        $response->assertStatus(403);
    }

    /**
     * Test owner can access property create route.
     */
    public function test_owner_can_access_property_create(): void
    {
        $response = $this->actingAs($this->owner1)->get(route('properties.create'));
        $response->assertStatus(200);
        $response->assertSee('Đăng tin cho thuê mới');
    }

    /**
     * Test owner can store property with uploaded files.
     */
    public function test_owner_can_store_property(): void
    {
        $mainImage = UploadedFile::fake()->create('thumbnail.jpg', 100, 'image/jpeg');
        $extraImage1 = UploadedFile::fake()->create('gallery1.jpg', 100, 'image/jpeg');
        $extraImage2 = UploadedFile::fake()->create('gallery2.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->owner1)->post(route('properties.store'), [
            'title' => 'New Villa with Pool',
            'description' => 'Luxury villa description text here.',
            'price' => 15000000,
            'area' => 150,
            'address' => 'District 2, HCMC',
            'ward' => 'An Phú',
            'type' => 'Biệt thự / Villa',
            'district' => 'D2',
            'city' => 'Hồ Chí Minh',
            'latitude' => 10.7891,
            'longitude' => 106.6983,
            'phone' => '0987654321',
            'category_id' => $this->category->id,
            'image' => $mainImage,
            'images' => [$extraImage1, $extraImage2],
            'bedroom' => 3,
            'bathroom' => 3,
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'properties']));
        
        $this->assertDatabaseHas('properties', [
            'title' => 'New Villa with Pool',
            'price' => 15000000,
            'owner_id' => $this->owner1->id,
            'status' => 'approved'
        ]);

        $property = Property::where('title', 'New Villa with Pool')->first();
        $this->assertNotNull($property->image);
        $this->assertCount(2, $property->images);

        // Cleanup files
        if (file_exists(public_path($property->image))) {
            @unlink(public_path($property->image));
        }
        foreach ($property->images as $img) {
            if (file_exists(public_path($img))) {
                @unlink(public_path($img));
            }
        }
    }

    /**
     * Test owner can store property with image URLs.
     */
    public function test_owner_can_store_property_with_image_url(): void
    {
        $response = $this->actingAs($this->owner1)->post(route('properties.store'), [
            'title' => 'Property with URLs',
            'description' => 'Luxury villa description text here.',
            'price' => 20000000,
            'area' => 120,
            'address' => 'District 1, HCMC',
            'ward' => 'Bến Nghé',
            'type' => 'Biệt thự / Villa',
            'district' => 'D1',
            'city' => 'Hồ Chí Minh',
            'latitude' => 10.7791,
            'longitude' => 106.6983,
            'phone' => '0987654321',
            'category_id' => $this->category->id,
            'image_url' => 'https://res.cloudinary.com/test/image.jpg',
            'gallery_urls' => "https://res.cloudinary.com/test/gallery1.jpg\nhttps://res.cloudinary.com/test/gallery2.jpg",
            'bedroom' => 2,
            'bathroom' => 2,
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'properties']));
        
        $this->assertDatabaseHas('properties', [
            'title' => 'Property with URLs',
            'price' => 20000000,
            'owner_id' => $this->owner1->id,
            'status' => 'approved'
        ]);

        $property = Property::where('title', 'Property with URLs')->first();
        $this->assertEquals('https://res.cloudinary.com/test/image.jpg', $property->image);
        $this->assertCount(2, $property->images);
        $this->assertContains('https://res.cloudinary.com/test/gallery1.jpg', $property->images);
        $this->assertContains('https://res.cloudinary.com/test/gallery2.jpg', $property->images);
    }

    /**
     * Test owner can edit their own property.
     */
    public function test_owner_can_edit_own_property(): void
    {
        $property = Property::create([
            'title' => 'My Old Property',
            'description' => 'Description.',
            'price' => 5000000,
            'price_label' => '5tr',
            'area' => 40,
            'address' => 'Cau Giay, Hanoi',
            'ward' => 'Dịch Vọng',
            'district' => 'CG',
            'city' => 'Hà Nội',
            'latitude' => 21.036,
            'longitude' => 105.78,
            'category_id' => $this->category->id,
            'owner_id' => $this->owner1->id,
            'phone' => '0987654321',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->owner1)->get(route('properties.edit', $property->id));
        $response->assertStatus(200);
        $response->assertSee('Chỉnh sửa tin đăng');
        $response->assertSee('My Old Property');
    }

    /**
     * Test owner cannot edit other owner's property.
     */
    public function test_owner_cannot_edit_other_property(): void
    {
        $property = Property::create([
            'title' => 'Owner 2 Property',
            'description' => 'Description.',
            'price' => 5000000,
            'price_label' => '5tr',
            'area' => 40,
            'address' => 'Cau Giay, Hanoi',
            'ward' => 'Dịch Vọng',
            'district' => 'CG',
            'city' => 'Hà Nội',
            'latitude' => 21.036,
            'longitude' => 105.78,
            'category_id' => $this->category->id,
            'owner_id' => $this->owner2->id,
            'phone' => '0987654321',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->owner1)->get(route('properties.edit', $property->id));
        $response->assertStatus(403);
    }

    /**
     * Test owner can delete their own property.
     */
    public function test_owner_can_delete_own_property(): void
    {
        $property = Property::create([
            'title' => 'To Be Deleted',
            'description' => 'Description.',
            'price' => 5000000,
            'price_label' => '5tr',
            'area' => 40,
            'address' => 'Cau Giay, Hanoi',
            'ward' => 'Dịch Vọng',
            'district' => 'CG',
            'city' => 'Hà Nội',
            'latitude' => 21.036,
            'longitude' => 105.78,
            'category_id' => $this->category->id,
            'owner_id' => $this->owner1->id,
            'phone' => '0987654321',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->owner1)->delete(route('properties.destroy', $property->id));
        $response->assertRedirect(route('profile.index', ['tab' => 'properties']));
        
        $this->assertSoftDeleted('properties', [
            'id' => $property->id
        ]);
    }

    /**
     * Test owner can manage appointments.
     */
    public function test_owner_can_manage_appointments(): void
    {
        $property = Property::create([
            'title' => 'Property for Viewing',
            'description' => 'Description.',
            'price' => 5000000,
            'price_label' => '5tr',
            'area' => 40,
            'address' => 'Cau Giay, Hanoi',
            'ward' => 'Dịch Vọng',
            'district' => 'CG',
            'city' => 'Hà Nội',
            'latitude' => 21.036,
            'longitude' => 105.78,
            'category_id' => $this->category->id,
            'owner_id' => $this->owner1->id,
            'phone' => '0987654321',
            'status' => 'approved',
        ]);

        $appointment = Appointment::create([
            'user_id' => $this->tenant->id,
            'property_id' => $property->id,
            'name' => 'John Doe',
            'phone' => '0987654321',
            'date' => '2026-06-12',
            'time' => '10:00:00',
            'message' => 'Want to view',
            'status' => 'pending',
        ]);

        // 1. Test Approve
        $response = $this->actingAs($this->owner1)->post(route('appointments.approve', $appointment->id));
        $response->assertRedirect(route('profile.index', ['tab' => 'appointments']));
        $this->assertEquals('approved', $appointment->fresh()->status);

        // 2. Test Complete
        $response = $this->actingAs($this->owner1)->post(route('appointments.complete', $appointment->id));
        $response->assertRedirect(route('profile.index', ['tab' => 'appointments']));
        $this->assertEquals('completed', $appointment->fresh()->status);

        // 3. Test Reject with reason
        $appointment->update(['status' => 'pending']);
        $response = $this->actingAs($this->owner1)->post(route('appointments.reject', $appointment->id), [
            'reject_reason' => 'Chủ nhà bận đột xuất.'
        ]);
        $response->assertRedirect(route('profile.index', ['tab' => 'appointments']));
        $this->assertEquals('rejected', $appointment->fresh()->status);
        $this->assertEquals('Chủ nhà bận đột xuất.', $appointment->fresh()->reject_reason);
    }

    /**
     * Test owner cannot manage other owner's appointments.
     */
    public function test_owner_cannot_manage_other_appointments(): void
    {
        $property = Property::create([
            'title' => 'Other Property',
            'description' => 'Description.',
            'price' => 5000000,
            'price_label' => '5tr',
            'area' => 40,
            'address' => 'Cau Giay, Hanoi',
            'ward' => 'Dịch Vọng',
            'district' => 'CG',
            'city' => 'Hà Nội',
            'latitude' => 21.036,
            'longitude' => 105.78,
            'category_id' => $this->category->id,
            'owner_id' => $this->owner2->id,
            'phone' => '0987654321',
            'status' => 'approved',
        ]);

        $appointment = Appointment::create([
            'user_id' => $this->tenant->id,
            'property_id' => $property->id,
            'name' => 'John Doe',
            'phone' => '0987654321',
            'date' => '2026-06-12',
            'time' => '10:00:00',
            'message' => 'Want to view',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner1)->post(route('appointments.approve', $appointment->id));
        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test guest can toggle property in wishlist via cookies.
     */
    public function test_guest_can_toggle_wishlist_via_cookie(): void
    {
        $propertyId = 'd4f23b9d-4767-4e94-814d-5c5f8df607d2';

        // 1. Toggle like -> should add to cookie
        $response = $this->post(route('wishlist.toggle'), [
            'property_id' => $propertyId,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorite' => true,
        ]);

        // Get the raw cookie
        $cookie = $this->getRawCookie($response, 'guest_wishlist');
        $this->assertNotNull($cookie);
        
        $wishlist = json_decode($cookie->getValue(), true);
        $this->assertContains($propertyId, $wishlist);

        // 2. Toggle like again with the cookie -> should remove from cookie
        $response2 = $this->withUnencryptedCookies(['guest_wishlist' => json_encode([$propertyId])])
            ->post(route('wishlist.toggle'), [
                'property_id' => $propertyId,
            ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'is_favorite' => false,
        ]);
        
        // The cookie should be empty
        $cookie2 = $this->getRawCookie($response2, 'guest_wishlist');
        $this->assertNotNull($cookie2);
        $wishlist2 = json_decode($cookie2->getValue(), true);
        $this->assertEmpty($wishlist2);
    }

    /**
     * Test successful login merges guest wishlist from cookie to DB.
     */
    public function test_login_merges_cookie_wishlist_to_db(): void
    {
        // Create user
        $email = 'wishlist.test.' . time() . '@nks.com.vn';
        $user = User::create([
            'name' => 'Nguyễn Hải Đăng',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        // Create Category
        $category = Category::firstOrCreate(
            ['slug' => 'chung-cu-test'],
            ['name' => 'Chung cư Test']
        );

        // Create Owner
        $owner = User::firstOrCreate(
            ['email' => 'owner.test.wishlist@nks.com.vn'],
            [
                'name' => 'Chủ nhà Test Wishlist',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // Create a real DB property with a fresh UUID
        $propertyId = \Illuminate\Support\Str::uuid()->toString();
        $property = Property::create([
            'id' => $propertyId,
            'title' => 'Căn hộ Test Merger',
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
            'owner_id' => $owner->id,
            'phone' => '0987654321',
            'status' => 'approved',
            'description' => 'Mô tả căn hộ',
            'slug' => 'can-ho-chung-cu-test-wishlist-' . time(),
        ]);

        // Put the property ID in cookie
        $cookieValue = json_encode([$property->id]);

        // Log in with the cookie
        $response = $this->withUnencryptedCookies(['guest_wishlist' => $cookieValue])
            ->post('/login', [
                'email' => $email,
                'password' => 'password',
            ]);

        $response->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);

        // Verify the database contains the wishlist item
        $this->assertTrue($user->favoriteProperties()->where('property_id', $property->id)->exists());

        // Verify cookie is deleted (expires in the past)
        $cookie = $this->getRawCookie($response, 'guest_wishlist');
        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->getExpiresTime() < time());
    }

    /**
     * Helper to retrieve a raw cookie from response headers.
     */
    protected function getRawCookie($response, $name)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie;
            }
        }
        return null;
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertySearchTest extends TestCase
{
    /**
     * Test autocomplete suggestion endpoint.
     */
    public function test_autocomplete_api_returns_correct_suggestions(): void
    {
        // 1. Send query matching seeder values (e.g., 'Vinhomes')
        $response = $this->getJson('/api/properties/autocomplete?q=Vinhomes');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'type',
                'label',
                'sublabel',
                'value'
            ]
        ]);

        // 2. Query empty or short string
        $responseShort = $this->getJson('/api/properties/autocomplete?q=a');
        $responseShort->assertStatus(200);
        $responseShort->assertJsonCount(0);
    }

    /**
     * Test keyword searching.
     */
    public function test_listings_search_matches_keyword(): void
    {
        // Send a search request matching a seeded property title
        $response = $this->get('/listings?keyword=Vinhomes');

        $response->assertStatus(200);
        $response->assertSee('Vinhomes');
    }

    /**
     * Test advanced filtering options.
     */
    public function test_listings_advanced_filters(): void
    {
        // Filter by transaction type
        $response = $this->get('/listings?purpose=rent');
        $response->assertStatus(200);

        // Filter by property type
        $responseType = $this->get('/listings?property_type=apartment');
        $responseType->assertStatus(200);

        // Filter by price range
        $responsePrice = $this->get('/listings?price=under_3');
        $responsePrice->assertStatus(200);
    }

    /**
     * Test public wishlist index page.
     */
    public function test_wishlist_page_returns_successful_response(): void
    {
        $response = $this->get('/wishlist');
        $response->assertStatus(200);
        $response->assertSee('Tin Đăng Đã Lưu');
    }

    /**
     * Test render cards endpoint.
     */
    public function test_wishlist_render_endpoint(): void
    {
        // Get some property ids
        $propertyIds = Property::limit(2)->pluck('id')->toArray();
        if (empty($propertyIds)) {
            $propertyIds = [\Illuminate\Support\Str::uuid()->toString()];
        }

        $response = $this->postJson('/wishlist/render', [
            'ids' => $propertyIds
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'html']);
    }

    /**
     * Test sync wishlist endpoint for logged-in users.
     */
    public function test_wishlist_sync_endpoint(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $properties = Property::limit(2)->get();
        if ($properties->isEmpty()) {
            return; // skip if no properties seeded
        }
        $propertyIds = $properties->pluck('id')->toArray();

        $response = $this->actingAs($user)->postJson('/wishlist/sync', [
            'ids' => $propertyIds
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check database
        foreach ($propertyIds as $id) {
            $this->assertTrue($user->favoriteProperties()->where('property_id', $id)->exists());
        }
    }
}

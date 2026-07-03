<?php

namespace Tests\Feature;

use Tests\TestCase;

class NewsTest extends TestCase
{
    /**
     * Test that the news list page is accessible.
     */
    public function test_news_page_is_accessible(): void
    {
        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertSee('Tin Tức Bất Động Sản');
    }

    /**
     * Test that a valid news article detail page is accessible and shows its content.
     */
    public function test_news_detail_page_is_accessible_with_valid_slug(): void
    {
        $response = $this->get('/news/bao-cao-thi-truong-can-ho-cho-thue-tphcm-q2-2026');
        $response->assertStatus(200);
        $response->assertSee('Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026');
        $response->assertSee('Trong Quý 2 năm 2026');
    }

    /**
     * Test that an invalid news article slug returns a 404 page.
     */
    public function test_news_detail_page_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/news/invalid-news-slug-12345');
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;
use App\Models\ViewTracking;
use App\Services\ViewTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ViewTrackingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ViewTrackingService::class);
    }

    /** @test */
    public function tracks_comic_view()
    {
        $comic = Comic::factory()->create(['views_count' => 0]);

        $result = $this->service->trackComicView($comic);

        $this->assertTrue($result);
        $this->assertEquals(1, $comic->fresh()->views_count);
    }

    /** @test */
    public function tracks_chapter_view()
    {
        $chapter = Chapter::factory()->create(['views_count' => 0]);

        $result = $this->service->trackChapterView($chapter);

        $this->assertTrue($result);
        $this->assertEquals(1, $chapter->fresh()->views_count);
    }

    /** @test */
    public function respects_cooldown_period()
    {
        $comic = Comic::factory()->create(['views_count' => 0]);

        // First view should be tracked
        $result1 = $this->service->trackComicView($comic);
        $this->assertTrue($result1);
        $this->assertEquals(1, $comic->fresh()->views_count);

        // Second view within 30 seconds should be blocked
        $result2 = $this->service->trackComicView($comic);
        $this->assertFalse($result2);
        $this->assertEquals(1, $comic->fresh()->views_count);
    }

    /** @test */
    public function creates_view_tracking_record()
    {
        $user = User::factory()->create();
        $comic = Comic::factory()->create();
        auth()->setUser($user);

        $this->service->trackComicView($comic);

        $this->assertDatabaseHas('view_tracking', [
            'comic_id' => $comic->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function creates_tracking_record_for_guest()
    {
        $comic = Comic::factory()->create();

        $this->service->trackComicView($comic);

        $this->assertDatabaseHas('view_tracking', [
            'comic_id' => $comic->id,
        ]);
    }

    /** @test */
    public function returns_site_view_stats()
    {
        Comic::factory(2)->create(['views_count' => 10]);
        Chapter::factory(2)->create(['views_count' => 5]);

        $stats = $this->service->getSiteViewStats();

        $this->assertEquals(20, $stats['total_comic_views']);
        $this->assertEquals(10, $stats['total_chapter_views']);
        $this->assertEquals(30, $stats['total_views']);
    }

    /** @test */
    public function unique_visitors_today_counts_distinct_ips()
    {
        $now = now();
        
        ViewTracking::create([
            'ip_address' => '192.168.1.1',
            'viewed_at' => $now,
            'comic_id' => 1,
            'user_agent' => 'Mozilla/5.0',
        ]);
        
        ViewTracking::create([
            'ip_address' => '192.168.1.1',
            'viewed_at' => $now,
            'comic_id' => 2,
            'user_agent' => 'Mozilla/5.0',
        ]);
        
        ViewTracking::create([
            'ip_address' => '192.168.1.2',
            'viewed_at' => $now,
            'comic_id' => 1,
            'user_agent' => 'Mozilla/5.0',
        ]);

        $stats = $this->service->getSiteViewStats();

        $this->assertEquals(2, $stats['unique_visitors_today']);
    }

    /** @test */
    public function unique_visitors_today_excludes_older_records()
    {
        $now = now();
        $yesterday = now()->subDay();

        ViewTracking::create([
            'ip_address' => '192.168.1.1',
            'viewed_at' => $yesterday,
            'comic_id' => 1,
            'user_agent' => 'Mozilla/5.0',
        ]);
        
        ViewTracking::create([
            'ip_address' => '192.168.1.2',
            'viewed_at' => $now,
            'comic_id' => 1,
            'user_agent' => 'Mozilla/5.0',
        ]);

        $stats = $this->service->getSiteViewStats();

        // Should only count today's visitors
        $this->assertEquals(1, $stats['unique_visitors_today']);
    }

    /** @test */
    public function get_multiple_comic_unique_view_counts_returns_correct_data()
    {
        $comic1 = Comic::factory()->create();
        $comic2 = Comic::factory()->create();

        ViewTracking::create(['comic_id' => $comic1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['comic_id' => $comic1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['comic_id' => $comic2->id, 'ip_address' => '192.168.1.2', 'user_agent' => 'test']);

        $counts = $this->service->getMultipleComicUniqueViewCounts([$comic1->id, $comic2->id]);

        $this->assertEquals(1, $counts[$comic1->id]);
        $this->assertEquals(1, $counts[$comic2->id]);
    }

    /** @test */
    public function get_multiple_chapter_unique_view_counts_returns_correct_data()
    {
        $chapter1 = Chapter::factory()->create();
        $chapter2 = Chapter::factory()->create();

        ViewTracking::create(['chapter_id' => $chapter1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['chapter_id' => $chapter1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['chapter_id' => $chapter2->id, 'ip_address' => '192.168.1.2', 'user_agent' => 'test']);

        $counts = $this->service->getMultipleChapterUniqueViewCounts([$chapter1->id, $chapter2->id]);

        $this->assertEquals(1, $counts[$chapter1->id]);
        $this->assertEquals(1, $counts[$chapter2->id]);
    }

    /** @test */
    public function get_multiple_comic_unique_view_counts_fills_missing_ids()
    {
        $comic1 = Comic::factory()->create();
        $comic2 = Comic::factory()->create();
        $comic3 = Comic::factory()->create();

        ViewTracking::create(['comic_id' => $comic1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);

        $counts = $this->service->getMultipleComicUniqueViewCounts([$comic1->id, $comic2->id, $comic3->id]);

        $this->assertEquals(1, $counts[$comic1->id]);
        $this->assertEquals(0, $counts[$comic2->id]);
        $this->assertEquals(0, $counts[$comic3->id]);
    }

    /** @test */
    public function get_comic_unique_view_count_returns_single_comic_count()
    {
        $comic = Comic::factory()->create();

        ViewTracking::create(['comic_id' => $comic->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['comic_id' => $comic->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);

        $count = $this->service->getComicUniqueViewCount($comic);

        $this->assertEquals(1, $count);
    }

    /** @test */
    public function get_chapter_unique_view_count_returns_single_chapter_count()
    {
        $chapter = Chapter::factory()->create();

        ViewTracking::create(['chapter_id' => $chapter->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['chapter_id' => $chapter->id, 'ip_address' => '192.168.1.2', 'user_agent' => 'test']);

        $count = $this->service->getChapterUniqueViewCount($chapter);

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function get_unique_comic_views_total_counts_distinct_visitors()
    {
        $comic1 = Comic::factory()->create();
        $comic2 = Comic::factory()->create();

        ViewTracking::create(['comic_id' => $comic1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['comic_id' => $comic1->id, 'ip_address' => '192.168.1.1', 'user_agent' => 'test']);
        ViewTracking::create(['comic_id' => $comic2->id, 'ip_address' => '192.168.1.2', 'user_agent' => 'test']);

        $total = $this->service->getUniqueComicViewsTotal();

        $this->assertEquals(2, $total);
    }

    /** @test */
    public function logged_in_user_identified_by_user_id()
    {
        $user = User::factory()->create();
        auth()->setUser($user);
        $comic = Comic::factory()->create();

        $this->service->trackComicView($comic);

        $tracking = ViewTracking::where('comic_id', $comic->id)->first();
        $this->assertEquals($user->id, $tracking->user_id);
    }

    /** @test */
    public function guest_user_identified_by_ip_and_user_agent()
    {
        $comic = Comic::factory()->create();

        $this->service->trackComicView($comic);

        $tracking = ViewTracking::where('comic_id', $comic->id)->first();
        $this->assertNull($tracking->user_id);
        $this->assertNotNull($tracking->ip_address);
        $this->assertNotNull($tracking->user_agent);
    }

    /** @test */
    public function returns_zero_for_empty_comic_ids()
    {
        $counts = $this->service->getMultipleComicUniqueViewCounts([]);

        $this->assertEquals([], $counts);
    }

    /** @test */
    public function returns_zero_for_empty_chapter_ids()
    {
        $counts = $this->service->getMultipleChapterUniqueViewCounts([]);

        $this->assertEquals([], $counts);
    }
}

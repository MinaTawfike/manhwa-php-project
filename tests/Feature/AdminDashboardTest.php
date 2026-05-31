<?php

namespace Tests\Feature;

use App\Jobs\SyncViewTrends;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);
    }

    /** @test */
    public function super_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.index');
    }

    /** @test */
    public function non_admin_cannot_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'viewer']);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function dashboard_displays_correct_statistics()
    {
        Comic::factory(5)->create();
        Chapter::factory(10)->create();
        User::factory(3)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');
        
        $this->assertEquals(5, $stats['total_comics']);
        $this->assertEquals(10, $stats['total_chapters']);
        $this->assertEquals(4, $stats['total_users']); // 3 + 1 superadmin
    }

    /** @test */
    public function dashboard_shows_recent_activity()
    {
        Comic::factory(3)->create();
        Chapter::factory(3)->create();
        User::factory(3)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertViewHas('recentComics');
        $response->assertViewHas('recentChapters');
        $response->assertViewHas('recentUsers');

        $recentComics = $response->viewData('recentComics');
        $this->assertCount(3, $recentComics);
    }

    /** @test */
    public function analytics_page_accessible_by_super_admin()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.analytics'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.analytics');
    }

    /** @test */
    public function analytics_page_shows_summary_data()
    {
        Comic::factory(5)->create();
        Chapter::factory(10)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.analytics'));

        $response->assertViewHas('analyticsSummary');
        $summary = $response->viewData('analyticsSummary');
        
        $this->assertEquals(5, $summary['total_comics']);
        $this->assertEquals(10, $summary['total_chapters']);
    }

    /** @test */
    public function analytics_filters_work()
    {
        Comic::factory(5)->create();

        $filters = ['most_viewed', 'recently_updated', 'most_bookmarked', 'newest'];

        foreach ($filters as $filter) {
            $response = $this->actingAs($this->superAdmin)
                ->get(route('admin.analytics', ['filter' => $filter]));

            $response->assertStatus(200);
            $response->assertViewHas('filter', $filter);
        }
    }

    /** @test */
    public function analytics_pagination_works()
    {
        Comic::factory(25)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.analytics'));

        $comicAnalytics = $response->viewData('comicAnalytics');
        $this->assertTrue($comicAnalytics->hasPages());
    }

    /** @test */
    public function system_health_page_accessible()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.system-health'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.system-health');
        $response->assertViewHas('systemInfo');
    }

    /** @test */
    public function content_moderation_page_accessible()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.content-moderation'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.content-moderation');
    }

    /** @test */
    public function trends_page_accessible()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.trends'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.trends');
    }

    /** @test */
    public function trends_data_api_returns_json()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('api.admin.trends.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'labels',
            'datasets' => [
                'total_views',
                'unique_visitors',
                'bookmarks_added',
            ],
        ]);
    }

    /** @test */
    public function trends_data_api_accepts_preset_range_query()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('api.admin.trends.data', ['range' => 28]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'labels',
            'datasets' => [
                'total_views',
                'unique_visitors',
                'bookmarks_added',
            ],
        ]);
    }

    /** @test */
    public function trends_data_api_accepts_custom_date_range_query()
    {
        Queue::fake();
        Cache::flush();

        $startDate = today()->subDays(13)->toDateString();
        $endDate = today()->subDays(7)->toDateString();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('api.admin.trends.data', ['start' => $startDate, 'end' => $endDate]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'labels',
            'datasets' => [
                'total_views',
                'unique_visitors',
                'bookmarks_added',
            ],
        ]);

        Queue::assertPushed(SyncViewTrends::class, function (SyncViewTrends $job) use ($startDate, $endDate) {
            return $job->startDate === $startDate && $job->endDate === $endDate;
        });
    }

    /** @test */
    public function missing_trend_dates_dispatch_sync_view_trends_job()
    {
        Queue::fake();
        Cache::flush();

        $startDate = today()->subDays(6)->toDateString();
        $endDate = today()->toDateString();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('api.admin.trends.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'labels',
            'datasets' => [
                'total_views',
                'unique_visitors',
                'bookmarks_added',
            ],
        ]);

        Queue::assertPushed(SyncViewTrends::class, function (SyncViewTrends $job) use ($startDate, $endDate) {
            return $job->startDate === $startDate && $job->endDate === $endDate;
        });
    }

    /** @test */
    public function non_super_admin_cannot_access_analytics()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.analytics'));

        $response->assertStatus(403);
    }

    /** @test */
    public function non_super_admin_cannot_access_system_health()
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $response = $this->actingAs($viewer)
            ->get(route('admin.system-health'));

        $response->assertStatus(403);
    }

    /** @test */
    public function view_counts_display_correctly()
    {
        $comic = Comic::factory()->create(['views_count' => 100]);
        $comic2 = Comic::factory()->create(['views_count' => 50]);
        
        Chapter::factory(2)->create(['comic_id' => $comic->id, 'views_count' => 25]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');
        
        $this->assertEquals(150, $stats['total_comic_views']);
        $this->assertEquals(50, $stats['total_chapter_views']);
        $this->assertEquals(200, $stats['total_views']);
    }
}

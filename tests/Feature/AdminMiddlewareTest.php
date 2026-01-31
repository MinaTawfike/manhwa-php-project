<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_admin_routes()
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_viewer_cannot_access_admin_routes()
    {
        $user = User::factory()->create([
            'role' => 'viewer',
        ]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_comic_creation()
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($user)->get('/comics/create');

        $response->assertStatus(200);
    }

    public function test_viewer_cannot_access_comic_creation()
    {
        $user = User::factory()->create([
            'role' => 'viewer',
        ]);

        $response = $this->actingAs($user)->get('/comics/create');

        $response->assertStatus(403);
    }
}

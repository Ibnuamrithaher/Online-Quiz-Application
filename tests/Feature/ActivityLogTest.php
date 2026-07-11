<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\ActivityLog;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_records_activity_log()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'Login',
        ]);
    }

    public function test_admin_can_view_activity_logs()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        ActivityLog::factory()->create(['user_id' => $admin->id, 'action' => 'Test Action', 'description' => 'Test']);

        $response = $this->actingAs($admin)->get('/admin/activity-logs');

        $response->assertStatus(200);
        $response->assertSee('Test Action');
    }

    public function test_participant_cannot_view_activity_logs()
    {
        $participant = User::factory()->create();

        $response = $this->actingAs($participant)->get('/admin/activity-logs');

        $response->assertStatus(403);
    }
}

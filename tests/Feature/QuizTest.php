<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_quiz()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/quizzes', [
            'title' => 'Test Quiz',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $response->assertRedirect('/admin/quizzes');
        $this->assertDatabaseHas('quizzes', ['title' => 'Test Quiz']);
    }

    public function test_peserta_cannot_access_admin_dashboard()
    {
        $peserta = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($peserta)->get('/admin/quizzes');
        $response->assertStatus(403);
    }
}

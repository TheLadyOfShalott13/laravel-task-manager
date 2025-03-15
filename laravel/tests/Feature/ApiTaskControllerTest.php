<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        // Create a test user and authenticate
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'web'); // Authenticate as the user
    }

    /** @test */
    public function it_returns_a_list_of_tasks_for_authenticated_user()
    {
        // Create tasks for the authenticated user
        Task::factory()->count(5)->create(['user_id' => $this->user->id]);

        // Create tasks for another user (should not appear)
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data'); // Ensure only the user's tasks are returned
    }

    /** @test */
    public function it_creates_a_new_task_for_authenticated_user()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'due_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => 'Test Task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_denies_task_creation_for_unauthenticated_users()
    {
        // Log out the user
        $this->be(null);

        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Unauthorized user cannot create this.',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function it_shows_a_specific_task_belongs_to_authenticated_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $task->id]);
    }

    /** @test */
    public function it_denies_access_to_tasks_belonging_to_other_users()
    {
        $task = Task::factory()->create(); // Task owned by another user

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function it_updates_a_task_for_authenticated_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updatedData = ['title' => 'Updated Task Title'];

        $response = $this->patchJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Updated Task Title']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
        ]);
    }

    /** @test */
    public function it_deletes_a_task_for_authenticated_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_prevents_deleting_tasks_of_other_users()
    {
        $task = Task::factory()->create(); // Task owned by another user

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403); // Forbidden
    }
}


<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $board;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user and board
        $this->user = User::factory()->create();
        $this->board = Board::factory()->create(['user_id' => $this->user->id]);
        
        // Authenticate the user
        $this->actingAs($this->user);
    }

    /** @test */
    public function user_can_list_all_tasks()
    {
        Task::factory()->count(3)->create(['board_id' => $this->board->id]);

        $response = $this->get('api/user/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'tasks');
    }

    /** @test */
    public function user_can_create_task()
    {
        $data = [
            'content' => 'Test task content',
            'status' => 'todo'
        ];

        $response = $this->post("api/user/tasks/{$this->board->id}", array_merge($data, ['board_id' => $this->board->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['task', 'message']);
        $this->assertDatabaseHas('tasks', [
            'content' => 'Test task content',
            'status' => 'todo',
            'board_id' => $this->board->id
        ]);
    }

    /** @test */
    public function user_cannot_create_task_without_content()
    {
        $response = $this->post("api/user/tasks/{$this->board->id}", [
            'status' => 'todo',
            'board_id' => $this->board->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }

    /** @test */
    public function user_can_view_a_task()
    {
        $task = Task::factory()->create(['board_id' => $this->board->id]);

        $response = $this->get("api/user/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['content' => $task->content]);
    }

    /** @test */
    public function user_gets_404_if_task_not_found()
    {
        $response = $this->get('api/user/tasks/99999');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No Task with this ID found']);
    }

    /** @test */
    public function user_can_update_task()
    {
        $task = Task::factory()->create(['board_id' => $this->board->id]);

        $updateData = ['content' => 'Updated task content', 'status' => 'doing'];
        $response = $this->put("api/user/tasks/{$task->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'task updated successfully']);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'content' => 'Updated task content',
            'status' => 'doing'
        ]);
    }

    /** @test */
    public function user_cannot_update_task_with_invalid_data()
    {
        $task = Task::factory()->create(['board_id' => $this->board->id]);

        $response = $this->put("api/user/tasks/{$task->id}", ['status' => 'invalid-status']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    /** @test */
    public function user_can_delete_task()
    {
        $task = Task::factory()->create(['board_id' => $this->board->id]);

        $response = $this->delete("api/user/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_gets_404_if_deleting_non_existent_task()
    {
        $response = $this->delete('api/user/tasks/99999');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No task with this ID found']);
    }
}

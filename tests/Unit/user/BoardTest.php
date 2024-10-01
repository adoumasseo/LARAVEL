<?php

namespace Tests\Unit;

use App\Models\Board;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    /** @test index */
    public function user_can_list_their_boards()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Create some boards for the user
        $boards = Board::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Perform GET request
        $response = $this->getJson('/api/user/boards');

        // Assert response status
        $response->assertStatus(200);

        // Assert the structure of the response
        $response->assertJsonStructure([
            'boards' => [
                '*' => ['id', 'board_name', 'status', 'user_id', 'created_at', 'updated_at']
            ]
        ]);

        // Assert the data in the response contains the created boards
        $response->assertJsonCount(3, 'boards');
    }

    /** @test create a valid board*/
    public function user_can_create_a_board()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Board data to be sent
        $data = ['board_name' => 'My New Board'];

        // Perform POST request
        $response = $this->postJson('/api/user/create-board', $data);

        // Assert response status
        $response->assertStatus(200);

        // Assert the structure of the response
        $response->assertJsonStructure([
            'board' => ['id', 'board_name', 'status', 'user_id'],
            'message'
        ]);

        // Assert the message returned
        $response->assertJson(['message' => 'board create succesfully']);
    }

    /** @test bad baord name */
    public function create_board_fails_with_validation_errors()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Send invalid data (missing board_name)
        $data = ['board_name' => ''];

        // Perform POST request
        $response = $this->postJson('/api/user/create-board', $data);

        // Assert response status
        $response->assertStatus(422);

        // Assert validation error messages
        $response->assertJsonStructure(['errors' => ['board_name']]);
    }

    /** @test */
    public function user_can_update_a_board()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Create a board for the user
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        // Update data
        $updateData = ['board_name' => 'Updated Board', 'status' => 'archived'];

        // Perform PUT request
        $response = $this->putJson("/api/user/update-board/{$board->id}", $updateData);

        // Assert response status
        $response->assertStatus(200);

        // Assert the structure of the response
        $response->assertJsonStructure([
            'board' => ['id', 'board_name', 'status', 'user_id'],
            'message'
        ]);

        // Assert the message returned
        $response->assertJson(['message' => 'board updated successfully']);
    }

    /** @test */
    public function update_board_fails_with_validation_errors()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Create a board for the user
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        // Send invalid data (empty board_name and invalid status)
        $updateData = ['board_name' => '', 'status' => 'invalid'];

        // Perform PUT request
        $response = $this->putJson("/api/user/update-board/{$board->id}", $updateData);

        // Assert response status
        $response->assertStatus(422);

        // Assert validation error messages
        $response->assertJsonStructure(['errors' => ['board_name', 'status']]);
    }

    /** @test */
    public function user_can_delete_a_board()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Create a board for the user
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        // Perform DELETE request
        $response = $this->deleteJson("/api/user/delete-board/{$board->id}");

        // Assert response status
        $response->assertStatus(204);
    }

    /** @test */
    public function deleting_non_existent_board_returns_404()
    {
        // Acting as the created user
        $this->actingAs($this->user);

        // Perform DELETE request for non-existent board
        $response = $this->deleteJson("/api/user/delete-board/99999");

        // Assert response status
        $response->assertStatus(404);

        // Assert the message returned
        $response->assertJson(['message' => 'No board with this ID found']);
    }

    /**
     * Test showing tasks for an existing board
     */
    public function test_can_show_board_tasks()
    {
        // Create a board associated with the current user
        $board = Board::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Create some tasks associated with this board
        $tasks = Task::factory()->count(3)->create([
            'board_id' => $board->id
        ]);

        // Act as the current user and make a GET request to the show route
        $response = $this->actingAs($this->user)->get("/api/user/board-get-tasks/{$board->id}");

        // Assert the response status is 200 (success)
        $response->assertStatus(200);

        // Assert that the returned JSON structure contains tasks
        $response->assertJsonStructure([
            'tasks' => [
                '*' => [
                    'id',
                    'content',
                    'board_id',
                ]
            ]
        ]);

        // Assert that the number of tasks returned is correct
        $response->assertJsonCount(3, 'tasks');
    }

    /**
     * Test showing tasks for a non-existent board
     */
    public function test_cannot_show_tasks_if_board_not_found()
    {
        // Try to access a board that doesn't exist (ID 999)
        $response = $this->actingAs($this->user)->get('/api/user/board-get-tasks/999');

        // Assert the response status is 404 (not found)
        $response->assertStatus(404);

        // Assert that the returned JSON contains the correct error message
        $response->assertJson([
            'message' => 'No board with this ID found'
        ]);
    }
}

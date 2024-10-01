<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    public function test_admin_can_view_all_users()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create some users
        User::factory()->count(5)->create();

        // Act as the admin user
        $response = $this->actingAs($admin)->get('/api/admin/dashboard');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'profile',
                        'role',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }

    public function test_admin_can_update_user_info()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a user to update
        $user = User::factory()->create();

        // Define the updated data
        $updatedData = [
            'first_name' => 'UpdatedFirstName',
            'last_name' => 'UpdatedLastName'
        ];

        // Act as the admin user
        $response = $this->actingAs($admin)->put("/api/admin/update-user/{$user->id}", $updatedData);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
                'user' => [
                    'id' => $user->id,
                    'first_name' => 'UpdatedFirstName',
                    'last_name' => 'UpdatedLastName',
                ]
            ]);

        // Assert the user was updated in the database
        $this->assertDatabaseHas('users', $updatedData + ['id' => $user->id]);
    }

    public function test_admin_cannot_update_user_info_with_invalid_data()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a user to update
        $user = User::factory()->create();

        // Define the invalid data (missing first_name and last_name)
        $invalidData = [
            'first_name' => '',
            'last_name' => ''
        ];

        // Act as the admin user
        $response = $this->actingAs($admin)->put("/api/admin/update-user/{$user->id}", $invalidData);

        // Assert response
        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['first_name', 'last_name']]);
    }

    public function test_admin_can_delete_user()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a user to delete
        $user = User::factory()->create();

        // Act as the admin user
        $response = $this->actingAs($admin)->delete("/api/admin/delete-user/{$user->id}");

        // Assert response
        $response->assertStatus(204);

        // Assert the user was deleted from the database
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_user_if_not_found()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Act as the admin user
        $response = $this->actingAs($admin)->delete("/api/admin/delete-user/9999");

        // Assert response
        $response->assertStatus(404)
            ->assertJson(['message' => 'No users with this ID found']);
    }
}

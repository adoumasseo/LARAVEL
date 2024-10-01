<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_self_with_valid_data()
    {
        // Create a user and simulate login
        $user = User::factory()->create([
            'password' => Hash::make('old_password')
        ]);
        
        // Simulate user authentication
        $this->actingAs($user);

        // Simulate a profile image upload
        Storage::fake('public');
        $file = UploadedFile::fake()->image('profile.jpg');

        // Make the update request
        $response = $this->json('PUT', "/api/user/profile/{$user->id}", [
            'first_name' => 'UpdatedFirstName',
            'last_name' => 'UpdatedLastName',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
            'profile' => $file,
        ]);

        // Assert the user was updated correctly
        $response->assertStatus(200);
        $response->assertJsonPath('user.first_name', 'UpdatedFirstName');
        $response->assertJsonPath('user.last_name', 'UpdatedLastName');

        // Verify the user's password was updated
        $this->assertTrue(Hash::check('new_password', $user->fresh()->password));

        Storage::disk('public')->assertExists($user->fresh()->profile);
    }

    public function test_update_self_unauthorized()
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Simulate user1 authentication
        $this->actingAs($user1);

        // Attempt to update user2's data
        $response = $this->json('PUT', "/api/user/profile/{$user2->id}", [
            'first_name' => 'UpdatedFirstName',
        ]);

        // Assert unauthorized response
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized'
        ]);
    }

    public function test_update_self_validation_fails()
    {
        // Create a user and simulate login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Send invalid data
        $response = $this->json('PUT', "/api/user/profile/{$user->id}", [
            'first_name' => '', // invalid (empty)
        ]);

        // Assert validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
    }
}

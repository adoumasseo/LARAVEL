<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->post('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $response->json('token');

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->post('/api/logout');

        $response->assertStatus(200);
    }

    public function test_user_bad_register()
    {

        $response = $this->post('/api/register', [
            'first_name' => '',
            'last_name' => '',
            'email' => 'bad_email',
            'password' => 'bad',
            'password_confirmation' => 'badd',
        ]);

        $response->assertStatus(422);
        // for check json response
        $response->assertJson([
            'errors' => [
                'first_name' => ['The first name field is required.'],
                'last_name' => ['The last name field is required.'],
                'email' => ['The email field must be a valid email address.'],
                'password' => [
                    'The password field must be at least 8 characters.',
                    'The password field confirmation does not match.',
                ],
            ],
        ]);

        // email already use
        $user = User::factory()->create([
            'email' => "john@example.com",
        ]);
        $reponse_mail_used = $this->post('/api/register', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password,
        ]);
        $reponse_mail_used->assertStatus(409);
        $reponse_mail_used->assertJson([
            'message' => 'User with this email already exits'
        ]);
    }

    public function test_user_bad_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/api/login', [
            'email' => 'adoumasseo@gmail.com',
            'password' => 'essaie_essaie',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid login credential'
        ]);

        $response_no_conform = $this->post('/api/login', [
            'email' => 'bad_email',
            'password' => 'bad_pas'
        ]);
        $response_no_conform->assertStatus(422);
        $response_no_conform->assertJsonStructure(['errors']);
    }
}

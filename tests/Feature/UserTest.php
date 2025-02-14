<?php

namespace Tests\Feature;

use Database\Seeders\AdminUserSeeder;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
            ],
        ]);
    }

    public function test_user_cannot_register_without_name(): void
    {
        $response = $this->postJson('/api/register', [
            'email' => 'john@doe.com',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_user_cannot_register_without_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_cannot_register_without_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_user_cannot_register_password_less_than_6_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'pass',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_user_cannot_register_with_invalid_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_cannot_register_email_already_exists(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->postJson('/api/register', [
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    public function test_user_cannot_register_all_blanks(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $response->assertJsonValidationErrors('email');
        $response->assertJsonValidationErrors('password');
    }
}

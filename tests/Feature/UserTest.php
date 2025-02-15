<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Database\Seeders\AdminUserSeeder;
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

    public function test_user_can_login(): void
    {
        $this->seed(AdminUserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => 'admin@email.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'role',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                'token'
            ],
        ]);
    }

    public function test_user_cannot_login_with_invalid_email(): void
    {
        $this->seed(AdminUserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $this->seed(AdminUserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => 'admin@email.com',
            'password' => 'invalid-password',
        ]);
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error',
        ]);
    }

    public function test_user_cannot_login_invalid_credentials(): void
    {
        //no seeder, which means no user in the database
        $response = $this->postJson('/api/login', [
            'email' => 'admin@email.com',
            'password' => 'invalid-password',
        ]);
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error',
        ]);
    }

    public function test_user_can_get_all_users(): void
    {
        $this->seed(AdminUserSeeder::class);
        // $response = $this->getJson('/api/users');
        $response = $this->actingAs(User::first())->getJson('/api/users');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ],
            ],
        ]);
    }

    public function test_user_cannot_get_all_users(): void
    {
        $this->getJson('/api/users')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_user_can_find_user_by_id(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->getJson('/api/users/' . $user->id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ]
            ]);
    }

    public function test_user_not_found(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->getJson('/api/users/' . $user->id - 1)
            ->assertStatus(404)
            ->assertJsonStructure([
                'error'
            ]);
    }

    public function test_user_can_update(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $user->id, [
            'name' => 'John',
            'email' => 'john@admin.com',
            'password' => 'password'
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at'
                ]
            ]);
    }

    public function test_user_update_no_changes(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $user->id, [
            'name' => 'Admin',
            'email' => 'admin@email.com',
            // 'password' => 'password'
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'error'
            ]);
    }

    public function test_user_update_form_blanks(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $user->id, [
            'name' => '',
            'email' => '',
            // 'password' => 'password'
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_user_can_delete_user(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->deleteJson('/api/users/' . $user->id + 5)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'User successfully deleted.'
            ]);
    }

    public function test_user_delete_id_not_found(): void
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->deleteJson('/api/users/' . $user->id - 5)
            ->assertStatus(404)
            ->assertJson([
                'error' => 'User not found.'
            ]);
    }
}

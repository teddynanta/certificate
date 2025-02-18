<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CertificateSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RecipientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class CertificateTest extends TestCase
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

    public function test_user_can_get_all_certificate()
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::first();
        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/certificates/');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'recipient_id',
                        'code',
                        'issued_date',
                        'created_by'
                    ]
                ]
            ]);
    }

    public function test_user_certificate_not_exist()
    {
        $this->seed(AdminUserSeeder::class);
        $user = User::first();
        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/certificates/');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'No certificates available.'
            ]);
    }

    public function test_user_can_get_by_code()
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::first();
        $certificate = Certificate::first()->only('code');
        // dd($certificate['code']);
        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/certificates/verify?code=' . $certificate['code']);
        // dd($response);
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => 'ok'
            ]);
    }
}

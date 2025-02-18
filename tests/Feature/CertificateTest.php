<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Certificate;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RecipientSeeder;
use Database\Seeders\CertificateSeeder;
use function PHPUnit\Framework\assertJson;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/certificates/verify?code=' . $certificate['code']);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'recipient_id',
                    'code',
                    'issued_date',
                    'created_by'
                ]
            ]);
    }

    public function test_user_certificate_not_found()
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::first();
        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/certificates/verify?code=999/DISKOMINFOTIKSAN/I.II/2025');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'No certificates found.'
            ]);
    }

    public function test_user_can_delete_certificate()
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::first();
        $certificate = Certificate::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->deleteJson('/api/certificates/' . $certificate->id);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Certificate successfully deleted.'
            ]);
    }

    public function test_user_cannot_delete_certificate()
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->deleteJson('/api/certificates/900');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Certificate not found.'
            ]);
    }

    public function test_user_can_create_certificate()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => 'John Doe',
            'email' => 'john@email.com',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function test_create_certificate_blank_name()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '',
            'email' => 'john@email.com',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_blank_email()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => 'Heri Suhendri',
            'email' => '',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_name_not_valid()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => 00000,
            'email' => 'john@doe.com',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_email_not_valid()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'johndoe',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_code_not_valid()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'john@doe',
            'code' => 'AKU/6969/3.12/5000',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_code_blank()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'john@doe',
            'code' => '',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_date_blank()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'john@doe',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => '',
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_date_not_valid()
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'john@doe',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => 'senin, 12 jan 25',
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_not_authenticated()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'john@doe',
            'code' => '99/DISKOMINFOTIKSAN/III.X/2025',
            'issued_date' => 'senin, 12 jan 25',
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(401)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_create_certificate_code_exist()
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::first();
        $certificate = Certificate::first()->only('code');
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('api/certificates/', [
            'name' => '???9999',
            'email' => 'john@doe.com',
            'code' => $certificate['code'],
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_user_can_update_certificate()
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::first();
        $certificate = Certificate::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->putJson('api/certificates/' . $certificate->id, [
            'name' => 'Albert Mohecking',
            'email' => 'papa@albert.com',
            // 'code' => '',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function test_user_update_id_not_found()
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::first();
        $certificate = Certificate::first();
        $response = $this->actingAs($admin)->withHeaders([
            'Accept' => 'application/json'
        ])->putJson('api/certificates/' . $certificate->id + 999, [
            'name' => 'Albert Mohecking',
            'email' => 'papa@albert.com',
            'issued_date' => date(now()),
            'created_by' => Auth::id()
        ]);
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    private $url = '/api/users';
    private $stringToExceedMaxLength = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

    public function test_if_return_all_users(): void {
        
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson($this->url);
        $response->assertStatus(200);
    }

    public function test_if_return_all_users_with_pagination(): void {
        $authUser = User::factory()->create();

        User::factory()->count(50)->create();

        $response = $this->actingAs($authUser, 'sanctum')->getJson("{$this->url}?limit=25");

        $response->assertStatus(200);
        $response->assertJsonCount(25, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'page',
                        'active'
                    ],
                ],
                'path',
                'per_page',
                'to',
                'total'
            ],
        ]);
    }

    public function test_if_return_a_single_user(): void {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}/{$user->id}");
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_if_user_can_be_created_and_inserted_in_the_database(): void {
        $user = User::factory()->create();

        $body = [
            'name' => 'Usuário de teste',
            'email' => 'autor@teste.com',
            'password' => '123123123'
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson($this->url, $body);
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'Usuário de teste',
            'email' => 'autor@teste.com',
        ]);
    }

    public function test_if_user_can_be_created_with_invalid_data(): void {
        $user = User::factory()->create();

        $body = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson($this->url, $body);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_if_user_can_be_updated_in_the_database(): void {
        $user = User::factory()->create();

        $body = [
            'name' => 'Usuário de teste editado',
            'email' => $user->email,
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("{$this->url}/{$user->id}", $body);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuário de teste editado',
            'email' => $user->email,
        ]);

    }

    public function test_if_user_can_be_updated_with_invalid_data(): void {
        $user = User::factory()->create();

        $body = [
            'name' => '',
            'email' => '',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("{$this->url}/{$user->id}", $body);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_if_user_can_not_be_found(): void {

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}/9999");
        $response->assertStatus(404);
    }

    public function test_if_user_can_be_deleted_from_the_database(): void {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("{$this->url}/{$user->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_if_unauthenticated_user_cannot_access_the_endpoints(): void {
        $response = $this->getJson($this->url);
        $response->assertStatus(401);

        $response = $this->postJson($this->url, []);
        $response->assertStatus(401);

        $response = $this->putJson("{$this->url}/1", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("{$this->url}/1");
        $response->assertStatus(401);
    }

}

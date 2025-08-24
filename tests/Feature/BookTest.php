<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;
    private $url = '/api/books';
    private $stringToExceedMaxLength = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

    public function test_if_return_all_books(): void {
        
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson($this->url);
        $response->assertStatus(200);
    }

    public function test_if_return_all_books_with_pagination(): void {
        $user = User::factory()->create();

        Book::factory()->count(50)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}?limit=25");

        $response->assertStatus(200);
        $response->assertJsonCount(25, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'author',
                    'pages',
                    'description',
                    'published_at',
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

    public function test_if_return_a_single_book(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}/{$book->id}");
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'pages',
                'description',
                'published_at',
            ],
        ]);
    }

    public function test_if_book_can_be_created_and_inserted_in_the_database(): void {
        $user = User::factory()->create();

        $body = [
            'title' => 'Livro de Teste',
            'author' => 'Autor de Teste',
            'pages' => 100,
            'description' => 'Descrição do livro de teste',
            'published_at' => '2023-10-01',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson($this->url, $body);
        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'title' => 'Livro de Teste',
            'author' => 'Autor de Teste',
            'pages' => 100,
            'description' => 'Descrição do livro de teste',
            'published_at' => '2023-10-01',
        ]);
    }

    public function test_if_book_can_be_created_with_invalid_data(): void {
        $user = User::factory()->create();

        $body = [
            'title' => '',
            'author' => '',
            'pages' => -100,
            'description' => $this->stringToExceedMaxLength,
            'published_at' => 'invalid-date',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson($this->url, $body);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['title', 'author', 'pages', 'description', 'published_at']);
    }

    public function test_if_book_can_be_updated_in_the_database(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $body = [
            'title' => 'Livro de Teste editado',
            'author' => 'Autor de Teste editado',
            'pages' => 150,
            'description' => 'Descrição do livro de teste editado',
            'published_at' => '2023-11-01',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("{$this->url}/{$book->id}", $body);
        $response->assertStatus(200);

        $this->assertDatabaseHas('books', [
            'title' => 'Livro de Teste editado',
            'author' => 'Autor de Teste editado',
            'pages' => 150,
            'description' => 'Descrição do livro de teste editado',
            'published_at' => '2023-11-01',
        ]);

    }

    public function test_if_book_can_be_updated_with_invalid_data(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $body = [
            'title' => '',
            'author' => '',
            'pages' => -150,
            'description' => $this->stringToExceedMaxLength,
            'published_at' => 'invalid-date',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("{$this->url}/{$book->id}", $body);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['title', 'author', 'pages', 'description', 'published_at']);
    }

    public function test_if_book_can_not_be_found(): void {

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}/9999");
        $response->assertStatus(404);
    }

    public function test_if_book_can_be_deleted_from_the_database(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("{$this->url}/{$book->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
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

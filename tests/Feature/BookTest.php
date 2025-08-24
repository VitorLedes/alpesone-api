<?php

namespace Tests\Feature;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class BookTest extends TestCase
{
    // use RefreshDatabase;
    private $url = '/api/books';

    
    public function test_if_book_can_be_created_and_inserted_in_the_database(): void
    {   
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $body = [
            'title' => 'Livro de Teste',
            'author' => 'Autor de Teste',
            'pages' => 100,
            'description' => 'Descrição do livro de teste',
            'published_at' => '2023-10-01',
        ];

        $response = $this->postJson($this->url, $body);
        $response->assertStatus(201);
    }
}

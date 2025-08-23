<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Função para obter todo os livros.
     */
    public function index() {

        $limit = (int) request()->get('limit') ?? 10;

        if ($limit > 100 || $limit < 1) {
            $limit = 10;
        }

        $books = Book::paginate($limit);

        return BookResource::collection($books)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Função para obter um único livro.
     *
     * @param integer $id
     * @return void
     */
    public function show(int $id) {

        $book = Book::findOrFail($id);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Função para criar um novo livro.
     *
     * @param BookRequest $request
     * @return void
     */
    public function store(BookRequest $request) {

        $book = Book::create($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * Função para atualizar um livro.
     *
     * @param BookRequest $request
     * @param integer $id
     * @return void
     */
    public function update(BookRequest $request, int $id) {

        $book = Book::findOrFail($id);
        $book->update($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(200);

    }

    /**
     * Função para deletar um livro.
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id) {

        $book = Book::findOrFail($id);
        $book->delete();

        return response()->noContent();
    }

}

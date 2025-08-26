<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Função para listar todos os usuários
     *
     * @return void
     */
    public function index(Request $request) {

        $limit = (int) $request->limit ?? 10;

        if ($limit > 100 || $limit < 1) {
            $limit = 10;
        }

        $users = User::paginate($limit);

        return UserResource::collection($users);
    }

    /**
     * Função para obter um único usuário.
     *
     * @param integer $id
     * @return void
     */
    public function show(int $id) {

        $user = User::findOrFail($id);
        
        return (new UserResource($user));
    }

    /**
     * Função para criar um novo usuário.
     *
     * @param UserRequest $request
     * @return void
     */
    public function store(UserRequest $request) {

        $user = User::create($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Função para atualizar um usuário.
     *
     * @param UserRequest $request
     * @param [type] $request
     * @param integer $id
     * @return void
     */
    public function update(int $id, UserRequest $request) {

        $user = User::findOrFail($id);
        $user->update($request->validated());

        return (new UserResource($user));
    }

    /**
     * Função para deletar um usuário.
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id) {

        $user = User::findOrFail($id);
        $user->delete();

        return response()->noContent();
    }
}

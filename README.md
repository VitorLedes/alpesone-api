# Laravel Books API

API REST para gerenciar livros e usuários.

## Como rodar

### Requisitos
- PHP 8.1+
- MySQL
- Composer

### Instalação

```bash
git clone <repo>
cd projeto
composer install
cp .env.example .env
php artisan key:generate
```

### Banco de dados

No `.env`:
```
DB_DATABASE=sua_base
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Para testes
DB_DATABASE_TESTING=sua_base_testing
```

Criar os bancos:
```sql
CREATE DATABASE sua_base;
CREATE DATABASE sua_base_testing;
```

Rodar migrations:
```bash
php artisan migrate
php artisan migrate --env=testing
```

### Subir o servidor
```bash
php artisan serve
```

## API

Base: `http://localhost:8000/api`

Todos os endpoints precisam de autenticação (Bearer token).

### Books
- `GET /books` - Lista livros (com paginação)
- `GET /books/{id}` - Mostra um livro
- `POST /books` - Cria livro
- `PUT /books/{id}` - Atualiza livro
- `DELETE /books/{id}` - Deleta livro

### Users  
- `GET /users` - Lista usuários
- `GET /users/{id}` - Mostra um usuário
- `POST /users` - Cria usuário
- `PUT /users/{id}` - Atualiza usuário
- `DELETE /users/{id}` - Deleta usuário

### Paginação
Adicione `?limit=10&page=2` nas listagens.

### Exemplo - Criar livro
```json
POST /api/books

{
    "title": "1984",
    "author": "George Orwell", 
    "pages": 328,
    "description": "Distopia clássica",
    "published_at": "1949-06-08"
}
```

### Exemplo - Criar usuário
```json
POST /api/users

{
    "name": "João Silva",
    "email": "joao@teste.com",
    "password": "12345678"
}
```

## Testes

Rodar todos:
```bash
php artisan test
```

Rodar só os de integração:
```bash
php artisan test tests/Feature/
```

## Comando de importação

```bash
php artisan import:books
```

Para rodar de hora em hora, adicionar no cron:
```bash
0 * * * * php /caminho/projeto/artisan import:books
```

## Validações

### Books
- title: obrigatório, máx 255 chars
- author: obrigatório, máx 255 chars  
- pages: obrigatório, número positivo
- description: opcional
- published_at: opcional, formato data

### Users
- name: obrigatório, máx 255 chars
- email: obrigatório, email válido, único
- password: obrigatório, mín 8 chars

## Estrutura do projeto

```
app/
├── Http/Controllers/
│   ├── BookController.php
│   └── UserController.php
├── Http/Requests/
│   ├── BookRequest.php
│   └── UserRequest.php
├── Models/
│   ├── Book.php
│   └── User.php
└── Console/Commands/
    └── ImportBooksCommand.php

tests/
├── Feature/
│   ├── BookTest.php
│   └── UserTest.php
└── Unit/
    └── ...
```

## Comandos úteis

```bash
# Limpar cache
php artisan cache:clear

# Gerar factory
php artisan make:factory BookFactory

# Rollback migrations  
php artisan migrate:rollback

# Ver rotas
php artisan route:list
```

## Deploy

Para produção:
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```

Configurar `.env` para produção:
```
APP_ENV=production
APP_DEBUG=false
```
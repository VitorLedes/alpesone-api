# Alpes ONE API Challenge

API REST para gerenciar carros e usuários.

## Como rodar

### Requisitos
- PHP 8.2+
- MySQL
- Composer

### Instalação

```bash
git clone <repo>
cd projeto
composer install
cp .env.example .env
cp .env .env.testing
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

### Cars
- `GET /cars` - Lista carros (com paginação)
- `GET /cars/{id}` - Mostra um carros
- `POST /cars` - Cria carros
- `PUT /cars/{id}` - Atualiza carros
- `DELETE /cars/{id}` - Deleta carros

### Users  
- `GET /users` - Lista usuários
- `GET /users/{id}` - Mostra um usuário
- `POST /users` - Cria usuário
- `PUT /users/{id}` - Atualiza usuário
- `DELETE /users/{id}` - Deleta usuário

### Paginação
Adicione `?limit=10` nas listagens.

### Exemplo - Criar carro
```json
POST /api/cars

{
    "type": "carro",
    "brand": "Hyundai",
    "model": "CRETA",
    "version": "CRETA 16A ACTION",
    "model_year": "2025",
    "build_year": "2025",
    "doors": 5,
    "board": "JCU2I93",
    "chassi": "",
    "transmission": "Automática",
    "km": "24208",
    "description": "revisado procedência garantia n Pegamos trocas mediante avaliação valor do anuncio para vendas avista 9931-6648 /  Araranguá - SC \r\n\r\nJefersson  48- 8427-9763 / Criciuma - SC\r\n\r\nLucas - 48-48 9177-1511 /  Tubarão - SC",
    "sold": "0",
    "category": "SUV",
    "url_car": "hyundai-creta-2025-automatica-125306",
    "old_price": "",
    "price": "115900.00",
    "color": "Branco",
    "fuel": "Gasolina",
    "external_id": "125306"
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
php artisan import:cars
```

Para rodar de hora em hora, adicionar no cron:
```bash
0 * * * * php /caminho/projeto/artisan import:cars
```

## Validações

### Cars
- type: obrigatório, string, máx 255 chars
- brand: obrigatório, string, máx 255 chars
- model: obrigatório, string, máx 255 chars
- version: obrigatório, string, máx 255 chars
- model_year: obrigatório, string, máx 255 chars
- build_year: obrigatório, string, máx 255 chars
- doors: obrigatório, numérico
- board: obrigatório, string, máx 255 chars
- chassi: não-obrigatório, string, máx 255 chars
- transmission: obrigatório, string, máx 255 chars
- km: obrigatório, string, máx 255 chars
- description: não-obrigatório, text
- sold: obrigatório, string, máx 255 chars
- category: obrigatório, string, máx 255 chars
- url_car: obrigatório, string, máx 255 chars
- old_price: não-obrigatório, string, máx 255 chars
- price: obrigatório, string, máx 255 chars
- color: obrigatório, string, máx 255 chars
- fuel: obrigatório, string, máx 255 chars
- external_id: obrigatório, numérico

### Users
- name: obrigatório, string, máx 255 chars
- email: obrigatório, email válido, único
- password: obrigatório, mín 8 chars

## Comandos úteis

```bash
# Limpar cache
php artisan cache:clear

# Rollback migrations  
php artisan migrate:rollback

# Ver rotas
php artisan route:list
```

## Deploy
Arquivo de deploy /caminho/do/projeto/deploy.sh (PRECISA SER RODADO COM SUDO)

GitHub Actions

/caminho/do/projeto/.github/workflows/deploy.yml (Esteira pra produção através de PR's pra MAIN)

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

### Como rodar na Instância EC2
Após estar com o servidor instanciado, conecte-se com sua chave
Instale as dependências do projeto
sudo apt install -y php8.2 php8.2-cli php8.2-mysql mysql-client composer unzip nginx git
Vá para o diretorio onde deseja clonar o projeto

git clone https://github.com/VitorLedes/alpesone-api
cd /caminho/do/projeto
composer install

-- Criar database
sudo mysql
CREATE DATABASE nome_da_base;

-- Criar usuário
CREATE USER 'usuario_do_projeto'@'localhost' IDENTIFIED BY 'senha_segura';

-- Dar permissões no banco
GRANT ALL PRIVILEGES ON nome_da_base.* TO 'usuario_do_projeto'@'localhost';

-- Configurar o .env com suas novas credenciais
DB_DATABASE=nome_da_base
DB_USERNAME=usuario_do_projeto
DB_PASSWORD=senha_segura
APP_ENV=production
APP_DEBUG=false

-- Rodar migrations
php artisan migrate --force

-- Ajustar permissões para o usuário do Nginx
sudo chown -R www-data:www-data storage bootstrap/cache

-- Configurar Nginx para apontar para a pasta public/ do projeto

-- Reiniciar Nginx
sudo systemctl restart nginx

-- Subir pra produção
Apenas basta concluir um PR para a branch main que o Github Actions vai executar os passos do deploy.yml, que assim, irá chamar o deploy.sh no servidor

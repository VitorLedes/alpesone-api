PROJECT_PATH="/var/www/alpesone-api"
GITHUB_REPOSITORY="https://github.com/VitorLedes/alpesone-api"
BACKUP_PATH="/var/backup/alpesone-api"
LOG_FILE="/var/log/alpesone-api/deploy.log"

# Criando o arquivo de logs (esse 2>/dev/null é pra não exibir nenhuma mensagem)
mkdir -p "$(dirname "$LOG_FILE")" 2>/dev/null

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # Sem cor

log() {
    echo -e "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> $LOG_FILE
}

print_status() {
    echo -e "${GREEN}[INFO]${NC} - $1"
    log "[INFO] - $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} - $1"
    log "[WARNING] - $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} - $1"
    log "[ERROR] - $1"
}

check_permissions() {

    if [ $EUID -ne 0 ]; then
        print_error "Este script deve ser executado como root ou com sudo."
        exit 1
    fi

}

create_backup() {
    print_status "Criando backup da versão atual..."

    if [ -d "$PROJECT_PATH" ]; then
        TIMESTAMP=$(date '+%Y%m%d%H%M%S')
        BACKUP_NAME="alpesone-api-backup-$TIMESTAMP"

        mkdir -p "$BACKUP_PATH"
        cp -r "$PROJECT_PATH" "$BACKUP_PATH/$BACKUP_NAME"
        print_status "Backup criado em $BACKUP_PATH/$BACKUP_NAME"
    else
        print_warning "Diretório do projeto não existe. Pulando backup."    
    fi
}

update_code() {
    print_status "Atualizando código do repositório..."

    if [ -d "$PROJECT_PATH/.git" ]; then
        print_status "Repositório existente, aplicando mudanças..."
        cd "$PROJECT_PATH"
        git fetch origin
        git reset --hard origin/main
        git pull origin main
    else
        print_status "Clonando repositório..."
        rm -rf "$PROJECT_PATH"
        git clone "$GITHUB_REPOSITORY" "$PROJECT_PATH"
        cd "$PROJECT_PATH"
    fi
}

install_dependencies() {

    print_status "Instalando dependencias..."
    cd "$PROJECT_PATH"

    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader
        print_status "Dependencias instaladas com sucesso!"
    else
        print_error "Arquivo composer.json não encontrado!"
        exit 1
    fi

}

set_permissions() {

    print_status "Configurando permissões..."

    cd "$PROJECT_PATH"

    chown -R www-data:www-data .

    find . -type d -exec chmod 755 {} \;

    find . -type f -exec chmod 644 {} \;

    chmod -R 755 storage bootstrap/cache

    print_status "Permissões configuradas"

}

run_laravel_commands() {
    print_status "Rodando comandos do Laravel..."
    cd "$PROJECT_PATH"

    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear

    print_warning "Deseja executar as migrations? (y/N)"
    read -r run_migrations

    if [ "$run_migrations" = "y" ]; then
        php artisan migrate --force
        print_status "Migrations executadas"
    fi

    print_status "Comandos do Laravel executados com sucesso!"
}

restart_server() {
    print_status "Reiniciando Nginx"

    systemctl restart nginx
    print_status "Nginx reiniciado"

}

health_check() {

    print_status "Verificando saúde da aplicação..."

    # AGuarda um pouco pros serviços subierem
    sleep 5

    # Faz requisição pra aplicação pra verificar se está tudo certo
    if curl -f -s http://localhost; then
        print_status "Aplicação respondendo normalmente!"
    else
        print_error "Aplicação não está respondendo, verifique os Logs!!!"
    fi
}

main() {

    print_status "=== INICIANDO DEPLOY DA APLICAÇÃO ==="
    print_status "Início em: $(date '+%Y%m%d%H%M%S')"

    check_permissions
    set_permissions
    create_backup
    update_code
    install_dependencies
    run_laravel_commands
    restart_server
    health_check

    print_status "=== DEPLOY CONCLUÍDO COM SUCESSO ==="
    print_status "LOGS SALVOS EM: $LOG_FILE"
    print_status "Finalizou em: $(date '+%Y%m%d%H%M%S')"
}

main "$@"
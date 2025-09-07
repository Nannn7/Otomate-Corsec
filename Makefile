# 🐳 Laravel 12 Docker Makefile
# Command shortcuts untuk development dengan Docker

# Colors
GREEN := \033[32m
BLUE := \033[34m
YELLOW := \033[33m
RED := \033[31m
RESET := \033[0m

# Default database profile
DB_PROFILE ?= mysql

.PHONY: help
help: ## Tampilkan bantuan perintah
	@echo "$(BLUE)🐳 Laravel 12 Docker Commands$(RESET)"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "$(GREEN)%-20s$(RESET) %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: up
up: ## Jalankan semua services dengan database yang dipilih
	@echo "$(BLUE)🚀 Starting services with $(DB_PROFILE)...$(RESET)"
	docker-compose --profile $(DB_PROFILE) up -d

.PHONY: down
down: ## Stop semua services
	@echo "$(RED)🛑 Stopping all services...$(RESET)"
	docker-compose --profile $(DB_PROFILE) down

.PHONY: restart
restart: down up ## Restart semua services

.PHONY: build
build: ## Build/rebuild semua images
	@echo "$(YELLOW)🔨 Building images...$(RESET)"
	docker-compose --profile $(DB_PROFILE) build

.PHONY: logs
logs: ## Lihat logs dari semua services
	docker-compose --profile $(DB_PROFILE) logs -f

.PHONY: logs-app
logs-app: ## Lihat logs dari app service
	docker-compose --profile $(DB_PROFILE) logs -f app

.PHONY: logs-db
logs-db: ## Lihat logs dari database service
	docker-compose --profile $(DB_PROFILE) logs -f db-$(DB_PROFILE)

.PHONY: status
status: ## Lihat status semua services
	docker-compose --profile $(DB_PROFILE) ps

.PHONY: shell
shell: ## Masuk ke container app
	docker-compose --profile $(DB_PROFILE) exec app bash

.PHONY: migrate
migrate: ## Jalankan database migrations
	@echo "$(GREEN)🗄️ Running migrations...$(RESET)"
	docker-compose --profile $(DB_PROFILE) exec app php artisan migrate

.PHONY: migrate-fresh
migrate-fresh: ## Fresh migrate dengan seed
	@echo "$(YELLOW)🔄 Fresh migrate with seed...$(RESET)"
	docker-compose --profile $(DB_PROFILE) exec app php artisan migrate:fresh --seed

.PHONY: seed
seed: ## Jalankan database seeders
	@echo "$(GREEN)🌱 Running seeders...$(RESET)"
	docker-compose --profile $(DB_PROFILE) exec app php artisan db:seed

.PHONY: test
test: ## Jalankan PHPUnit tests
	@echo "$(BLUE)🧪 Running tests...$(RESET)"
	docker-compose --profile $(DB_PROFILE) exec app php artisan test

.PHONY: tinker
tinker: ## Jalankan Laravel Tinker
	docker-compose --profile $(DB_PROFILE) exec app php artisan tinker

.PHONY: composer-install
composer-install: ## Install Composer dependencies
	docker-compose --profile $(DB_PROFILE) run --rm app composer install

.PHONY: composer-update
composer-update: ## Update Composer dependencies
	docker-compose --profile $(DB_PROFILE) run --rm app composer update

.PHONY: npm-install
npm-install: ## Install NPM dependencies
	docker-compose --profile $(DB_PROFILE) run --rm app npm install

.PHONY: npm-build
npm-build: ## Build assets
	docker-compose --profile $(DB_PROFILE) run --rm app npm run build

.PHONY: npm-dev
npm-dev: ## Build assets untuk development
	docker-compose --profile $(DB_PROFILE) run --rm app npm run dev

.PHONY: cache-clear
cache-clear: ## Clear Laravel cache
	docker-compose --profile $(DB_PROFILE) exec app php artisan cache:clear
	docker-compose --profile $(DB_PROFILE) exec app php artisan config:clear
	docker-compose --profile $(DB_PROFILE) exec app php artisan route:clear
	docker-compose --profile $(DB_PROFILE) exec app php artisan view:clear

.PHONY: key-generate
key-generate: ## Generate Laravel app key
	docker-compose --profile $(DB_PROFILE) exec app php artisan key:generate

.PHONY: queue-work
queue-work: ## Jalankan queue worker
	docker-compose --profile $(DB_PROFILE) --profile worker up -d

.PHONY: mysql
mysql: ## Masuk ke MySQL CLI (hanya untuk MySQL)
ifeq ($(DB_PROFILE),mysql)
	docker-compose --profile mysql exec db-mysql mysql -u laravel -p laravel
else
	@echo "$(RED)❌ MySQL hanya tersedia dengan DB_PROFILE=mysql$(RESET)"
endif

.PHONY: postgres
postgres: ## Masuk ke PostgreSQL CLI (hanya untuk PostgreSQL)
ifeq ($(DB_PROFILE),postgres)
	docker-compose --profile postgres exec db-postgres psql -U laravel -d laravel
else
	@echo "$(RED)❌ PostgreSQL hanya tersedia dengan DB_PROFILE=postgres$(RESET)"
endif

.PHONY: backup
backup: ## Backup database
ifeq ($(DB_PROFILE),mysql)
	@echo "$(GREEN)💾 Backing up MySQL database...$(RESET)"
	docker-compose --profile mysql exec db-mysql mysqldump -u laravel -p laravel laravel > backup-$(shell date +%Y%m%d-%H%M%S).sql
else
	@echo "$(GREEN)💾 Backing up PostgreSQL database...$(RESET)"
	docker-compose --profile postgres exec db-postgres pg_dump -U laravel laravel > backup-$(shell date +%Y%m%d-%H%M%S).sql
endif

.PHONY: prune
prune: ## Hapus unused Docker resources
	@echo "$(RED)🧹 Cleaning up Docker resources...$(RESET)"
	docker system prune -f
	docker volume prune -f

.PHONY: clean
clean: down ## Hapus semua data termasuk volumes
	@echo "$(RED)🗑️ Removing all data...$(RESET)"
	docker-compose --profile $(DB_PROFILE) down -v

# Database switching commands
.PHONY: use-mysql
use-mysql: ## Switch ke MySQL
	@echo "$(GREEN)🔵 Switching to MySQL...$(RESET)"
	@sed -i '' 's/DB_CONNECTION=pgsql/DB_CONNECTION=mysql/' .env 2>/dev/null || true
	@sed -i '' 's/DB_HOST=db-postgres/DB_HOST=db-mysql/' .env 2>/dev/null || true
	@echo "$(GREEN)✅ Database switched to MySQL$(RESET)"
	@echo "$(YELLOW)💡 Run 'make up DB_PROFILE=mysql' untuk start$(RESET)"

.PHONY: use-postgres
use-postgres: ## Switch ke PostgreSQL
	@echo "$(GREEN)🟢 Switching to PostgreSQL...$(RESET)"
	@sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/' .env 2>/dev/null || true
	@sed -i '' 's/DB_HOST=db-mysql/DB_HOST=db-postgres/' .env 2>/dev/null || true
	@echo "$(GREEN)✅ Database switched to PostgreSQL$(RESET)"
	@echo "$(YELLOW)💡 Run 'make up DB_PROFILE=postgres' untuk start$(RESET)"

# Development workflow
.PHONY: setup
setup: ## Setup awal project
	@echo "$(BLUE)🚀 Setting up Laravel project...$(RESET)"
	cp .env.example .env
	make composer-install
	make npm-install
	make npm-build
	@echo "$(GREEN)✅ Setup selesai!$(RESET)"
	@echo "$(YELLOW)💡 Run 'make up' untuk memulai development$(RESET)"

.PHONY: dev
setup-dev: ## Setup untuk development
	make setup
	make up
	make key-generate
	make migrate-fresh
	@echo "$(GREEN)🎉 Development environment ready!$(RESET)"
	@echo "$(BLUE)🌐 Open http://localhost:8080$(RESET)"

.PHONY: setup-pg
setup-pg: ## Setup awal project
	@echo "$(BLUE)🚀 Setting up Laravel project...$(RESET)"
	cp .env.example .env
	make composer-install DB_PROFILE=postgres
	make npm-install DB_PROFILE=postgres
	make npm-build DB_PROFILE=postgres
	@echo "$(GREEN)✅ Setup selesai!$(RESET)"
	@echo "$(YELLOW)💡 Run 'make up' untuk memulai development$(RESET)"

.PHONY: dev-pg
setup-dev-pg: ## Setup untuk development
	make use-postgres
	make setup-pg
	make up DB_PROFILE=postgres
	make key-generate DB_PROFILE=postgres
	make migrate-fresh DB_PROFILE=postgres
	@echo "$(GREEN)🎉 Development environment ready!$(RESET)"
	@echo "$(BLUE)🌐 Open http://localhost:8080$(RESET)"

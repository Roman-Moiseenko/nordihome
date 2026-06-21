# Docker Compose команды для быстрого запуска проекта

.PHONY: help up down build build-no-cache restart php-shell node-shell mysql-shell logs ps migrate fresh seed test

help: ## Показать эту справку
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
	awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Запустить все контейнеры
	docker-compose up -d

down: ## Остановить и удалить контейнеры
	docker-compose down

build: ## Собрать образы
	docker-compose build

build-no-cache: ## Собрать образы без кеша
	docker-compose build --no-cache

restart: ## Перезапустить все контейнеры
	docker-compose restart

# --- Shell внутри контейнеров ---
php-shell: ## Войти в консоль PHP-контейнера
	docker-compose exec php sh

node-shell: ## Войти в консоль Node-контейнера
	docker-compose exec node sh

mysql-shell: ## Войти в MySQL
	docker-compose exec mysql mysql -u nordihome -p nordihome

# --- Логи ---
logs: ## Посмотреть логи всех контейнеров
	docker-compose logs -f

# --- Laravel команды ---
artisan: ## Запустить artisan (пример: make artisan tinker)
	docker-compose exec php php artisan $(cmd)

migrate: ## Запустить миграции
	docker-compose exec php php artisan migrate

fresh: ## Сбросить и пересоздать БД с сидами
	docker-compose exec php php artisan migrate:fresh --seed

seed: ## Запустить сиды
	docker-compose exec php php artisan db:seed

# --- Composer ---
composer: ## Запустить composer (пример: make composer cmd='require package')
	docker-compose exec php composer $(cmd)

# --- NPM ---
npm: ## Запустить npm (пример: make npm cmd='install lodash')
	docker-compose exec node npm $(cmd)

# --- Тесты ---
test: ## Запустить тесты
	docker-compose exec php php artisan test

test-coverage: ## Запустить тесты с покрытием
	docker-compose exec php php artisan test --coverage

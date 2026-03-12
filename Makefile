include .env

#-----------------------------------------------------------
# Docker
#-----------------------------------------------------------

# Start all containers
up:
	docker-compose up -d

# Stop all containers
down:
	docker-compose down

# Show container status
status:
	docker-compose ps

s: status

# Show all logs
logs:
	docker-compose logs

# Watch client logs
logs-client:
	docker logs -f ${APP_NAME}_client

logs-c: logs-client

# Restart all containers
restart: down up

# Restart client container
restart-client:
	docker-compose restart client

rc: restart-client

# Build and start containers
build:
	docker-compose up -d --build

# Build with no cache
build-no-cache:
	docker-compose build --no-cache

# Rebuild containers
rebuild: down build

#-----------------------------------------------------------
# Container Access
#-----------------------------------------------------------

# Access PHP container
php:
	docker exec -it --user www-data ${APP_NAME}_php bash

# Access client container
client:
	docker exec -it --user node ${APP_NAME}_client sh

# Access postgres container
postgres:
	docker exec -it --user postgres ${APP_NAME}_postgres bash

#-----------------------------------------------------------
# Database
#-----------------------------------------------------------

# Run migrations
db-migrate:
	docker-compose exec php php artisan migrate

# Run migrations with force
db-migrate-force:
	docker-compose exec -T php php artisan migrate --force

# Rollback migrations
db-rollback:
	docker-compose exec php php artisan migrate:rollback

# Seed database
db-seed:
	docker-compose exec php php artisan db:seed

# Fresh migrations
db-fresh:
	docker-compose exec php php artisan migrate:fresh

# Fresh with seed
db-fresh-seed: db-fresh db-seed

#-----------------------------------------------------------
# Testing
#-----------------------------------------------------------

# Run PHP tests
test-php:
	docker-compose exec php vendor/bin/phpunit --order-by=defects --stop-on-defect

# Run all PHP tests
test-php-all:
	docker-compose exec php vendor/bin/phpunit --order-by=defects

# Run PHP tests with coverage
test-php-coverage:
	docker-compose exec php vendor/bin/phpunit --coverage-html tests/report

# Run client tests
test-client:
	docker-compose exec client npm run test

# Run both PHP and client tests
test: test-php-all test-client

# PHP linting
lint-php-pint:
	docker-compose exec php composer pint:test
	
lint-php-stan:
	docker-compose exec php composer stan

lint-php-fix:
	docker-compose exec php composer lint

lint-php: lint-php-pint lint-php-stan

# Client linting
lint-client:
	docker-compose exec client npm run lint

lint-client-fix:
	docker-compose exec client npm run lint:eslint

# Run all linting
lint: lint-php lint-client

lint-fix: lint-php-fix lint-client-fix

#-----------------------------------------------------------
# Dependencies
#-----------------------------------------------------------

# Install composer dependencies
composer-install:
	docker-compose exec php composer install

# Install composer dependencies (non-interactive)
composer-install-t:
	docker-compose exec -T php composer install

# Update composer dependencies
composer-update:
	docker-compose exec php composer update

# Install npm dependencies
npm-install:
	docker-compose exec client npm install

# Update npm dependencies
npm-update:
	docker-compose exec client npm update

# Update all dependencies
update-deps: composer-update npm-update

#-----------------------------------------------------------
# Installation
#-----------------------------------------------------------

# Copy API environment file
env-api:
	cp .env.api api/.env

# Copy client environment file
env-client:
	cp .env.client client/.env

# Set Laravel permissions
permissions:
	sudo chown -R $$USER:www-data api/storage
	sudo chown -R $$USER:www-data api/bootstrap/cache
	sudo chmod -R 775 api/bootstrap/cache
	sudo chmod -R 775 api/storage

# Permissions alias
perm: permissions

# Generate Laravel key
key:
	docker-compose exec php php artisan key:generate --ansi
	docker-compose exec php php artisan jwt:secret --ansi

# Create storage symlink
storage:
	docker-compose exec php php artisan storage:link

# PHP composer autoload
autoload:
	docker-compose exec php composer dump-autoload

# Install git hooks
install-hooks:
	./scripts/install-hooks.sh

# Full installation
install: build env-api env-client composer-install key storage permissions db-migrate install-hooks rc

#-----------------------------------------------------------
# Artisan Commands
#-----------------------------------------------------------

# Run artisan tinker
tinker:
	docker-compose exec php php artisan tinker

# Clear application cache
cache-clear:
	docker-compose exec php php artisan cache:clear
	docker-compose exec php php artisan config:clear
	docker-compose exec php php artisan route:clear
	docker-compose exec php php artisan view:clear

#-----------------------------------------------------------
# Cleanup
#-----------------------------------------------------------

# Clear nginx logs
logs-clear:
	sudo rm -f docker/nginx/logs/*.log
	sudo rm -f api/storage/logs/*.log

# Remove all volumes
remove-volumes:
	docker-compose down --volumes

# Prune docker networks
prune-networks:
	docker network prune

# Full cleanup
clean: down remove-volumes prune-networks

export php := 8.1
export composerflags :=

test-all:
	@make test php=8.0 composerflags="--prefer-stable"
	@make test php=8.0 composerflags="--prefer-lowest"

	@make test php=8.1 composerflags="--prefer-stable"
	@make test php=8.1 composerflags="--prefer-lowest"

test:
	@make up
	@make install-vendor
	@docker-compose exec php-$(php) vendor/bin/phpunit
	@make down

install-vendor:
	@docker-compose exec php-$(php) composer install  --quiet
	@docker-compose exec php-$(php) composer update $(composerflags)  --quiet

up:
	@docker-compose up -d php-$(php)

down:
	@docker-compose down --remove-orphans

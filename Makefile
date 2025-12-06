install:
	composer install

lint:
	phpcs --standard=PSR12 --extensions=php src/ bin/

lint-fix:
	phpcbf --standard=PSR12 --extensions=php src/

test:
		./vendor/bin/phpunit --testdox tests/

test-coverage:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover=coverage.xml --coverage-filter=src tests/

.PHONY: lint
.PHONY: lint-fix
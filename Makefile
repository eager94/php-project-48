lint:
	phpcs --standard=PSR12 --extensions=php src/ bin/

lint-fix:
	phpcbf --standard=PSR12 --extensions=php src/

install:
	composer install


.PHONY: lint
.PHONY: lint-fix
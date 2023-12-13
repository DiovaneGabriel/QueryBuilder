.PHONY: test

test:
	clear && \
	docker exec php-test php ./test/test.php

install:
	clear && \
	docker compose up -d && \
	docker exec -it php-test sh -c "cd /var/www/html && composer update"

up:
	docker compose up -d

down:
	docker compose down
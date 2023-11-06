#!/usr/bin/env bash

docker compose exec frankenphp /app/vendor/bin/phpunit --bootstrap /app/test/bootstrap.php test

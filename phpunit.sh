#!/usr/bin/env bash

cd "$(dirname "$0")"

docker run --rm -it -v "$(pwd -P):/app" php:8.0-rc-cli-alpine /app/vendor/bin/phpunit "/app/src/$@"

#!/bin/bash

echo "If tests fail with PHP 7.4, please run:"
echo ""
echo "    composer update --prefer-lowest"
echo ""

echo "Running tests on PHP 7.4"
APP_DIR="`dirname $PWD`" docker-compose -p mnormalization run php74 vendor/bin/phpunit "$@"

echo "Running tests on PHP 8.0"
APP_DIR="`dirname $PWD`" docker-compose -p mnormalization run php80 vendor/bin/phpunit "$@"

echo "Running tests on PHP 8.1"
APP_DIR="`dirname $PWD`" docker-compose -p mnormalization run php81 vendor/bin/phpunit "$@"

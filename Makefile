# --------------------------------#
# Makefile for the "make" command
# --------------------------------#

GREEN = echo "\e[32m\# $1\e[0m"
RED = echo "\e[31m\# $1\e[0m"

COMPOSER = composer
COMPOSE = docker-compose
SYMFONY = symfony
CONSOLE = bin/console
BUILD = bin/build
TEST = vendor/bin/pest
LOCAL_TEST = bin/test

init:
	$(MAKE) build

build:
	$(BUILD)

rebuild:
	bin/stop
	bin/clean
	$(BUILD)

test:
	$(TEST)

local-test:
	$(LOCAL_TEST)

migrate:
	bin/migrate
	bin/migrate -e test

load-fixtures:
	$(COMPOSE) run --rm srvc_php $(CONSOLE) doctrine:fixtures:load --env test --no-interaction


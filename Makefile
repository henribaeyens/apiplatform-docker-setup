# --------------------------------#
# Makefile for the "make" command
# --------------------------------#

RED:=\033[0;31m
GREEN:=\033[0;32m
YELLOW:=\033[0;33m
NO_COLOR:=\033[0m

DOCKER_RUN = docker run
COMPOSE = docker-compose
SYMFONY = symfony
CONSOLE = bin/console
BUILD = bin/build
STOP = bin/stop
CLEAN = bin/clean
TEST = bin/test
MIGRATE = bin/migrate
LOADFIXTURES = bin/load-fixtures
CSFIXER=bin/csfixer
PHPSTAN=bin/phpstan

init:
	$(MAKE) build

build:
	@echo "$(GREEN)building containers and initializing$(NO_COLOR)"
	$(BUILD)

rebuild:
	$(STOP)
	$(CLEAN)
	$(BUILD)

test:
	@echo "$(GREEN)running Pest test suite$(NO_COLOR)"
	$(TEST)

migrate:
	@echo "$(GREEN)running migrations$(NO_COLOR)"
	$(MIGRATE)
	$(MIGRATE) -e test

load-fixtures:
	@echo "$(GREEN)loading fixture$(NO_COLOR)"
	$(LOADFIXTURES)

csfixer-dryrun:
	@echo "$(GREEN)running cs-fixer on dry run mode$(NO_COLOR)"
	$(CSFIXER) -dryrun

csfixer:
	@echo "$(GREEN)running cs-fixer$(NO_COLOR)"
	$(CSFIXER)

phpstan:
	@echo "$(GREEN)running phpstan$(NO_COLOR)"
	$(PHPSTAN)
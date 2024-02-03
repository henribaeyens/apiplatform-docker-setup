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
PHPQA = jakzal/phpqa:php8.1
PHPQA_RUN = $(DOCKER_RUN) --init --rm -v $(PWD):/project -w /project $(PHPQA)

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
	vendor/bin/pest

host-test:
	@echo "$(GREEN)running Pest test suite (host)$(NO_COLOR)"
	$(TEST)

migrate:
	@echo "$(GREEN)running migrations$(NO_COLOR)"
	$(MIGRATE)
	$(MIGRATE) -e test

load-fixtures:
	@echo "$(GREEN)loading fixture$(NO_COLOR)"
	$(COMPOSE) run --rm srvc_php $(CONSOLE) doctrine:fixtures:load --env test --no-interaction

csfixer-dryrun:
	@echo "$(GREEN)running cs-fixer on dry run mode$(NO_COLOR)"
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose --dry-run

csfixer:
	@echo "$(GREEN)running cs-fixer$(NO_COLOR)"
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose

phpstan:
	@echo "$(GREEN)running phpstan$(NO_COLOR)"
	$(PHPQA_RUN) phpstan ./src --rules=@Symfony --verbose

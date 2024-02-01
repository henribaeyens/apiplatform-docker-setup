# --------------------------------#
# Makefile for the "make" command
# --------------------------------#

GREEN = /bin/echo -e "\e[32m\# $1\e[0m"
RED = /bin/echo -e "\e[31m\# $1\e[0m"

COMPOSER = composer
SYMFONY = symfony
CONSOLE = bin/console
BUILD = bin/build
TEST = vendor/bin/pest
LOCAL_TEST = bin/test

init:
	$(MAKE) build
	@$(call GREEN, "Project initialized!")

build:
	@$(call GREEN, "Building and initializing project")
	$(BUILD)

rebuild:
	bin/stop
	bin/clean
	$(BUILD)

test:
	@$(call GREEN, "Running tests")
	$(TEST)

local-test:
	@$(call GREEN, "Running tests from  local shell")
	$(LOCAL_TEST)

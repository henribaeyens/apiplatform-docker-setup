# --------------------------------#
# Makefile for the "make" command
# --------------------------------#

# ----- Colors -----
GREEN = /bin/echo -e "\032[0;32m\#\# $1\033[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

# ----- Programs -----
COMPOSER = composer
SYMFONY = symfony
CONSOLE = bin/console
BUILD = bin/build
TEST = bin/test

init:
	$(MAKE) build
	@$(call GREEN, "Project initialized!")

build:
	@$(call GREEN, "Building and initializing project")
	$(BUILD)

test:
	@$(call GREEN, "Running tests")
	$(TEST)



# --------------------------------#
# Makefile for the "make" command
# --------------------------------#

# ----- Colors -----
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

# ----- Programs -----
COMPOSER = composer
SYMFONY = symfony
CONSOLE = bin/console
BUILD = bin/build
TEST = /vendor/bin/pest

init:
	$(MAKE) build
	@$(call GREEN, "Project initialized!")

build:
	@$(call GREEN, "Building and initializing project")
	$(BUILD)


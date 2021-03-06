# Makefile for car-pooling-challenge
# vim: set ft=make ts=8 noet
# Copyright Cabify.com
# Licence MIT

# Variables
# UNAME		:= $(shell uname -s)

.EXPORT_ALL_VARIABLES:

# this is godly
# https://news.ycombinator.com/item?id=11939200
.PHONY: help
help:	### this screen. Keep it first target to be default
ifeq ($(UNAME), Linux)
	@grep -P '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
else
	@# this is not tested, but prepared in advance for you, Mac drivers
	@awk -F ':.*###' '$$0 ~ FS {printf "%15s%s\n", $$1 ":", $$2}' \
		$(MAKEFILE_LIST) | grep -v '@awk' | sort
endif

# Targets
#
.PHONY: db-create
db-create: db-drop
	php bin/console doctrine:database:create --no-interaction
	php bin/console doctrine:schema:update --force --no-interaction

.PHONY: db-drop
db-drop:
	php bin/console doctrine:database:drop --force

.PHONY: debug
debug:	### Debug Makefile itself
	@echo $(UNAME)

.PHONY: dockerize
dockerize: build
	@docker build -t car-pooling-challenge:latest .

.PHONY: cs
cs: ### Validate all code standards
	./vendor/bin/php-cs-fixer fix src --diff --rules=@PSR12 --dry-run
	./vendor/bin/php-cs-fixer fix tests --diff --rules=@PSR12 --dry-run

.PHONY: cs-fix
cs-fix: ### fix psr-12 code standards
	./vendor/bin/php-cs-fixer fix src --diff --rules=@PSR12
	./vendor/bin/php-cs-fixer fix tests --diff --rules=@PSR12

.PHONY: test
test: unit ### run tests

.PHONY: unit
unit: ### run unit testing
	./vendor/bin/phpunit \
		--exclude-group='disabled' --testdox

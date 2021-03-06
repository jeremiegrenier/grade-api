.DEFAULT_GOAL := help
SHELL := /bin/bash

.PHONY: build cs docker-build docker-clean docker-create-db docker-dependency docker-fix-permissions docker-logs docker-run docker-run-test docker-sh docker-sh-db docker-stop help install it test

## Install project from scratch
install: docker-run docker-dependency docker-create-db docker-create-schema

## Clean system from docker image
docker-clean:
	docker system prune -a -f

## Build docker image
docker-build:
	docker build -t grade-api .

## Run Docker images
docker-run: docker-stop
	docker-compose up -d

## Stop Docker images
docker-stop:
	docker-compose down

## Attach to main Docker image
docker-sh:
	docker-compose exec grade-api sh

## Attach to database Docker image
docker-sh-db:
	docker-compose exec database sh

## Watch logs from all Docker images
docker-logs:
	docker-compose --env-file docker-compose.env logs -f

## Fix permission on docker image
docker-fix-permissions:
	docker-compose run --rm grade-api chown -R $$(id -u):$$(id -g) .

## Create database on first install
docker-create-db:
	docker-compose exec grade-api sh -c 'php bin/console doctrine:database:create'

## Create schema for database
docker-create-schema:
	docker-compose exec grade-api sh -c 'php bin/console doctrine:schema:create'

## Install dependency
docker-dependency:
	docker-compose exec grade-api sh -c 'make build'

## Run tests on launched docker image
docker-run-test: docker-run
	docker-compose exec grade-api sh -c 'make test'

## Install dependency inside docker image
build:
	composer validate
	composer install

## Run cs fixer to linf php files
cs: build
	vendor/bin/php-cs-fixer fix --config=.php_cs --diff --verbose

## Run tests
test: build
	php bin/console --env=test doctrine:database:create
	php bin/console --env=test doctrine:schema:create
	php bin/phpunit
	php bin/console --env=test doctrine:database:drop --force

## Run tests unitaire
test-unit: build
	php bin/phpunit --testsuite unit

## Clean code and run tests
it: build cs test

## ------

# APPLICATION
APPLICATION := $(shell cat composer.json | grep "\"name\"" | cut -d\" -f 4 )

# COLORS
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

TARGET_MAX_CHAR_NUM=20
## Show this help
help:
	@echo '# ${YELLOW}${APPLICATION}${RESET} / ${GREEN}${ENV}${RESET}'
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Targets:'
	@awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			gsub(":", " ", helpCommand); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${YELLOW}%-$(TARGET_MAX_CHAR_NUM)s${RESET} ${GREEN}%s${RESET}\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST) | sort

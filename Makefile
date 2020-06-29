.DEFAULT_GOAL := help
SHELL := /bin/bash

## Clean system from docker image
clean:
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

## Watch logs from all Docker images
docker-logs:
	docker-compose --env-file docker-compose.env logs -f

## Fix permission on docker image
docker-fix-permissions:
	docker-compose run --rm grade-api chown -R $$(id -u):$$(id -g) .


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

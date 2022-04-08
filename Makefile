
.PHONY: all build

## Colors
COLOR_RESET			= \033[0m
COLOR_ERROR			= \033[31m
COLOR_INFO			= \033[32m
COLOR_COMMENT		= \033[33m
COLOR_TITLE_BLOCK	= \033[0;44m\033[37m

env = dev

## Help
help:
	@printf "${COLOR_TITLE_BLOCK}UGO Customer Portal Makefile${COLOR_RESET}\n"
	@printf "\n"
	@printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	@printf " make [target]\n\n"
	@printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	@awk '/^[a-zA-Z\-\_0-9\@]+:/ { \
		helpLine = match(lastLine, /^## (.*)/); \
		helpCommand = substr($$1, 0, index($$1, ":")); \
		helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
		printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)



DOCKER_COMPOSE = docker-compose -p workflow -f docker-compose.yaml -f docker-compose.override.yaml

CONTAINER_ID_PHP = $$(docker container ls -f "name=box-deal-erp_php" -q)
CONTAINER_ID_DB = $$(docker container ls -f "name=box-deal-erp_db" -q)
NODE=
PHP = symfony
DB = docker exec -ti $(CONTAINER_ID_DB) 


######### DOCKER COMPOSE COMMANDS #########

CONTAINER=

## kill docker containers
docker-kill:
	@$(DOCKER_COMPOSE) kill $(CONTAINER) || true

## delete docker containers
docker-rm:
	@$(DOCKER_COMPOSE) rm -f $(CONTAINER) || true

## build docker containers
docker-build:
	@$(DOCKER_COMPOSE) build $(CONTAINER)

## build docker containers, no caching
docker-build-no-cache:
	@$(DOCKER_COMPOSE) build --no-cache --pull $(CONTAINER)

## launch docker containers
docker-up:
	@$(DOCKER_COMPOSE) up $(CONTAINER)

## display docker containers state
docker-ps:
	@$(DOCKER_COMPOSE) ps $(CONTAINER) || true

## display docker logs
docker-logs:
	@$(DOCKER_COMPOSE) logs $(CONTAINER) || true

## launch docker containers, no rebuild
start:
	docker-compose up -d --no-recreate

## launch docker containers in background mode, no rebuild
start-detached:
	@$(DOCKER_COMPOSE) up --no-recreate -d

## launch docker containers in background mode, no rebuild
start-daemon: start-detached port

## stop docker containers
stop:
	@$(DOCKER_COMPOSE) stop

## restart docker containers (rebuild after kill and remove of the containers)
restart: docker-kill docker-rm docker-build start

## restart docker containers in background mode (rebuild after kill and remove of the containers)
restart-daemon: docker-kill docker-rm docker-build start-daemon

## restart docker containers, no caching (rebuild after kill and remove of the containers)
restart-no-cache: docker-kill docker-rm docker-build-no-cache start

######### CONNECT TO CONTAINERS #########


## shell connect in db container
shell-db:
	@$(DOCKER_COMPOSE) exec db bash

start-localhost:
	$(PHP) server:start -d

stop-localhost:
	$(PHP) server:stop

run-dev: start start-localhost yarn-run-dev
stop-dev: stop stop-localhost
######### COMPOSER #########

## run composer install
composer-install:
	$(PHP) composer install

## run composer update
composer-update:
	$(PHP) composer update

## show installed composer dependencies
composer-show-installed:
	$(PHP) composer show -i

composer-validate:
	$(PHP) composer validate

composer-ci:
	$(PHP) composer ci

## run yarn install
yarn-install:
	$(NODE) yarn install

yarn-run-dev:
	$(NODE) yarn run dev

## run yarn encore prod
npm-build:
	$(NODE) npm run-script build

clean-client:
	$(NODE) rm -rf node_modules
	$(NODE) rm -rf build


######### DATABASE #########



## mysql connect on project's database
db-connect:
	$(DB) mysql -u root --password=azerty customer_portal_db

## drop the database
db-drop:
	$(PHP) console doctrine:database:drop --if-exists --force --env=$(env)

## create the database
db-create:
	$(PHP) console doctrine:database:create --if-not-exists --env=$(env)

## dump mysql updates to be made
db-dump-updates:
	$(PHP) console doctrine:schema:update --dump-sql  --env=$(env)

## update the database
db-schema-update:
	$(PHP) console doctrine:schema:update --force --env=$(env)

## validate database schema
db-schema-validate:
	$(PHP) console doctrine:schema:validate

## load the fixtures

fixtures:
	$(PHP) console doctrine:fixtures:load -n --env=$(env)

## init database (drop if exists before creation and schema update)
db-init: db-create db-schema-update

prepare:
	make db-init env=$(env)
	make fixtures env=$(env)

prepare-test:
	make db-init env=test
	make fixtures env=test

tests: 
	symfony php bin/phpunit --testdox
.PHONY: tests

phpinsights-fix:
	vendor/bin/phpinsights analyse src tests --fix --no-interaction

phpinsights:
	vendor/bin/phpinsights --no-interaction

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon src --no-progress

analyse: phpinsights-fix phpstan

debug-env:
	symfony console debug:dotenv

dump-env-vars:
	symfony console debug:container --env-vars

cache-clear:
	symfony console cache:clear --env=$(env)

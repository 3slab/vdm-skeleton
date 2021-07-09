.PHONY: help up realup down build rm reload ps cc logs tests
.PHONY: shell-data shell-api setup-db
.PHONY: dev-collect dev-compute dev-store
.PHONY: local-collect local-consume local-store

default: help

help:
	@echo "Available make commands:\n"; \
	echo  "Docker related:"; \
	echo  "\033[0;32mup\033[0m                      Boots containers (shortcut for docker-compose up)"; \
	echo  "\033[0;32mbuild\033[0m                   Up with build container"; \
	echo  "\033[0;32mdown\033[0m                    Shuts down containers (shortcut for docker-compose down)"; \
	echo  "\033[0;32mrm\033[0m                      Remove stopped containers"; \
	echo  "\033[0;32mreload\033[0m                  Runs down + up"; \
	echo  "\033[0;32mps\033[0m                      show running containers (shortcut for docker-compose ps)"; \
	echo  "\033[0;32mcc\033[0m                      Clear sf cache"; \
	echo  "\033[0;32mlogs\033[0m                    Shows logs (all containers)"; \
	echo  "\033[0;32mtests\033[0m                   Run unit tests"; \
	echo  "\033[0;32mshell-data\033[0m              Run shell inside dataflow container"; \
	echo  "\033[0;32mshell-api\033[0m               Run shell inside api container"; \
	echo  "\033[0;32msetup-db\033[0m                Setup the database from the dataflow container"; \
	echo  "\033[0;32mdev-collect\033[0m             Run the collect command with the openweathermapcollectdev environment"; \
	echo  "\033[0;32mdev-compute\033[0m             Run the compute command with the openweathermapcomputedev environment"; \
	echo  "\033[0;32mdev-store\033[0m               Run the store command with the openweathermapstoredev environment"; \
	echo  "\033[0;32mlocal-collect\033[0m           Run the collect command with the openweathermapcollectlocal environment"; \
	echo  "\033[0;32mlocal-compute\033[0m           Run the compute command with the openweathermapcomputelocal environment"; \
	echo  "\033[0;32mlocal-store\033[0m             Run the store command with the openweathermapstorelocal environment"; \
	echo  "";

up: realup

realup:
	@docker-compose up -d; \
	echo "\nEnjoy :)\n"

down:
	@docker-compose down --remove-orphans

build:
	@docker-compose up -d --build

rm:
	@docker-compose rm

reload: down up

ps:
	@docker-compose ps

cc:
	@docker exec -it  vdm_skeleton_data_app bash -c "rm -rf /var/www/html/var/cache/*"

logs:
	@docker-compose logs -f

tests:
	@docker exec -it vdm_skeleton_data_app /bin/sh -c "vendor/bin/phpcs" && \
	docker exec -it vdm_skeleton_data_app /bin/sh -c "vendor/bin/phpunit"

shell-data:
	@docker exec -it vdm_skeleton_data_app /bin/bash

shell-api:
	@docker exec -it vdm_skeleton_api_app /bin/bash

setup-db:
	@docker exec -it vdm_skeleton_data_app /bin/sh -c "bin/console doctrine:database:create --if-not-exists && bin/console doctrine:schema:update --force"

dev-collect:
	@docker-compose exec -e VDM_APP_NAME=openweathermap_collect -e APP_ENV=openweathermapcollectdev -e VDM_PRINT_MSG=true vdm_skeleton_data_app bin/console vdm:collect openweathermap-collect -vv

dev-compute:
	@docker-compose exec -e VDM_APP_NAME=openweathermap_compute -e APP_ENV=openweathermapcomputedev -e VDM_PRINT_MSG=true vdm_skeleton_data_app bin/console vdm:consume openweathermap-compute -vv

dev-store:
	@docker-compose exec -e VDM_APP_NAME=openweathermap_store -e APP_ENV=openweathermapstoredev -e VDM_PRINT_MSG=true vdm_skeleton_data_app bin/console vdm:consume openweathermap-store -vv

local-collect:
	@docker-compose exec -e VDM_APP_NAME=openweathermap_collect -e APP_ENV=openweathermapcollectlocal -e VDM_PRINT_MSG=false vdm_skeleton_data_app bin/console vdm:collect openweathermap-collect -vv

local-compute:
	@docker-compose exec -e VDM_APP_NAME=openweathermap_compute -e APP_ENV=openweathermapcomputelocal -e VDM_PRINT_MSG=false vdm_skeleton_data_app bin/console vdm:consume openweathermap-compute -vv

local-store:
	@docker-compose exec -e VDM_APP_NAME=openweathermap_store -e APP_ENV=openweathermapstorelocal -e VDM_PRINT_MSG=false vdm_skeleton_data_app bin/console vdm:consume openweathermap-store -vv

# Determine the docker compose API version to get the separator character
VERSION?=$(shell docker-compose -v)
ifneq (,$(findstring v2.,$(VERSION)))
	SEPARATOR:=-
else
	SEPARATOR:=_
endif
CONTAINER?=$(shell basename $(CURDIR))$(SEPARATOR)php$(SEPARATOR)1

.PHONY: dev clean composer craft nuke ssh up

dev: up
clean:
	rm -f cms/composer.lock
	rm -rf cms/vendor/
composer: up
	docker exec -it $(CONTAINER) su-exec www-data composer \
		$(filter-out $@,$(MAKECMDGOALS))
craft: up
	docker exec -it $(CONTAINER) su-exec www-data php craft \
		$(filter-out $@,$(MAKECMDGOALS))
nuke:
	docker-compose down -v
	rm -f cms/composer.lock
	rm -rf cms/vendor/
	docker-compose up --build --force-recreate
ssh: up
	docker exec -it $(CONTAINER) su-exec www-data /bin/sh
up:
	if [ ! "$$(docker ps -q -f name=$(CONTAINER))" ]; then \
		cp -n cms/example.env cms/.env; \
		docker-compose up; \
    fi
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line

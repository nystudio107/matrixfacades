CONTAINER?=$(shell basename $(CURDIR))_php_1

.PHONY: dev clean composer craft nuke up update update-clean

dev: up
clean:
	docker-compose down -v
	docker-compose up --build
composer: up
	docker exec -it ${CONTAINER} composer \
		$(filter-out $@,$(MAKECMDGOALS))
craft: up
	docker exec -it ${CONTAINER} php craft \
		$(filter-out $@,$(MAKECMDGOALS))
nuke:
	docker-compose down -v
	rm -f cms/composer.lock
	rm -rf cms/vendor/
	docker-compose up --build --force-recreate
ssh: up
	docker exec -it ${CONTAINER} /bin/sh
update:
	docker-compose down
	rm -f cms/composer.lock
	docker-compose up
update-clean:
	docker-compose down
	rm -f cms/composer.lock
	rm -rf cms/vendor/
	docker-compose up
up:
	if [ ! "$$(docker ps -q -f name=${CONTAINER})" ]; then \
		cp -n cms/example.env cms/.env; \
		docker-compose up; \
    fi
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line

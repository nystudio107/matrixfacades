# nystudio107/matrixfacades Change Log

## 1.0.11 - 2022.05.03
### Changed
* Use `docker-compose` to test the version, rather than the newer `docker compose`, to be backwards compatible

## 1.0.10 - 2022.05.03
### Changed
* Determine the separator character by checking the Docker Compose API version at runtime

## 1.0.9 - 2022.05.02
### Changed
* Use `su-exec` for the `craft`, `composer`, and `ssh` commands from the Makefile

## 1.0.8 - 2022.05.02
### Fixed
* Clean up initial check for `composer.lock` or `vendor/autoload.php` to ensure the first-time install is done

## 1.0.7 - 2022.04.22
### Fixed
* Ensure that the [mysql/mysql-server](https://hub.docker.com/r/mysql/mysql-server/tags) image can pick from the ARM64 architecture by using `latest` tag

## 1.0.6 - 2022.03.26
### Added
* Added more logging to indicate when a container is waiting for another service to start up, and when seeding a database is complete
* Run `composer craft-update` after a `composer install` is done via `composer_install.sh`

### Changed
* Moved permissions setting to Docker image creation

## 1.0.5 - 2022.03.26
### Changed
* Updated to Craft CMS 3.7.37

## 1.0.4 - 2022.03.26
### Added
* Dramatically sped up the startup time for the PHP containers by moving the permissions setting script to run asynchronously in the queue container via the `run_queue.sh` script

## 1.0.3 - 2022.03.17

### Added
* Significantly increased startup times via a `composer_install.sh` script that only runs `composer install` at container startup time if `composer.lock` or `vendor/` is missing
* Run migrations / project config changes via the `run_queue.sh` script, only after the db container responds

## 1.0.2 - 2022.02.11

### Changed

* Minor tweaks to the config so it can run with other local dev environments still running

## 1.0.1 - 2022.01.22

### Added

* Added a new "Color Swatches" Matrix Façade and the "Landing Pages" channel to show off how it works

## 1.0.0 - 2022.01.19

### Added

* Initial release

Brought to you by [nystudio107](https://nystudio107.com/)

# nystudio107/matrixfacades Change Log

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

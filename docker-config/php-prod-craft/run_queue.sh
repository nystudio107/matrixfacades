#!/bin/bash

# Run Queue shell script
#
# This shell script runs the Craft CMS queue via `php craft queue/listen`
# It's wrapped in a "keep alive" infinite loop that restarts the command
# (after a 60 second sleep) should it exit unexpectedly for any reason
#
# @author    nystudio107
# @copyright Copyright (c) 2022 nystudio107
# @link      https://nystudio107.com/
# @license   MIT

# Wait until the db container responds
until eval "mysql -h mysql -u $DB_USER -p$DB_PASSWORD $DB_DATABASE -e 'select 1' > /dev/null 2>&1"
do
    sleep 1
done
# Run any pending migrations/project config changes
cd /var/www/project/cms
su-exec www-data composer craft-update
# Run a queue listener
su-exec www-data php craft queue/listen 10

#!/usr/bin/env bash

echo -n "Waiting for MySQL.."

while ! mysql --protocol TCP -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" -e"show databases;" > /dev/null 2>&1; do
	echo -n .
	sleep 1
	((counter++))

	if [ $counter -gt 45 ]; then
		>&2 echo "failed"
		exit 1
	fi
done

echo "OK"

php artisan migrate --force

vendor/bin/phpunit
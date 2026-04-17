#!/usr/bin/env bash
set -euo pipefail

composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate -n --env=prod
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
php bin/console asset-map:compile --env=prod


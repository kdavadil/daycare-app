#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
if [[ -x .tools/node/bin/node ]]; then export PATH="$PWD/.tools/node/bin:$PATH"; fi
for executable in php composer node npm docker; do
  command -v "$executable" >/dev/null || { echo "Missing prerequisite: $executable" >&2; exit 1; }
done
php -r 'exit(PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 4 ? 0 : 1);' || { echo 'Use PHP 8.4.' >&2; exit 1; }
node -e 'process.exit(Number(process.versions.node.split(".")[0]) === 24 ? 0 : 1)' || { echo 'Use Node 24 LTS (nvm use).' >&2; exit 1; }
[[ -f .env ]] || cp .env.example .env
[[ -f .env.testing ]] || cp .env.testing.example .env.testing
# This setup command is only for the isolated local configuration.
php -r '$e = parse_ini_file(".env", false, INI_SCANNER_RAW); exit(($e["APP_ENV"] ?? "") === "local" && ($e["DB_HOST"] ?? "") === "127.0.0.1" && ($e["DB_PORT"] ?? "") === "55432" && ($e["DB_DATABASE"] ?? "") === "sibol" ? 0 : 1);' || { echo 'Refusing setup outside the documented local database configuration.' >&2; exit 1; }
docker compose up -d --wait
composer install --no-interaction --prefer-dist
# Generate missing keys only; rerunning setup must never rotate an existing key.
if ! grep -Eq '^APP_KEY=.+$' .env; then php artisan key:generate --no-interaction; fi
if ! grep -Eq '^APP_KEY=.+$' .env.testing; then php artisan key:generate --env=testing --no-interaction; fi
php artisan config:clear
php artisan migrate --no-interaction
npm ci
npm run build
echo 'Ready. Run composer dev. App: http://localhost:8000 | Mail: http://localhost:8025'

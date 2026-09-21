#!/usr/bin/env bash
# Runs on the Linux VPS as its restricted deploy user. Requires PHP 8.4 and GNU coreutils.
set -euo pipefail
umask 0027
root=${1:?}; revision=${2:?}; digest=${3:?}; app_url=${4:?}; environment=${5:?}
[[ "$root" =~ ^/srv/sibol/(staging|production)$ ]]
[[ "$revision" =~ ^[a-f0-9]{40}$ && "$digest" =~ ^[a-f0-9]{64}$ ]]
[[ "$app_url" =~ ^https://[a-zA-Z0-9][a-zA-Z0-9.-]*$ ]]
[[ "$environment" = staging || "$environment" = production ]]
[[ "$root" = "/srv/sibol/$environment" ]]
exec 9>"$root/deploy.lock"
flock -n 9 || { echo 'Another deploy is running.' >&2; exit 1; }
archive="$root/incoming/$revision.tar.gz"
release="$root/releases/$revision-$(date -u +%Y%m%d%H%M%S)"
[[ -f "$root/shared/.env" && -d "$root/shared/storage" ]]
printf '%s  %s\n' "$digest" "$archive" | sha256sum -c -
mkdir "$release"
tar -xzf "$archive" -C "$release"
[[ "$(cat "$release/REVISION")" = "$revision" ]]
rm -rf "$release/storage"
ln -s "$root/shared/storage" "$release/storage"
ln -s "$root/shared/.env" "$release/.env"
cd "$release"
php -r 'exit(PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 4 ? 0 : 1);'
# Refuse insecure or missing production configuration before touching the database.
EXPECTED_ENV="$environment" php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (config("app.env") !== getenv("EXPECTED_ENV") || config("app.debug") || !config("app.key") || !config("session.secure") || config("database.default") !== "pgsql") { fwrite(STDERR, "Deployment environment is not ready.\n"); exit(1); }
'
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache
# Compiled Blade views live in shared storage; do not clear them while the old release serves traffic.
previous=$(readlink -f "$root/current" || true)
activated=false
restore_previous() {
  result=$?
  trap - ERR
  if [[ "$activated" = true && -n "$previous" && -d "$previous" ]]; then
    ln -s "$previous" "$root/current.rollback"
    mv -Tf "$root/current.rollback" "$root/current"
    sudo -n systemctl reload php8.4-fpm || true
    sudo -n systemctl restart "sibol-queue@$environment.service" "sibol-scheduler@$environment.service" || true
    echo 'Previous code release restored. Database migrations were not reversed.' >&2
  fi
  exit "$result"
}
trap restore_previous ERR
ln -s "$release" "$root/current.next"
mv -Tf "$root/current.next" "$root/current"
activated=true
sudo -n systemctl reload php8.4-fpm
sudo -n systemctl restart "sibol-queue@$environment.service" "sibol-scheduler@$environment.service"
# A response header from the application proves that the new release handles requests.
headers=$(mktemp)
trap 'rm -f "$headers"' EXIT
healthy=false
for attempt in {1..10}; do
  if curl --fail --silent --show-error --max-time 10 -D "$headers" "$app_url/ready" >/dev/null && grep -Fqi "X-Sibol-Release: $revision" "$headers"; then
    healthy=true
    break
  fi
  sleep 2
done
[[ "$healthy" = true ]] || { echo 'New release failed readiness verification.' >&2; false; }
sudo -n systemctl is-active --quiet "sibol-queue@$environment.service"
sudo -n systemctl is-active --quiet "sibol-scheduler@$environment.service"
trap - ERR
printf 'Deployed %s to %s\n' "$revision" "$environment"

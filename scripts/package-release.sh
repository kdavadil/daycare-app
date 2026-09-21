#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
revision=${1:?Usage: package-release.sh COMMIT_SHA}
[[ "$revision" =~ ^[a-f0-9]{40}$ ]] || { echo 'Expected a full commit SHA.' >&2; exit 1; }
[[ -f composer.lock && -f package-lock.json && -f public/build/manifest.json ]]
mkdir -p artifacts
stage=$(mktemp -d)
trap 'rm -rf "$stage"' EXIT
# Deliberate allowlist: never ship .env, conversations, mock-ups, tests, or local dependencies.
for entry in app bootstrap config database public resources routes artisan composer.json composer.lock; do
  cp -R "$entry" "$stage/$entry"
done
rm -rf "$stage/bootstrap/cache"
mkdir -p "$stage/bootstrap/cache" "$stage/storage/app/private" "$stage/storage/framework/cache/data" "$stage/storage/framework/sessions" "$stage/storage/framework/views" "$stage/storage/logs"
rm -f "$stage/public/hot"
if [[ -L "$stage/public/storage" ]]; then rm "$stage/public/storage"; fi
find "$stage/database" -type f \( -name '*.sqlite' -o -name '*.sqlite-*' \) -delete
printf '%s\n' "$revision" > "$stage/REVISION"
composer install --working-dir="$stage" --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress
# Avoid publishing build-machine paths in cache files. The server regenerates them.
find "$stage/bootstrap/cache" -type f -name '*.php' -delete
tar -czf artifacts/sibol-release.tar.gz -C "$stage" .
(cd artifacts && shasum -a 256 sibol-release.tar.gz > sibol-release.tar.gz.sha256)
echo 'Created artifacts/sibol-release.tar.gz and SHA-256 checksum.'

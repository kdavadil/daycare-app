#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
compose=(docker compose --env-file .hosting/compose.env -f deployment/mac/compose.yaml)
case "${1:-status}" in
  start)
    "${compose[@]}" up -d database
    "${compose[@]}" run --rm --user www-data web php artisan migrate --force --no-interaction
    "${compose[@]}" up -d
    ;;
  stop) "${compose[@]}" stop ;;
  status) "${compose[@]}" ps ;;
  link) "${compose[@]}" logs tunnel 2>&1 | sed -nE 's/.*(https:\/\/[-a-z0-9]+\.trycloudflare\.com).*/\1/p' | tail -1 ;;
  url)
    url="${2:?Supply the current HTTPS tunnel URL}"
    [[ "$url" =~ ^https://[a-z0-9-]+\.trycloudflare\.com$ ]] || { echo 'Expected a temporary Cloudflare HTTPS URL.' >&2; exit 1; }
    python3 - "$url" <<'PY'
from pathlib import Path
import sys
p = Path('.hosting/app.env')
p.write_text('\n'.join('APP_URL='+sys.argv[1] if line.startswith('APP_URL=') else line for line in p.read_text().splitlines())+'\n')
PY
    "${compose[@]}" up -d --no-deps --force-recreate web queue scheduler
    ;;
  backup)
    umask 077
    destination=".hosting/backups/$(date -u +%Y%m%dT%H%M%SZ)"
    mkdir -p "$destination"
    "${compose[@]}" exec -T database pg_dump -U sibol -d sibol_host -Fc > "$destination/database.dump"
    "${compose[@]}" exec -T web tar -czf - -C /var/www/html storage/app > "$destination/files.tar.gz"
    cp .hosting/app.env .hosting/database.env .hosting/compose.env "$destination/"
    echo "Backup saved in $destination. Keep a secure copy off this Mac."
    ;;
  *) echo 'Usage: bash scripts/host.sh {start|stop|status|link|url HTTPS_URL|backup}' >&2; exit 1 ;;
esac

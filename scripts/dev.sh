#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
if [[ -x .tools/node/bin/node ]]; then export PATH="$PWD/.tools/node/bin:$PATH"; fi
node -e 'process.exit(Number(process.versions.node.split(".")[0]) === 24 ? 0 : 1)' || { echo 'Use Node 24 LTS (nvm use).' >&2; exit 1; }
exec npx --no-install concurrently --kill-others --names=web,queue,vite,scheduler \
  'php artisan serve --host=127.0.0.1 --port=8000' \
  'php artisan queue:work --sleep=1 --tries=3 --timeout=60' \
  'npm run dev -- --host=127.0.0.1' \
  'php artisan schedule:work'

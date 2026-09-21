# Development environment

## Toolchain

Use PHP 8.4 and Node 24 LTS, matching CI. Version hints are committed as `.php-version` and `.nvmrc`. Dependency lockfiles are committed. Application source remains privately licensed pending an owner decision; dependencies retain their own licenses.

PHP and Node run on the host for fast development. Docker runs only PostgreSQL 17 and Mailpit, keeping the number of processes and containers small. Linux and macOS are supported; Windows development should use WSL2. Docker Desktop is optional: any compatible Docker Engine/Compose installation works.

On the initial development machine, an isolated Node 24 runtime is available under ignored `.tools/node`; the setup/dev scripts prefer it without changing the system Node version. Fresh clones use their installed Node 24. Install prerequisite runtimes using your normal package manager. With nvm, use `nvm install` and `nvm use` from this folder. Required PHP extensions: pdo_pgsql, mbstring, intl, gd, zip, bcmath, pcntl, and the normal Laravel defaults. Check with `php -m`.

## First run

Run `bash scripts/setup.sh`, then `composer dev`. The setup script checks tool versions and refuses to migrate outside the documented local database. It does not rotate an existing APP_KEY or wipe data. Development runs the Laravel server, queue worker, scheduler, and Vite together; Ctrl+C stops those four processes.

Docker services:

| Service | Address | Purpose |
|---|---|---|
| App | http://localhost:8000 | Local PHP development server |
| Vite | http://127.0.0.1:5173 | Frontend hot reload |
| PostgreSQL | 127.0.0.1:55432 | `sibol` development and `sibol_testing` test databases |
| Mailpit UI | http://localhost:8025 | Capture and inspect local email |
| Mailpit SMTP | 127.0.0.1:1025 | Local-only delivery; does not send external mail |

All exposed services bind to loopback. PostgreSQL data persists in the `sibol_postgres_data` Docker volume. `docker compose stop` preserves it; never use `down -v` unless you deliberately want to discard local database data.

The testing database is created by the PostgreSQL initialization SQL when a new volume is first initialized. If you reuse an older Sibol volume without it, run `docker compose exec postgres createdb -U sibol sibol_testing` once. Do not recreate a volume to fix a missing test database.

## Tests and checks

- `composer validate --strict`: Composer metadata and lockfile consistency.
- `composer lint`: formatting; `composer format` applies formatting.
- `composer test`: Laravel tests on PostgreSQL; RefreshDatabase modifies only `sibol_testing`.
- `npm run build`: production Vite/Tailwind build, without external font requests.
- `npx playwright install chromium`, then `npm run test:e2e`: mobile browser smoke test using a separate PHP server on port 8010, with a real Livewire request.
- `docker compose config --quiet` and `bash -n scripts/*.sh deployment/*.sh`: configuration/syntax checks.

Build assets before running PHP/browser tests without Vite. Stop `composer dev` first if you want to test the built bundle; a running Vite server writes `public/hot`, which instructs Laravel to use that server instead.

Each developer gets local `.env` and `.env.testing` files with independently generated keys. CI uses a disposable PostgreSQL service. No real school records or credentials belong in development or tests.

## Troubleshooting

- Docker connection error: start your Docker engine, then rerun setup.
- Port conflict: check other local services before changing the documented ports in Compose and both env files together.
- Wrong database or stale configuration: run `php artisan config:clear`; the test bootstrap deliberately rejects non-test database names.
- Missing manifest: run `npm ci` followed by `npm run build`.
- Browser missing: rerun `npx playwright install chromium`. Linux CI uses `--with-deps` for OS libraries.
- Mail not arriving externally: expected; local SMTP points to Mailpit.

## Dependency maintenance

Dependabot opens weekly Composer, npm, and GitHub Actions updates. Workflows pin verified action commit SHAs. Docker images pin PostgreSQL major version and a Mailpit release; pull tested patch updates deliberately. Neither database major upgrades nor dependency updates are automatically deployed.

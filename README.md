# Sibol

Mobile-first childcare PWA for Philippine preschools and families.

[![CI](https://github.com/kdavadil/daycare-app/actions/workflows/ci.yml/badge.svg)](https://github.com/kdavadil/daycare-app/actions/workflows/ci.yml)

The application foundation is implemented. Parent accounts, school workflows, and the installable PWA shell are upcoming sprint work; the current page is a development placeholder, not a completed childcare system.

## Development

Requirements: PHP 8.4 (PDO PostgreSQL, mbstring, intl, GD, ZIP, bcmath, pcntl), Composer 2, Node 24 LTS, npm, Docker with Compose.

```sh
nvm use
bash scripts/setup.sh
composer dev
```

- App: http://localhost:8000
- Local email inbox: http://localhost:8025
- PostgreSQL: 127.0.0.1:55432
- Stop app processes: Ctrl+C
- Stop database/email without deleting data: `docker compose stop`

Setup installs locked dependencies, starts isolated local services, generates missing keys, migrates the development database, and builds assets. Rerunning it preserves existing keys and data. Never use the local example credentials on a server.

```sh
composer lint
composer test
npm run build
npx playwright install chromium
npm run test:e2e
```

Tests use the separate `sibol_testing` PostgreSQL database. They refuse to run against the development database. See [development instructions](docs/development.md) for troubleshooting and toolchain details.

## CI/CD

GitHub Actions runs formatting, dependency audits, PostgreSQL tests, a production frontend build, and a mobile Chromium/Livewire smoke test. Successful branch builds produce a deployable artifact.

Deployment is manually dispatched from `main`, reruns CI, and uses a configured GitHub environment. Server activation performs checksum verification, migrations, atomic release switching, service restart, readiness checks, and code rollback when a previous release is available. Server provisioning, domain/TLS, backups, and environment secrets must be configured before deployment can run.

See the [deployment runbook](docs/deployment.md). Never commit `.env`, SSH keys, production data, or payment proofs.

## Product references

- [Four-week sprint backlog](docs/sprint-backlog.md)
- [Technical design](docs/technical-design.md)
- [Interactive design mock-up](design/sibol-mobile.html)
- [Editable mock-up source](design/sibol-mobile.fragment.html)

The mock-up has fictional data and simulated payments. Personal conversation/context archives stay local and are excluded from Git. They are not included in release artifacts.

# CI/CD and server deployment

## Current scope

Repository: https://github.com/kdavadil/daycare-app

CI is defined in `.github/workflows/ci.yml`. CD is defined in `.github/workflows/deploy.yml`. No server hostname, operating system, domain, or SSH account has been supplied yet. The deployment files target a Linux VPS with systemd, Nginx, and PHP 8.4; confirm the actual server before using them. No production deployment has been performed.

## CI checks

Pushes and pull requests run Composer validation/install, npm ci, Compose/script validation, Pint, Composer audit, npm high/critical advisory checks, a frontend build, PostgreSQL-backed tests, a Chromium mobile smoke test, and disposable-container deployment tests for activation, checksum failure, migration failure, and unhealthy-release rollback. The deployment tests mock PHP/HTTPS/systemd and do not replace a staging-server rehearsal. Dependency advisory failures require review; do not blindly suppress them.

For branch builds, `scripts/package-release.sh` creates a tarball and checksum containing production Composer dependencies, compiled assets, application code, and a REVISION marker. It uses an allowlist to omit credentials, node_modules, tests, chat archives, mock-ups, and development databases. Artifacts expire after 14 days.

The Deploy workflow is manually dispatched for `staging` or `production`, only from `main`. It reruns CI against that commit, downloads the resulting artifact from the same workflow run, and uses the selected environment's connection settings. It never deploys automatically from a pull request. Future automatic staging deployment can be added after the target is verified.

## Repository configuration

Create GitHub environments named `staging` and `production`. Restrict production deployment to `main` and set a required human reviewer in the environment settings. Keep the CI workflow as a required status check on the default branch before allowing routine feature merges. Environment protection and repository rules must be verified in GitHub; YAML alone cannot enforce their creation.

Set these environment variables (not in source code):

| Variable | Example / requirement |
|---|---|
| DEPLOY_HOST | Verified server hostname or IPv4 address |
| DEPLOY_PORT | SSH port, default 22 |
| DEPLOY_USER | Restricted `sibol` deploy user |
| DEPLOY_PATH | `/srv/sibol/staging` or `/srv/sibol/production`, matching the environment |
| APP_URL | Canonical HTTPS URL, no trailing slash or path |

Set environment secrets through GitHub's UI or secure local tooling:

| Secret | Purpose |
|---|---|
| DEPLOY_SSH_KEY | Dedicated deployment private key for the intended server/user |
| DEPLOY_KNOWN_HOSTS | Host key entry verified against the server console or administrator |

Never paste private keys into chat. The workflow uses strict host-key checking and does not run ssh-keyscan to trust an unverified host. The release workflow has read-only repository permissions. Production credentials live on the server in the shared `.env`, never inside the artifact.

## Server preparation — adapt after the actual target is known

Prerequisites: supported Linux/systemd, Nginx, PHP 8.4 CLI/FPM with required extensions, PostgreSQL 17, curl, GNU tar/coreutils/flock, domain DNS, valid HTTPS certificates, firewall, off-server backups, and a restricted deployment account. Composer is used in CI and is not needed to install dependencies on the server.

Expected layout for each environment:

```text
/srv/sibol/production/
  incoming/
  releases/
  current -> releases/<commit>-<timestamp>
  shared/
    .env
    storage/
      app/private/
      framework/cache/data/
      framework/sessions/
      framework/views/
      logs/
```

Use separate database names, credentials, storage, and keys for staging and production. Stage only synthetic data. The `sibol` OS user owns release directories; PHP-FPM must be able to read releases and read/write shared storage. One simple layout uses `sibol:www-data` ownership, 0750 releases, 2770 shared storage directories, and 0640 `.env`. Ensure the setgid group and write bits are correct for both CLI and FPM-created files. Do not use world-writable permissions.

Prepare the shared `.env` from `deployment/.env.production.example`, fill database/SMTP details, and generate APP_KEY once. For staging, set APP_ENV=staging, a distinct APP_URL/database/key, and keep APP_DEBUG=false and secure cookies enabled. Generate a key locally with `php artisan key:generate --show` and transfer it through a secure channel; do not rotate an existing key on deployment.

Review/install the Nginx template and both systemd service templates. The template assumes `/usr/bin/php8.4` and `/run/php/php8.4-fpm.sock`; adapt after inspecting the server. Allow `sibol` to execute only the exact required systemctl commands via sudo: reload php8.4-fpm; restart and is-active for `sibol-queue@staging`, `sibol-queue@production`, `sibol-scheduler@staging`, and `sibol-scheduler@production`. Never grant unrestricted passwordless sudo. If php-fpm is shared with unrelated apps, assess the shared reload and use a dedicated pool/service where appropriate.

Enable the desired environment's queue and scheduler services after the first release is present. Set up nightly encrypted database/media backups, monitor backup success, and rehearse restoration before putting real school data on the server. Take/verify a fresh backup before a deployment that changes schema.

## Release behavior

1. CI validates and packages the selected main commit.
2. GitHub environment protection gates the deployment job.
3. SSH/scp transfers the artifact; the server verifies its checksum and revision marker.
4. A deployment lock prevents simultaneous activations. The script links shared configuration and storage into a new release.
5. It rejects missing APP_KEY, wrong environment, debug mode, insecure cookies, and a non-PostgreSQL database setting.
6. It runs forward migrations, config cache, and route cache before switching the current symlink atomically.
7. It reloads FPM, restarts the environment's queue/scheduler, and checks HTTPS `/ready` plus the expected revision response header and service health.
8. A failure after switching restores the previous code symlink and restarts services if a previous release exists. Database migrations are not reversed. The first deployment has no prior release to restore.

Only backward-compatible migrations belong in this pipeline. Apply schema expansion first and remove old schema in a later release after all old code/workers are retired. Database restore is a separate operator decision, not an automatic rollback. The deploy artifact has no seed-data execution step.

`/up` checks application boot; `/ready` checks the database connection, required foundation tables, and writable runtime directories. It intentionally returns no connection/error details. This initial readiness check does not replace database migration assertions or application-level tests as features are added.

Old releases are retained for rollback; configure deliberate cleanup after the pilot, preserving the current and previous known-good releases. Monitor disk usage, failed jobs, HTTP errors, and backup freshness. A single VPS is not a high-availability setup.

## First deployment acceptance

Verify domain/TLS, deployment environment settings/reviewer, host key, database isolation, restricted permissions, backup recovery, placeholder page, `/ready`, queue processing, scheduler service, and application logs. Run a staging deployment and a controlled failed-health rollback rehearsal before the first production deployment.

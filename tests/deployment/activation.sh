#!/usr/bin/env bash
# Integration tests for the release-switching shell, in a disposable Linux container.
# PHP, HTTPS, and systemd are mocked; real server integration still requires staging.
set -euo pipefail
[[ "${SIBOL_DISPOSABLE_TEST:-}" = 1 ]] || { echo 'Run only in the documented disposable container.'; exit 1; }
root=/srv/sibol/staging
mkdir -p "$root"/{incoming,releases,shared/storage} /tmp/sibol-mocks
printf 'APP_ENV=staging\n' > "$root/shared/.env"
export PATH="/tmp/sibol-mocks:$PATH"
cat > /tmp/sibol-mocks/php <<'MOCK'
#!/usr/bin/env bash
if [[ "${1:-}" = artisan && "${2:-}" = migrate && -f /tmp/fail_migration ]]; then exit 1; fi
exit 0
MOCK
cat > /tmp/sibol-mocks/sudo <<'MOCK'
#!/usr/bin/env bash
exit 0
MOCK
cat > /tmp/sibol-mocks/sleep <<'MOCK'
#!/usr/bin/env bash
exit 0
MOCK
cat > /tmp/sibol-mocks/curl <<'MOCK'
#!/usr/bin/env bash
[[ ! -f /tmp/fail_health ]] || exit 22
while [[ $# -gt 0 ]]; do
  if [[ "$1" = -D ]]; then
    printf 'HTTP/1.1 200 OK\r\nX-Sibol-Release: %s\r\n' "$(cat /srv/sibol/staging/current/REVISION)" > "$2"
    exit 0
  fi
  shift
done
exit 1
MOCK
chmod +x /tmp/sibol-mocks/*
script=/workspace/deployment/activate-release.sh
make_archive() {
  local revision=$1
  local stage
  stage=$(mktemp -d)
  mkdir -p "$stage/storage"
  printf '%s\n' "$revision" > "$stage/REVISION"
  tar -czf "$root/incoming/$revision.tar.gz" -C "$stage" .
  sha256sum "$root/incoming/$revision.tar.gz" | cut -d ' ' -f 1
  rm -rf "$stage"
}
one=1111111111111111111111111111111111111111
two=2222222222222222222222222222222222222222
three=3333333333333333333333333333333333333333
one_digest=$(make_archive "$one")
two_digest=$(make_archive "$two")
three_digest=$(make_archive "$three")
bash "$script" "$root" "$one" "$one_digest" https://staging.example.test staging
[[ "$(cat "$root/current/REVISION")" = "$one" ]]
[[ "$(readlink "$root/current/storage")" = "$root/shared/storage" ]]
echo 'PASS: activate verified release and preserve shared storage'
previous=$(readlink -f "$root/current")
if bash "$script" "$root" "$two" "$(printf '0%.0s' {1..64})" https://staging.example.test staging; then
  echo 'FAIL: corrupt artifact was accepted'; exit 1
fi
[[ "$(readlink -f "$root/current")" = "$previous" ]]
echo 'PASS: checksum failure leaves active release unchanged'
touch /tmp/fail_migration
if bash "$script" "$root" "$two" "$two_digest" https://staging.example.test staging; then
  echo 'FAIL: migration failure was accepted'; exit 1
fi
rm /tmp/fail_migration
[[ "$(readlink -f "$root/current")" = "$previous" ]]
echo 'PASS: migration failure leaves active release unchanged'
touch /tmp/fail_health
if bash "$script" "$root" "$three" "$three_digest" https://staging.example.test staging; then
  echo 'FAIL: unhealthy release was accepted'; exit 1
fi
[[ "$(readlink -f "$root/current")" = "$previous" ]]
echo 'PASS: unhealthy activation restores the previous code release'

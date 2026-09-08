#!/usr/bin/env bash
#
# ICTHospital — route smoke test
#
# Logs in once, then GETs every parameterless route and records the status code.
# Use it after any refactor to see how much of the app still responds.
#
# Usage: bash smoke-test.sh [base-url] [login] [password]
# The app must be running: docker compose up -d
#
# Copyright (c) ICT Innovations <https://www.ictinnovations.com>
# Part of ICTHospital <https://www.icthospital.com>
# Licensed under the GNU General Public License v3.0.

BASE="${1:-http://localhost:8082}"
USER="${2:-admin}"
PASS="${3:-secret123}"
JAR="$(mktemp)"
OUT="$(mktemp)"

cd ~/dev/icthospital-app 2>/dev/null || true

# The Docker daemon lives inside WSL, so containers stop when the VM does.
docker compose up -d >/dev/null 2>&1
for i in $(seq 1 30); do
  curl -s -o /dev/null --max-time 2 "$BASE/" && break
  sleep 2
done

echo "collecting routes..."
# route:list --json escapes slashes as \/ ; strip the backslashes out.
docker compose exec -T app php artisan route:list --json 2>/dev/null \
  | tr ',' '\n' \
  | grep -oE '"uri":"[^"]*"' \
  | sed 's/"uri":"//; s/"$//' \
  | tr -d '\\' \
  | sort -u > "$OUT"

echo "routes found: $(wc -l < "$OUT")"

echo "authenticating as $USER..."
TOKEN=$(curl -s -c "$JAR" "$BASE/" | grep -oE 'name="_token" value="[^"]+' | head -1 | sed 's/.*value="//')
LOGIN=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' -X POST "$BASE/users/login" \
        -d "_token=$TOKEN&login=$USER&password=$PASS")
echo "login POST -> $LOGIN"

DASH=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' "$BASE/dashboard")
if [ "$DASH" != "200" ]; then
  echo "ABORT: not authenticated (dashboard -> $DASH)"; exit 1
fi
echo "session confirmed (dashboard 200)"
echo

ok=0; redirect=0; clienterr=0; servererr=0; skipped=0
declare -a FAIL_5XX FAIL_4XX OK_LIST

while read -r uri; do
  [ -z "$uri" ] && continue
  case "$uri" in
    *"{"*)                                     skipped=$((skipped+1)); continue ;;  # needs a parameter
    *logout*)                                  skipped=$((skipped+1)); continue ;;  # would end the session
    _ignition*|_debugbar*|sanctum*|telescope*) skipped=$((skipped+1)); continue ;;
    oauth/*)                                   skipped=$((skipped+1)); continue ;;  # Passport, POST/redirect flows
  esac

  code=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' --max-time 25 "$BASE/$uri")
  case "$code" in
    2*)  ok=$((ok+1)); OK_LIST+=("/$uri") ;;
    3*)  redirect=$((redirect+1)) ;;
    4*)  clienterr=$((clienterr+1)); FAIL_4XX+=("$code /$uri") ;;
    *)   servererr=$((servererr+1)); FAIL_5XX+=("$code /$uri") ;;
  esac
done < "$OUT"

echo "================ SMOKE TEST ================"
printf '  2xx OK           : %s\n' "$ok"
printf '  3xx redirect     : %s\n' "$redirect"
printf '  4xx client error : %s\n' "$clienterr"
printf '  5xx SERVER ERROR : %s\n' "$servererr"
printf '  skipped          : %s\n' "$skipped"
echo "==========================================="

if [ ${#OK_LIST[@]} -gt 0 ]; then
  echo; echo "WORKING:"; printf '  %s\n' "${OK_LIST[@]}"
fi
if [ ${#FAIL_5XX[@]} -gt 0 ]; then
  echo; echo "SERVER ERRORS (need fixing):"; printf '  %s\n' "${FAIL_5XX[@]}"
fi
if [ ${#FAIL_4XX[@]} -gt 0 ]; then
  echo; echo "4xx (mostly POST-only or permission-gated):"; printf '  %s\n' "${FAIL_4XX[@]}"
fi

rm -f "$JAR" "$OUT"
[ "$servererr" -eq 0 ]

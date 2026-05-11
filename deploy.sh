#!/usr/bin/env bash
set -euo pipefail

# ── 配置 ──
SERVER="root@103.217.253.79"
REMOTE_DIR="/var/www/loops"
BRANCH="main"

# ── 颜色 ──
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

step() { echo -e "\n${GREEN}▸ $1${NC}"; }
fail() { echo -e "${RED}✗ $1${NC}"; exit 1; }

# ── 1. 本地：推送代码 ──
step "Pushing to origin/${BRANCH}..."
git push origin "${BRANCH}" || fail "git push failed"

# ── 2. 远程：部署 ──
step "Deploying on server..."
ssh -o ConnectTimeout=10 "${SERVER}" bash -s -- "${REMOTE_DIR}" "${BRANCH}" <<'REMOTE_SCRIPT'
set -euo pipefail
REMOTE_DIR="$1"
BRANCH="$2"
cd "${REMOTE_DIR}"

echo "▸ Pulling latest code..."
git fetch origin
git reset --hard "origin/${BRANCH}"

echo "▸ Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --quiet

echo "▸ Installing Node dependencies..."
npm ci --silent

echo "▸ Building frontend..."
npm run build 2>&1 | tail -3

echo "▸ Running migrations..."
php artisan migrate --force

echo "▸ Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "▸ Restarting Horizon..."
php artisan horizon:terminate 2>/dev/null || true

echo ""
echo "✓ Deploy complete! $(git log --oneline -1)"
REMOTE_SCRIPT

echo -e "\n${GREEN}✓ All done!${NC}"

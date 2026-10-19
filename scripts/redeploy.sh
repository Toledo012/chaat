#!/usr/bin/env bash
# =============================================================
#  SEMAHN Tickets — redeploy
#  Uso:  ./scripts/redeploy.sh            → despliega si hay commits nuevos
#        ./scripts/redeploy.sh --force    → reconstruye aunque no haya cambios
#        ./scripts/redeploy.sh --no-migrate
#        ./scripts/redeploy.sh --no-backup
# =============================================================
set -euo pipefail

BRANCH="${BRANCH:-main}"
FORCE=0; MIGRATE=1; BACKUP=1
for arg in "$@"; do
  case "$arg" in
    --force)      FORCE=1 ;;
    --no-migrate) MIGRATE=0 ;;
    --no-backup)  BACKUP=0 ;;
    -h|--help)    sed -n '2,9p' "$0"; exit 0 ;;
    *) echo "Opción desconocida: $arg" >&2; exit 1 ;;
  esac
done

cd "$(dirname "$0")/.."
ROOT="$(pwd)"
LOG_DIR="$ROOT/storage/deploy-logs"; mkdir -p "$LOG_DIR"
LOG="$LOG_DIR/deploy_$(date +%F_%H-%M-%S).log"
exec > >(tee -a "$LOG") 2>&1

log()  { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31mERROR: %s\033[0m\n' "$*"; exit 1; }

# --- Un solo deploy a la vez ---
exec 9>"/tmp/semahn-redeploy.lock"
flock -n 9 || fail "Ya hay otro redeploy corriendo."

# --- Validaciones ---
[ -f .env ] || fail "No existe .env (cp .env.docker.example .env)"
grep -qE '^APP_KEY=.+' .env || fail "APP_KEY vacía en .env"
grep -qE '^APP_DEBUG=false' .env || echo "AVISO: APP_DEBUG no está en false"
[ -z "$(git status --porcelain --untracked-files=no)" ] || \
  fail "Hay cambios locales en el servidor. Revisa 'git status' (no se edita código en prod)."

# --- ¿Hay cambios? ---
log "Buscando cambios en origin/$BRANCH"
git fetch --quiet origin "$BRANCH"
PREV="$(git rev-parse HEAD)"
NEXT="$(git rev-parse "origin/$BRANCH")"
if [ "$PREV" = "$NEXT" ] && [ "$FORCE" -eq 0 ]; then
  echo "Sin cambios ($(git log -1 --format='%h %s')). Usa --force para reconstruir."
  exit 0
fi
git --no-pager log --oneline "$PREV..$NEXT" || true

# --- Respaldo previo (por si la migración rompe algo) ---
if [ "$BACKUP" -eq 1 ] && docker compose ps --status running --services | grep -qx db; then
  log "Respaldo previo de la base"
  BACKUP_TAG="predeploy" "$ROOT/scripts/backup.sh"
fi

# --- Código nuevo ---
log "git pull ($PREV → $NEXT)"
git merge --ff-only --quiet "origin/$BRANCH"

# --- Build + up ---
log "Construyendo imágenes"
docker compose build --pull app web

log "Levantando contenedores"
docker compose up -d --remove-orphans

# --- Esperar a que web responda ---
log "Esperando healthcheck"
for i in $(seq 1 30); do
  STATUS="$(docker inspect -f '{{.State.Health.Status}}' "$(docker compose ps -q web)" 2>/dev/null || echo starting)"
  [ "$STATUS" = "healthy" ] && break
  [ "$i" -eq 30 ] && { docker compose logs --tail=50 app web; fail "web no quedó healthy. Rollback: git checkout $PREV && $0 --force --no-migrate"; }
  sleep 3
done
echo "web: healthy"

DOMAIN="$(grep -E '^DOMAIN=' .env | cut -d= -f2- | tr -d '"' || true)"
if [ -n "$DOMAIN" ]; then
  if curl -fsS -m 10 -o /dev/null "https://$DOMAIN/up"; then
    echo "https://$DOMAIN → OK"
  else
    echo "AVISO: https://$DOMAIN/up no respondió (¿está arriba el proxy en /var/www/proxy?)"
  fi
fi

# --- Migraciones ---
if [ "$MIGRATE" -eq 1 ]; then
  log "Migraciones"
  docker compose exec -T app php artisan migrate --force
fi

# --- Limpieza ---
log "Limpiando imágenes viejas"
docker image prune -f >/dev/null

find "$LOG_DIR" -name 'deploy_*.log' -mtime +30 -delete
log "Deploy OK → $(git log -1 --format='%h %s')"
echo "Anterior: $PREV   (rollback: git checkout $PREV && $0 --force --no-migrate)"
docker compose ps

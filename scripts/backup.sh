#!/usr/bin/env bash
# =============================================================
#  SEMAHN Tickets — respaldo de la base (MySQL en Docker)
#  Uso manual:  ./scripts/backup.sh
#  Cron (2:00 a.m.):  0 2 * * * /opt/semahn/scripts/backup.sh >> /var/log/semahn-backup.log 2>&1
#
#  - Incluye triggers y rutinas (la tabla movimientos depende de ellos)
#  - Quita los DEFINER para que el dump se pueda restaurar en cualquier MySQL
#  - Guarda FUERA del repo, retención configurable
# =============================================================
set -euo pipefail

cd "$(dirname "$0")/.."
BACKUP_DIR="${BACKUP_DIR:-/var/backups/semahn}"
RETENCION_DIAS="${RETENCION_DIAS:-14}"
TAG="${BACKUP_TAG:-daily}"
STAMP="$(date +%F_%H-%M)"
FILE="$BACKUP_DIR/semahntickets_${TAG}_${STAMP}.sql.gz"

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
umask 077

docker compose exec -T db sh -c \
  'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqldump -uroot \
     --single-transaction --routines --triggers --events \
     --no-tablespaces --set-gtid-purged=OFF "$MYSQL_DATABASE"' \
  | sed -E 's/DEFINER=`[^`]+`@`[^`]+`//g' \
  | gzip -9 > "$FILE.tmp"

# Validar que el dump está completo antes de darlo por bueno
gzip -t "$FILE.tmp"
zcat "$FILE.tmp" | tail -n 1 | grep -q "Dump completed" || { rm -f "$FILE.tmp"; echo "ERROR: dump incompleto" >&2; exit 1; }
mv "$FILE.tmp" "$FILE"

# Copia del .env (APP_KEY: sin ella no se recupera nada cifrado)
cp .env "$BACKUP_DIR/env_latest"

# Retención (los predeploy también caducan)
find "$BACKUP_DIR" -name 'semahntickets_*.sql.gz' -mtime +"$RETENCION_DIAS" -delete

echo "[$(date '+%F %T')] OK $(du -h "$FILE" | cut -f1) → $FILE"

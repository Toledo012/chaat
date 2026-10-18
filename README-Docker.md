# SEMAHN Tickets — Docker (producción)

Stack: Laravel 12 · PHP 8.4-FPM · Nginx 1.27 · MySQL 8.0 · Let's Encrypt · assets Vite compilados en la imagen.
Producción: https://semahnchiapas.com.mx

```
Internet :80/:443 → web (Nginx + TLS) → app (PHP-FPM :9000) → db (MySQL, solo red interna)
                     certbot (renueva el certificado cada 12 h)
```

## Primer despliegue

```bash
git clone https://github.com/Toledo012/chaat.git semahn && cd semahn
cp .env.docker.example .env
nano .env                         # passwords, APP_URL, SMTP (solo letras/números/_ en passwords)

# APP_KEY: si migras desde AWS, REUSA la APP_KEY del .env viejo.
# Si es instalación nueva, genera una:
docker compose build
docker compose run --rm --no-deps --entrypoint php app artisan key:generate --show
#   → pega el resultado en APP_KEY= del .env

# Dump de la base: se importa SOLO en el primer arranque (volumen vacío).
# Quita los DEFINER (el usuario SEMAHN@localhost no existe en el contenedor → error 1449 en triggers)
sed -E 's/DEFINER=`[^`]+`@`[^`]+`//g' /ruta/semahntickets.sql > docker/mysql/init/01_semahntickets.sql

docker compose up -d
docker compose ps                 # db "healthy", web "healthy", app "running"
docker compose logs -f
```

Si el dump trae `CREATE DATABASE` / `USE ...` con otro nombre, quítalas o deja que coincida con `DB_DATABASE`.
Si la base es nueva (sin dump): `docker compose exec app php artisan migrate --force`.

## HTTPS (una sola vez)

Sin certificado, Nginx arranca solo en HTTP. Con el registro A del dominio apuntando al VPS:

```bash
./scripts/init-ssl.sh --staging   # prueba (opcional)
./scripts/init-ssl.sh             # certificado real → Nginx cambia solo a HTTPS
```
Incluye `www.` automáticamente si ese registro también apunta al VPS. La renovación es automática.
Con `SESSION_SECURE_COOKIE=true` el login solo funciona por HTTPS.

## Actualizar el sistema (nuevo commit)

```bash
./scripts/redeploy.sh              # respaldo previo → pull → build → up → healthcheck → migrate
./scripts/redeploy.sh --force      # reconstruir aunque no haya commits nuevos
./scripts/redeploy.sh --no-migrate # sin migraciones
```
Logs de cada deploy en `storage/deploy-logs/`. Al final imprime el commit anterior para rollback.

## Respaldo automático

```bash
sudo crontab -e
0 2 * * * /opt/semahn/scripts/backup.sh >> /var/log/semahn-backup.log 2>&1
```
Guarda en `/var/backups/semahn` (fuera del repo), 14 días, con triggers y sin DEFINER.

## Comandos útiles

```bash
docker compose exec app php artisan tinker
docker compose exec app sh
docker compose restart app
docker compose logs -f app
docker compose down               # detiene (conserva la BD)
docker compose down -v            # ⚠️ BORRA la BD y storage
```

## Respaldos de la base

```bash
docker compose exec -T db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction "$MYSQL_DATABASE"' \
  | gzip > backup_$(date +%F).sql.gz
```

Restaurar sobre la BD existente:
```bash
gunzip -c backup.sql.gz | docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
```

Conectarte con DBeaver/Workbench: túnel SSH al VPS y luego
`docker compose exec db mysql -uroot -p` — el puerto 3306 NO está publicado a propósito.

## Troubleshooting

- **El contenedor app se reinicia en bucle**: `docker compose logs app`. Casi siempre es `APP_KEY` vacía o un error en `.env`.
- **Cambié passwords de BD y ya no conecta**: MySQL guarda las credenciales del primer arranque en el volumen. Respalda, `docker compose down -v`, vuelve a levantar e importa.
- **El dump no se importó**: solo ocurre con el volumen vacío. Importa a mano (ver Restaurar).
- **Cambié el .env y no se refleja**: `docker compose up -d --force-recreate app`.
- **Docker ignora UFW**: cualquier `ports:` queda público aunque ufw lo bloquee. Por eso la BD no publica puertos.

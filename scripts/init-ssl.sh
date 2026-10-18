#!/usr/bin/env bash
# =============================================================
#  SEMAHN Tickets — emitir el certificado HTTPS (Let's Encrypt)
#  Solo se corre UNA vez (o si cambias de dominio). La renovación
#  la hace el contenedor "certbot" solo, cada 12 h.
#
#  Uso:  ./scripts/init-ssl.sh             → certificado real
#        ./scripts/init-ssl.sh --staging   → prueba (no cuenta en el límite de LE)
# =============================================================
set -euo pipefail
cd "$(dirname "$0")/.."

STAGING=""
[ "${1:-}" = "--staging" ] && STAGING="--staging"

envget() { grep -E "^$1=" .env | tail -n1 | cut -d= -f2- | tr -d '"'; }
DOMAIN="$(envget DOMAIN)"
EMAIL="$(envget LETSENCRYPT_EMAIL)"
[ -n "$DOMAIN" ] || { echo "ERROR: falta DOMAIN en .env"; exit 1; }
[ -n "$EMAIL" ] && [[ "$EMAIL" != *changeme* ]] || { echo "ERROR: pon un correo real en LETSENCRYPT_EMAIL"; exit 1; }

# --- El DNS debe apuntar a ESTE servidor, si no Let's Encrypt falla ---
MY_IP="$(curl -4 -fsS https://api.ipify.org || true)"
DNS_IP="$(getent ahostsv4 "$DOMAIN" | awk 'NR==1{print $1}')"
echo "IP del servidor: ${MY_IP:-?}   |   $DOMAIN → ${DNS_IP:-sin registro}"
if [ -z "$DNS_IP" ] || { [ -n "$MY_IP" ] && [ "$DNS_IP" != "$MY_IP" ]; }; then
  echo "ERROR: el registro A de $DOMAIN no apunta a este servidor (puede tardar en propagar)."
  exit 1
fi

DOMAINS=(-d "$DOMAIN")
if [ "$(getent ahostsv4 "www.$DOMAIN" | awk 'NR==1{print $1}')" = "$DNS_IP" ]; then
  DOMAINS+=(-d "www.$DOMAIN"); echo "Incluyendo www.$DOMAIN"
else
  echo "AVISO: www.$DOMAIN no apunta aquí; se emite solo para $DOMAIN"
fi

# --- Nginx arriba en modo HTTP para servir el reto ---
docker compose up -d web certbot

docker compose run --rm --entrypoint certbot certbot certonly \
  --webroot -w /var/www/certbot "${DOMAINS[@]}" \
  --email "$EMAIL" --agree-tos --no-eff-email --non-interactive \
  --keep-until-expiring $STAGING

# --- Reinicia Nginx: detecta el certificado y cambia a HTTPS ---
docker compose restart web
sleep 5
docker compose logs --tail=5 web | grep ssl-mode || true

if curl -fsS -o /dev/null "https://$DOMAIN/up"; then
  echo "✅ HTTPS activo: https://$DOMAIN"
else
  echo "⚠️  El certificado se emitió pero https://$DOMAIN/up no respondió. Revisa: docker compose logs web"
fi

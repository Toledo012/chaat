#!/bin/sh
# Corre dentro del contenedor web en cada arranque (docker-entrypoint.d)
set -e
CERT="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"
if [ -n "$DOMAIN" ] && [ -f "$CERT" ]; then
  echo "[ssl-mode] Certificado encontrado → HTTPS para ${DOMAIN}"
  # 20-envsubst-on-templates.sh ya generó conf.d/default.conf desde https.conf.template
else
  echo "[ssl-mode] Sin certificado → modo HTTP-only"
  cp /etc/nginx/http-only.conf /etc/nginx/conf.d/default.conf
fi
# Recarga cada 12 h para tomar certificados renovados por certbot
( while sleep 43200; do nginx -s reload 2>/dev/null || true; done ) &

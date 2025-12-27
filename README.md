# FREESURF / CuWiP

Squelette Laravel 12 + Inertia (Vue 3) + PWA + Horizon, avec routes `portal` (portail client) et `backoffice`.

## DÃ©marrage rapide
- PHP 8.2+, Composer, Node 20+
- Installer et lancer:
  - `cd backend`
  - `composer install`
  - `cp .env.example .env && php artisan key:generate`
  - `npm ci && npm run dev`
  - `php artisan serve`

## PWA
- Config via `vite-plugin-pwa` (autoUpdate). Build CI dÃ©sactive le PWA avec `SKIP_PWA=1`.

## Files, Queues, Horizon
- `.env`: `QUEUE_CONNECTION=redis`, `REDIS_CLIENT=predis`
- DÃ©marrer Horizon: `php artisan horizon`

## Scheduler
- Commande `invoices:generate-monthly` planifiÃ©e le 1er Ã  01:00 (voir `bootstrap/app.php`).

## CI/CD (GitHub Actions)
- Workflow: `.github/workflows/ci.yml`
  - Build & migrations SQLite en CI
  - DÃ©ploiement SSH (si secrets prÃ©sents)
- Secrets Ã  dÃ©finir dans le repo GitHub:
  - `SSH_HOST` (ex: 102.219.46.68)
  - `SSH_USER` (ex: root)
  - `SSH_PASSWORD` (mot de passe root ou mieux: clÃ© privÃ©e via `SSH_PRIVATE_KEY` et action correspondante)
  - `DEPLOY_PATH` (ex: /var/www/freesurf)
  - `ENV_PROD` (contenu complet du `.env` de prod)

## Git
Initialisation de base:
```
git init
git add .
git commit -m "chore: bootstrap FREESURF (Laravel + Inertia + PWA)"
git branch -M main
git remote add origin git@github.com:Shelton237/FREESURF.git
git push -u origin main
```



## Docker (production)
- Fichiers clés:
  - docker-compose.yml: app (php-fpm), nginx, horizon, scheduler, redis, db
  - docker-compose.override.yml: Traefik (HTTPS auto) + labels
  - ackend/Dockerfile: build multi-étapes
  - docker/nginx.conf: vhost Nginx pour Laravel
  - docker/entrypoint.sh: init Laravel (migrate, caches)
- Variables:
  - ackend/.env (app) — injectez via le secret ENV_PROD dans la CI
  - .env  à la racine (optionnel): DOMAIN, ACME_EMAIL pour Traefik
- Démarrer:
  - docker compose up -d --build`n  - DNS: créer un enregistrement A DOMAIN → IP serveur; Traefik émettra le certificat Let’s Encrypt automatiquement.


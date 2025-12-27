# FREESURF / CuWiP

Squelette Laravel 12 + Inertia (Vue 3) + PWA + Horizon, avec routes `portal` (portail client) et `backoffice`.

## Démarrage rapide
- PHP 8.2+, Composer, Node 20+
- Installer et lancer:
  - `cd backend`
  - `composer install`
  - `cp .env.example .env && php artisan key:generate`
  - `npm ci && npm run dev`
  - `php artisan serve`

## PWA
- Config via `vite-plugin-pwa` (autoUpdate). Build CI désactive le PWA avec `SKIP_PWA=1`.

## Files, Queues, Horizon
- `.env`: `QUEUE_CONNECTION=redis`, `REDIS_CLIENT=predis`
- Démarrer Horizon: `php artisan horizon`

## Scheduler
- Commande `invoices:generate-monthly` planifiée le 1er à 01:00 (voir `bootstrap/app.php`).

## CI/CD (GitHub Actions)
- Workflow: `.github/workflows/ci.yml`
  - Build & migrations SQLite en CI
  - Déploiement SSH (si secrets présents)
- Secrets à définir dans le repo GitHub:
  - `SSH_HOST` (ex: 102.219.46.68)
  - `SSH_USER` (ex: root)
  - `SSH_PASSWORD` (mot de passe root ou mieux: clé privée via `SSH_PRIVATE_KEY` et action correspondante)
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


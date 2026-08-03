# Docker drafts for cloud-pos-inventory-v5

This folder contains draft Docker artifacts to containerize the Laravel application.

Files added (drafts):
- `Dockerfile` — app image (PHP-FPM)
- `Dockerfile.assets` — Node (Vite) build image
- `docker-compose.yml` — skeleton compose file for local testing
- `docker/nginx/default.conf` — nginx site config
- `.dockerignore` — files excluded from build

Notes:
- These are draft files intended for review. Adjust credentials, volumes, and secrets before running.
- For production, create a `docker-compose.prod.yml` and use a registry for images.

Example quick-start (dev):
```
docker compose up --build
```

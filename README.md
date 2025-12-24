# 🌱 plantoteque

A plants database

running at https://plantes.alveoles.fr

## Run dev server using docker

```bash
docker compose build
docker compose up -d
docker compose exec php composer install
docker compose exec php yarn install
docker compose exec php yarn build
```

```
dc exec php yarn encore dev
```
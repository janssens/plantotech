# plantotech
A plants 🌱 database to make the planet eden again


running at https://plantes.universite-alveoles.fr


## Run dev server using docker

```bash
docker compose build
docker compose up -d
docker compose exec fpm composer install
docker compose exec fpm yarn install
docker compose exec fpm yarn build
```

http://127.0.0.1:10302/

```bash
docker compose exec fpm php bin/console
```
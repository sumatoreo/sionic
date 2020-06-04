# Install

```shell
docker-compose up -d
docker-compose exec app bash

app> composer install
app> php artisan migrate
```

# Run Script

```shell
docker-compose exec app bash

app> php artisan parser:run
```

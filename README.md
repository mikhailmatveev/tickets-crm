## Tickets CRM

Тестовая CRM для обработки тикетов

## Развёртывание
1. `git clone git@github.com:mikhailmatveev/tickets-crm.git`
2. `cp .env.example .env`
3. `cp .env.testing.example .env.testing`
4. `Настроить .env`
5. `Настроить .env.testing`
6. `./deploy.sh`

## Параметры развёртывания

Показать справку `./deploy.sh -h`

```
$ ./deploy.sh -h
Использование: ./deploy.sh [--dry-run] [--force-rebuild]
  --dry-run        Показать команды без выполнения
  --force-rebuild  Принудительно пересобрать образы без кэша
```

Деплой на холостую `./deploy.sh --dry-run` 

Показывает только этапы выполнения команд без выполнения самих команд

```
$ ./deploy.sh --dry-run
Режим развёртывания: local
Используемый compose-файл: docker-compose.yml
Включён режим dry-run: команды будут только показаны.
Сборка и запуск сервисов: mariadb redis php queue nginx mailpit
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml up -d --build mariadb redis php queue nginx mailpit
Ожидание готовности MariaDB...
Проверка готовности MariaDB пропущена в dry-run.
Установка PHP-зависимостей...
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php composer install --no-interaction --prefer-dist --optimize-autoloader
Запуск тестов...
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php php artisan test --env=testing
Генерация документации Swagger...
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php php artisan l5-swagger:generate
Запуск Laravel-команд (очистка кэша, миграции, storage:link)...
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php php artisan config:clear
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php php artisan migrate --force
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php php artisan cache:clear
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml exec -T php php artisan storage:link
Проверка необходимости запуска сидеров...
Проверка сидеров пропущена в dry-run.
Сборка frontend-ассетов для local/dev и production...
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml run --rm node sh -lc npm ci && npm run build
Запуск контейнера с Vite dev-server...
+ docker compose --env-file /home/mikhail/tickets-crm/.env -f /home/mikhail/tickets-crm/docker-compose.yml up -d node
Развёртывание завершено.
```

Перед запуском деплоя убедиться, что у файла `deploy.sh` есть права на выполнение. Если нет, то надо выполнить `chmod +x deploy.sh`

При успешном выполнении деплоя будет такой вывод:
```
[+] Running 1/1
✔ Container Tickets-CRM_node  Started  0.2s
Развёртывание завершено.
```

## Админ

В приложении должен существовать главный админ, в случае, если не будет никаких пользователей

В `.env` это следующие параметры (на боевом меняем на свои)

```
ADMIN_NAME="John Smith"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD=password
```

## Telescope

Чтобы включить Telescope в `.env` надо добавить параметр

Настройки в `.env`
```
TELESCOPE_ENABLED=true
```

После чего сервис будет доступен по адресу `/telescope`

Проверка прав для доступа к Telescope осуществляется только в `production` окружении,
то есть, если `APP_ENV=production` в `.env`. В остальных случаях Telescope будет доступен пользователям с любой ролью

## Mailpit

Доступен только для `local/dev` окружения

Настройки в `.env`
```
MAILPIT_SMTP_PORT=1025
MAILPIT_UI_PORT=8025
```

Требуется для проверки отправки сообщение на почту при создании нового пользователя или при изменении пароля пользователю

## Документация

Генерируется автоматически при деплое. Доступна по адресу `/api/documentation`

## Production

**На текущем этапе пока требует тестирования**

Для её работы требуется файл окружения `.env.production` со своими настройками для боевой сборки - HTTPS, Let's Encrypt, а так же зарегистрированные `DOMAIN` и `EMAIL`

Использует `docker-compose.production.yml` и `docker/nginx/production.conf` файлы

## Переменные окружения
| Переменная                 | Описание                                      | Значение по умолчанию | Примечание                                   |
|----------------------------|-----------------------------------------------|-----------------------|----------------------------------------------|
| `APP_BACKEND_PORT`         | HTTP порт бекенд-части (Laravel)              | 8080                  |                                              |
| `APP_BACKEND_PORT_SECURE`  | HTTPS порт для бекенд-части (Laravel)         | 443                   |                                              |
| `APP_FRONTEND_PORT`        | HTTP порт для фронтенд-части                  | 5173                  | только для окружения `local/dev`             |
| `APP_PORT`                 | Порт приложения                               | 8080                  | для `production` = `APP_BACKEND_PORT_SECURE` |
| `APP_PATH`                 | Рабочий каталог приложения                    | `/var/www`            |                                              |
| `HOST_UID`                 | ID пользователя `root`                        | 1000                  |                                              |
| `HOST_GID`                 | ID группы `root`                              | 1000                  |                                              |
| `DB_ROOT_PASSWORD`         | Пароль пользователя `root` к БД               |                       |                                              |
| `SANCTUM_STATEFUL_DOMAINS` | Параметры CORS для корректной работы Sanctum  |                       |                                              |
| `MAILPIT_SMTP_PORT`        | Порт для отправки/получения сообщений Mailpit | 1025                  | только для окружения `local/dev`             |
| `MAILPIT_UI_PORT`          | Порт веб-интерфейса Mailpit                   | 8025                  | только для окружения `local/dev`             |
| `VITE_PORT`                | Порт Vite dev-сервера                         | 5173                  | только для окружения `local/dev`             |
| `TELESCOPE_ENABLED`        | Включение/отключение Telescope                | `true`                |                                              |
| `DOMAIN`                   | Домен для выдачи SSL-сертификата              | `example.com`         | только для окружения `production`            |
| `EMAIL`                    | Адрес почты для выдачи SSL-сертификата        | `admin@example.com`   | только для окружения `production`            |
| `MARIADB_VERSION`          | Версия MariaDB образа Docker                  | 11                    |                                              |
| `NGINX_VERSION`            | Версия Nginx образа Docker                    | `alpine`              |                                              |
| `NODE_VERSION`             | Версия Node образа Docker                     | 24                    |                                              |
| `PHP_VERSION`              | Версия PHP образа Docker                      | 8.4                   |                                              |
| `REDIS_VERSION`            | Версия Redis образа Docker                    | 7                     |                                              |

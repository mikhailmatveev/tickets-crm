## Tickets CRM

Тестовая CRM для обработки тикетов

## Развёртывание
1. `git clone git@github.com:mikhailmatveev/tickets-crm.git`
2. `cp .env.example .env`
3. `cp .env.testing.example .env.testing`
4. `Настроить .env`
5. `Настроить .env.testing`
6. `./deploy.sh`

Перед запуском деплоя убедиться, что у файла `deploy.sh` есть права на выполнение. Если нет, то надо выполнить `chmod +x deploy.sh`

## Порядок выполнения основных команд при деплое
1. Проверка параметров запуска скрипта, путей и переменных окружения
2. Подготовка compose-файла и списка сервисов в зависимости от переменной `APP_ENV`
3. Сборка и запуск сервисов общих для `local` и `production` сборки
4. Проверка готовности БД
5. Установка PHP-зависимостей
6. Генерация `APP_KEY` для `.env` и `.env.testing`
7. Прогон тестов
8. Генерация документации Swagger
9. Выполнение миграций, очистка кэша, `storage:link`
10. Наполнение базы (seeding)
11. Действия специфичные для `production` (кеширование, получение сертификата)
12. Подготовка и запуск Vite dev-сервера (только для `local/dev` окружения)
13. Завершение

При успешном выполнении деплоя выведется следующее:
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

В проекте используется генератор документации Swagger
Сгенерировать документацию `php artisan l5-swagger:generate`
Доступно по адресу `/api/documentation`

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

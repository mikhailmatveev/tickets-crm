## Tickets CRM

Тестовая CRM для обработки тикетов

## Развёртывание
1. `git clone git@github.com:mikhailmatveev/tickets-crm.git`
2. `cp .env.example .env`
3. `Настроить .env`
4. `composer install`
5. `php artisan key:generate`
6. `php artisan key:generate --env=testing`
7. `php artisan telescope:install`
8. `npm install`

## Админ

В приложении должен существовать главный админ, в случае, если не будет никаких пользователей

В `.env` это следующие параметры (на боевом меняем на свои)

```
ADMIN_NAME="John Smith"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD=password
```

## Публикация сторонних библиотек Laravel

Может потребоваться дополнительно публикация через `artisan vendor:publish` сторонних библиотек,
чтобы создались необходимые для работы конфиги

```
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```
## Выполнение миграций

`php artisan migrate`

## Наполнение базы данными

`php artisan db:seed`

## Прогон тестов

`php artisan test`

Должен появиться такой вывод

```
PASS  Tests\Unit\SeederTest
✓ that at least one admin exists                                                                                                                                             0.11s  
✓ that all customers has tickets                                                                                                                                             0.03s  
✓ that all done tickets has at least one reply                                                                                                                               0.02s  
✓ that all ticket replies linked to manager users                                                                                                                            0.02s  
✓ that ticket replies has at least one reply                                                                                                                                 0.02s

PASS  Tests\Feature\ExampleTest
✓ the application returns a successful response                                                                                                                              0.08s

Tests:    6 passed (6 assertions)
Duration: 0.37s
```

## Запуск dev сервера Laravel

`php artisan serve`

По умолчанию должен запустить веб-сервер по адресу `http://127.0.0.1:8000`

## Сборка фронтенд части

`npm run dev`
или
`npm run build`

Первый вариант отслеживает изменение файлов и автоматически обновляет сборку. Второй - больше подходит для `prod`

## Telescope

Чтобы включить Telescope в `.env` надо добавить параметр
```
TELESCOPE_ENABLED=true
```

После чего сервис будет доступен по адресу `/telescope`

## Документация

В проекте используется генератор документации Swagger
Сгенерировать документацию `php artisan l5-swagger:generate`
Доступно по адресу `/api/documentation`

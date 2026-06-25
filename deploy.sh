#!/usr/bin/env bash
set -euo pipefail

# Этап 1: параметры запуска скрипта
# Поддерживаются флаги:
# --dry-run        показать команды без выполнения
# --force-rebuild  пересобрать docker-образы без кэша
DRY_RUN=0
FORCE_REBUILD=0
REQUESTED_APP_ENV="${APP_ENV:-}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run)
      DRY_RUN=1
      shift
      ;;
    --force-rebuild)
      FORCE_REBUILD=1
      shift
      ;;
    -h|--help)
      echo "Использование: ./deploy.sh [--dry-run] [--force-rebuild]"
      echo "  --dry-run        Показать команды без выполнения"
      echo "  --force-rebuild  Принудительно пересобрать образы без кэша"
      exit 0
      ;;
    *)
      echo "Неизвестный аргумент: $1"
      echo "Использование: ./deploy.sh [--dry-run] [--force-rebuild]"
      exit 1
      ;;
  esac
done

# Этап 2: подготовка путей и загрузка переменных окружения
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="${ROOT_DIR}/.env"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "Файл .env не найден: ${ENV_FILE}"
  exit 1
fi

set -a
source "${ENV_FILE}"
set +a

APP_ENV="${REQUESTED_APP_ENV:-${APP_ENV:-local}}"

if [[ "${APP_ENV}" == "production" ]]; then
  ENV_FILE="${ROOT_DIR}/.env.production"

  if [[ ! -f "${ENV_FILE}" ]]; then
    echo "Файл .env.production не найден: ${ENV_FILE}"
    exit 1
  fi

  set -a
  source "${ENV_FILE}"
  set +a

  APP_ENV="${REQUESTED_APP_ENV:-${APP_ENV:-production}}"
  echo "Загружен .env.production"

  if [[ -z "${DOMAIN:-}" || "${DOMAIN}" == "example.com" || "${DOMAIN}" == "localhost" || "${DOMAIN}" == "127.0.0.1" ]]; then
    echo "Некорректный DOMAIN для production: ${DOMAIN:-<empty>}"
    exit 1
  fi

  if [[ -z "${EMAIL:-}" || "${EMAIL}" == "admin@example.com" ]]; then
    echo "Некорректный EMAIL для production: ${EMAIL:-<empty>}"
    exit 1
  fi
fi

# Этап 3: выбор compose-файла и списка сервисов по APP_ENV
APP_ENV="${APP_ENV:-local}"
COMPOSE_FILE="docker-compose.yml"
SERVICES=(mariadb redis php queue nginx)

if [[ "${APP_ENV}" == "production" ]]; then
  COMPOSE_FILE="docker-compose.production.yml"
  SERVICES+=(certbot)
else
  # mailpit только для локальной разработки
  SERVICES+=(mailpit)
fi

# Этап 4: определение команды Docker Compose (v2 или legacy v1)
if docker compose version >/dev/null 2>&1; then
  COMPOSE=(docker compose --env-file "${ENV_FILE}" -f "${ROOT_DIR}/${COMPOSE_FILE}")
elif command -v docker-compose >/dev/null 2>&1; then
  COMPOSE=(docker-compose --env-file "${ENV_FILE}" -f "${ROOT_DIR}/${COMPOSE_FILE}")
else
  echo "Docker Compose не установлен."
  exit 1
fi

# Этап 5: обёртка для dry-run
# В обычном режиме команда выполняется,
# в dry-run только печатается.
run_cmd() {
  echo "+ $*"
  if [[ "${DRY_RUN}" -eq 0 ]]; then
    "$@"
  fi
}

# Этап 6: лог режима запуска
echo "Режим развёртывания: ${APP_ENV}"
echo "Используемый compose-файл: ${COMPOSE_FILE}"
if [[ "${DRY_RUN}" -eq 1 ]]; then
  echo "Включён режим dry-run: команды будут только показаны."
fi
if [[ "${FORCE_REBUILD}" -eq 1 ]]; then
  echo "Включён режим force-rebuild: образы будут пересобраны без кэша."
fi

# Этап 7: сборка и запуск базовых сервисов
echo "Сборка и запуск сервисов: ${SERVICES[*]}"
if [[ "${FORCE_REBUILD}" -eq 1 ]]; then
  run_cmd "${COMPOSE[@]}" build --no-cache
  run_cmd "${COMPOSE[@]}" up -d --force-recreate "${SERVICES[@]}"
else
  run_cmd "${COMPOSE[@]}" up -d --build "${SERVICES[@]}"
fi

# Этап 8: ожидание готовности БД (MariaDB)
echo "Ожидание готовности MariaDB..."
if [[ "${DRY_RUN}" -eq 1 ]]; then
  echo "Проверка готовности MariaDB пропущена в dry-run."
else
  for i in {1..60}; do
    if "${COMPOSE[@]}" exec -T mariadb mariadb-admin ping -h localhost -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent >/dev/null 2>&1; then
      echo "MariaDB готова."
      break
    fi

    if [[ "${i}" -eq 60 ]]; then
      echo "MariaDB не стала готова за отведённое время."
      exit 1
    fi

    sleep 2
  done
fi

# Этап 9: установка PHP-зависимостей (с --no-dev для production)
echo "Установка PHP-зависимостей..."
if [[ "${APP_ENV}" == "production" ]]; then
  run_cmd "${COMPOSE[@]}" exec -T php composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
else
  run_cmd "${COMPOSE[@]}" exec -T php composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Этап 10: генерация APP_KEY при отсутствии ключа
if [[ -z "${APP_KEY:-}" ]]; then
  echo "Генерация APP_KEY..."
  run_cmd "${COMPOSE[@]}" exec -T php php artisan key:generate --force
  run_cmd "${COMPOSE[@]}" exec -T php php artisan key:generate --force --env="testing"
fi

# Этап 10.1: запуск тестов
# При падении тестов set -e прервёт деплой
echo "Запуск тестов..."
run_cmd "${COMPOSE[@]}" exec -T php php artisan test --env=testing || true

# Этап 10.2: генерация документации Swagger
echo "Генерация документации Swagger..."
run_cmd "${COMPOSE[@]}" exec -T php php artisan l5-swagger:generate

# Этап 11: базовые Laravel-команды запуска
echo "Запуск Laravel-команд (очистка кэша, миграции, storage:link)..."
run_cmd "${COMPOSE[@]}" exec -T php php artisan config:clear
run_cmd "${COMPOSE[@]}" exec -T php php artisan migrate --force
run_cmd "${COMPOSE[@]}" exec -T php php artisan cache:clear
run_cmd "${COMPOSE[@]}" exec -T php php artisan storage:link || true

# Этап 12: запуск сидеров (только при первом деплое)
echo "Проверка необходимости запуска сидеров..."
if [[ "${DRY_RUN}" -eq 1 ]]; then
  echo "Проверка сидеров пропущена в dry-run."
else
  ROLES_COUNT=$("${COMPOSE[@]}" exec -T mariadb mariadb \
    -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" \
    -se "SELECT COUNT(*) FROM roles;" 2>/dev/null || echo "0")

  if [[ "${ROLES_COUNT}" == "0" ]]; then
    echo "Первый деплой — запуск сидеров..."
    run_cmd "${COMPOSE[@]}" exec -T php php artisan db:seed --force
  else
    echo "Сидеры пропущены — данные уже существуют."
  fi
fi

# Этап 13: действия, специфичные для production
if [[ "${APP_ENV}" == "production" ]]; then
  # 13.1: оптимизация Laravel для production
  echo "Оптимизация Laravel для production..."
  run_cmd "${COMPOSE[@]}" exec -T php php artisan config:cache
  run_cmd "${COMPOSE[@]}" exec -T php php artisan route:cache
  run_cmd "${COMPOSE[@]}" exec -T php php artisan view:cache

  # 13.2: первичное получение SSL-сертификата (если ещё не выпущен)
  if [[ -n "${DOMAIN:-}" && "${DOMAIN}" != "example.com" && -n "${EMAIL:-}" ]]; then
    if [[ "${DRY_RUN}" -eq 1 ]]; then
      echo "Проверка/выпуск SSL-сертификата пропущены в dry-run."
    elif ! "${COMPOSE[@]}" exec -T certbot test -f "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" >/dev/null 2>&1; then
      echo "SSL-сертификат не найден. Выполняется первичное получение Let's Encrypt сертификата..."
      run_cmd "${COMPOSE[@]}" run --rm --entrypoint "" certbot certbot certonly \
        --webroot -w /var/www/certbot \
        -d "${DOMAIN}" -d "www.${DOMAIN}" \
        --email "${EMAIL}" --agree-tos --no-eff-email
      run_cmd "${COMPOSE[@]}" exec -T nginx nginx -s reload
    fi
  else
    echo "Первичное получение SSL пропущено. Укажи DOMAIN и EMAIL в .env для bootstrap-сертификата в production."
  fi
fi

# 14: сборка frontend-ассетов для local/dev и production
echo "Сборка frontend-ассетов для local/dev и production..."
run_cmd "${COMPOSE[@]}" run --rm node sh -lc "npm ci && npm run build"

# Этап 15: действия только для local/dev (запуск Vite dev-server)
if [[ "${APP_ENV}" != "production" ]]; then
  echo "Запуск контейнера с Vite dev-server..."
  run_cmd "${COMPOSE[@]}" up -d node
fi

# Этап 16: завершение
echo "Развёртывание завершено."

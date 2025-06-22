## Шаги для запуска проекта


1. Клонирование репозитория
Перейдите в директорию, где вы хотите разместить проект, и выполните команду для клонирования репозитория:
```bash
git clone https://github.com/D3vour3r69/susu-hosting.git
```
Зайдите в директорию проекта
```bash
cd hosting-app
```

2. Скопируйте `.env.example` в `.env`:
```bash
cp .env.example .env
```
   
3. Настройте переменные окружения в `.env`:
   - Убедитесь, что `DB_HOST` совпадают с именами сервисов в `compose.yaml`.
   - Проверьте порты и учетные данные для БД.
   - DB_CONNECTION=pgsql - Ваша база данных
   - DB_HOST=db - Название контейнера в докер компоуз
   - DB_PORT=5432 - Порт указан по дефолту, если указан другой вписывается сюда
   - DB_DATABASE=laravel - Название созданной базы данных
   - DB_USERNAME=laravel - Пользователь базы данных
   - DB_PASSWORD=secret - Пароль от базы данных

## Установка

1. Запустите контейнеры:
   ```bash
   docker-compose up -d --build
   ```
# Важные замечания
- Если возникают ошибки прав доступа, выполните:
  ```bash
  docker-compose exec app chmod -R 777 storage bootstrap/cache
  ```
2. Установите PHP-зависимости **внутри контейнера**:
   ```bash
   docker-compose exec app composer install
   ```

3. Сгенерируйте ключ приложения:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

4. Выполните миграции:
   ```bash
   docker-compose exec app php artisan migrate:fresh --seed
   ```
5. Запустите парсеры для сотрудников и начальников, это может занять какое-то время
   ```bash
   docker-compose exec app php artisan app:parse-structure   
   ```
   ```bash
   docker-compose exec app php artisan app:parse-employees
   ```
## Проверка работоспособности

  Откройте веб-браузер и перейдите по адресу http://localhost:8000, чтобы проверить работоспособность приложения.

   Зайдите под пользователем Login: khudiakovata@susu.ru Password: 123 для проверки со стороны руководителя кафедры\отдела.

   Зайдите под администратором Login: admin@example.com Password: 123 для проверкисо стороны администратора веб-приложения.
   
Для тестирования приложения через Laravel Dusk выполните следующее
```bash
docker compose exec app php artisan serve --env=dusk.local --port=8000
```
```bash
docker compose exec app php artisan dusk
```
Остановка контейнеров
Чтобы остановить контейнеры, используйте команду:
```bash
  docker-compose stop
```
 Удаление контейнеров
Чтобы удалить контейнеры, используйте команду:
```bash
  docker-compose down
```
Примечания
Убедитесь, что Docker и Docker Compose установлены на вашей системе.
Docker-desktop при работе на WINDOWS



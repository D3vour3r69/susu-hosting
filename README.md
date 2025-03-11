Шаги для запуска проекта


1. Клонирование репозитория
Перейдите в директорию, где вы хотите разместить проект, и выполните команду для клонирования репозитория:

git clone https://github.com/D3vour3r69/susu-hosting/tree/feature

2. Запуск контейнеров
Запустите все контейнеры с помощью Docker Compose:

docker-compose up -d

3. Применение миграций
После запуска контейнеров примените миграции для базы данных:

docker-compose exec app php artisan migrate

4.Проверка работоспособности
Откройте веб-браузер и перейдите по адресу http://localhost, чтобы проверить работоспособность приложения.

5. Остановка контейнеров
Чтобы остановить контейнеры, используйте команду:

docker-compose stop

6. Удаление контейнеров
Чтобы удалить контейнеры, используйте команду:

docker-compose down

Примечания
Убедитесь, что Docker и Docker Compose установлены на вашей системе.
Docker-desktop при работе на WINDOWS

# Laboratory Internet

## How to run

```bash
git clone git@github.com:Shadovitsme/Laboratory_Internet.git
cd Laboratory_Internet
cp .env.example .env
sudo composer update
php artisan key:generate
npm install
sudo docker compose up -d
vendor/bin/sail artisan migrate:fresh
npm run build
```

Открыть в браузере `http://localhost:80/`

## API

Описание API можно увидеть в файле [swagger.yml](./swagger.yml)

Для удобного просмотра, вставьте текст в редактор, например, [этот](https://editor-next.swagger.io/)

## Задание

_Можно на чистом php:_

1. Создать открытый Git репозиторий.
2. Реализовать методы REST API для работы с пользователями:
3. Создание пользователя;
4. Обновление информации пользователя;
5. Удаление ользователя;
6. Авторизация пользователя;
7. Получить информацию о пользователе.
8. В файле README.md описать реализованные методы.
# pro_context

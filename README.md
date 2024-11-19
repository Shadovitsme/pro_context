# pro_context

## How to run

```bash
git clone https://github.com/Shadovitsme/pro_context
cd pro_context
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



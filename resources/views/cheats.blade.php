<!DOCTYPE html>
<html lang="ru">

<head>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body data-bs-theme="dark">

    <x-nav />

    <div class="container pt-5">

        <p class="lead">
            Это тестовая страница, содержащая все возможные поля и действия для взаимодействия с API.
        </p>
        <x-register />
        <x-authenticate />
        <x-profile />
        <x-edit-profile />
        <x-result-feed />

    </div>
</body>

</html>
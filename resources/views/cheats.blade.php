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

    <div class="container align-items-start justify-content-between d-flex flex-row flex-wrap pt-5">
        <x-register />
        <x-authenticate />
        <x-edit-profile />
        <x-profile />
        <x-delete />
        <x-result-feed />
    </div>
</body>

</html>
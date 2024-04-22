<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
<?php
        // шапка с окном входа или именем авторизованного пользователя
        ($userAuthorized) ? headerUserLogged($loginUser) : headerLoginForm($AUTH_MSG);
?>
    </header>
    <main class="mainContent">
        <h2><?= $header ?></h2>
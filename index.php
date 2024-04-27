<?php
    
    session_start();
    require 'php/functions.php';

    checkLogout();
    
    $phpProfile = 'profile.php';
    $dataServices = 'data/services.json';
    $dataUsers = 'data/users.json';
    $services = json_decode(file_get_contents($dataServices), true);
    $users = json_decode(file_get_contents($dataUsers), true);

    $AUTH_ERR = '<span class="error">Ошибка в имени или пароле.</span>';
    $userAuthorized = false;
    $loginAttempt = false;
    $loginPass = '';
    $discountForLogin = ''; // здесь будет Unix-дата последней активации скидки за логин
    $discountBirth = false; // здесь будет двоичный флаг ДР/неДР

    // в куках есть юзер? юзер есть в базе? задаем авторизацию, если да
    $userAuthorized = checkUserfromCookie();
    $loginUser = ($userAuthorized) ? htmlspecialchars($_COOKIE['user']) : '';
    
    // проверка, есть ли на входе форма с логином
    // если есть, то сверяем пароль с базой
    if(isset($_POST['login'])) {
        $loginAttempt = true;
        $loginUser = htmlspecialchars($_POST['login']);
        $loginPass = htmlspecialchars($_POST['password']);
        $userAuthorized = checkPassword($loginUser, $loginPass);        
    };
    if ($userAuthorized) {
        // при успешной авторизации устанавливаем куки на 1 сутки
        setcookie("user",$loginUser,time()+60*60*24,"/");
        // обновляем время последнего входа
        $currentTime = time();
        updateLoginTime($currentTime,$loginUser);
        $discountForLogin = checkDiscountForLogin();

        // счетчик событий (акций) в шапке:
        // скидка на день рождения +1
        // скидка на логин +1
        $events_counter = 0;
        $userBirthValue = getUserBirth($loginUser); // берем ДР из базы, если есть
        $discountBirth = checkUserBirthday($userBirthValue); // проверяем событие "сегодня ДР юзера!"
        if ($discountBirth) $events_counter++;
        if ($discountForLogin) $events_counter++;
    };


    $AUTH_MSG = (!$userAuthorized && $loginAttempt ) ? $AUTH_ERR : '';

    // выводим шапку страницы
    $title = 'Студия красота и SPA';
    $header = 'Добро пожаловать в Студию красоты и SPA!';
    require_once 'php/head.php';
?>

<!-- Акции -->


<?
$contentTitle = 'Наши услуги:';
require_once 'content.php';
require_once 'php/footer.php'
?>

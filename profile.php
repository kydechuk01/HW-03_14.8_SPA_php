<?php
    session_start();
    require 'php/functions.php';
    $phpProfile = 'profile.php';

    $AUTH_ERR = '<span class="error">Ошибка в имени или пароле.</span>';

    // читаем базу пользователей
    $dataUsers = 'data/users.json';
    $users = json_decode(file_get_contents($dataUsers), true);

    // проверяем авторизацию
    if (!empty($_COOKIE['user'])) {
        // если в куках есть юзер, проверяем его профиль
        $userAuthorized = checkUserfromCookie();
        $loginAttempt = true;
        $loginUser = ($userAuthorized) ? htmlspecialchars($_COOKIE['user']) : '';
    } else { // иначе редирект на главную и завершаем скрипт
        header('location: /');
        die;
    };

    // проверка, пришла ли на вход страницы кнопка "Сохранить дату"
    // тогда записываем эту ДР в базу
    if(isset($_POST['dateBirth'])) {
        $userBirthdate = htmlspecialchars($_POST['dateBirth']);
        updateUserBirth($userBirthdate,$loginUser);
        
    };
    
    $events_counter = 0;

    // Задаем дату из POST-запроса обновления ДР или читаем из базы
    $userBirthValue = (isset($_POST['dateBirth']))
        ? $_POST['dateBirth'] 
        : getUserBirth($loginUser);
    
    // скидка на день рождения
    $discountBirth = checkUserBirthday($userBirthValue);
    $discountForLogin = checkDiscountForLogin();
    
    if ($discountBirth) $events_counter++;
    if ($discountForLogin) $events_counter++;


    $AUTH_MSG = (!$userAuthorized && $loginAttempt ) ? $AUTH_ERR : '';
    $title = 'Личный профиль '.$loginUser.': студия красота и SPA';
    $header = 'Личный профиль '.$loginUser;
    require_once 'php/head.php';
  
    $dataUsers = 'data/users.json';
    
       echo ' Ваш день рождения:
    <form action="#" method="POST" style="display:inline-block">
    <input name="dateBirth" type="date" value="'.$userBirthValue.'">
    <input name="submit" type="submit" value="Сохранить"><br>
    </form>
    ';

    // DONE: персональная скидка -50% в день рождения
    // DONE: article: скидка -5% для авторизованных
    
    printDiscountsTitle();
    printDiscountBirth();
    printDiscountForLogin();

       
    require_once 'php/footer.php'
    ?>

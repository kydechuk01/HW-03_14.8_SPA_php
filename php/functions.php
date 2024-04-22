<?php

// проверка, пришла ли на вход страницы кнопка "Выход из профиля"
function checkLogout()
{
    if (isset($_POST['logout'])) {
        unset($_COOKIE['user']);
        // unset($_COOKIE['discountForLogin']);
        setcookie('user', '', -1, '/'); // удаляем куки 'user'
        // setcookie('discountForLogin', '', -1, '/'); // удаляем куки 'discountForLogin'
        session_destroy();
        header('location: /'); // перезагружаем корень
        die;
    };
};

// есть ли такое сочетание юзер+пассворд?
function checkPassword($login, $password)
{
    global $users;
    
    foreach ($users as $user) {
        if (
            $user['username'] == $login &&
            $user['hash'] == sha1($password)
        ) return true;
    }
    return false;
};

// проверка, есть ли в куки user И есть ли юзер с таким именем в базе
function checkUserfromCookie()
{
    global $users;
    if (isset($_COOKIE['user'])) {
        $userCookie = htmlspecialchars($_COOKIE['user']);
        foreach ($users as $user) {
            if (in_array($userCookie, $user)) return true;
            };
        return false;
    };
};

// показать форму ввода логина в шапке
function headerLoginForm($MSG)
{
    echo '
        <div class="formLogin">
        <form action="/" method="POST" style="display:inline-block">
        Вход в личный кабинет:
        <input class="login" name="login" type="text" placeholder="Логин">
        <input class="login" name="password" type="password" placeholder="Пароль">
        <input class="btnLogin" name="submit" type="submit" value="Войти"><br>
        ' . $MSG . '</form></div>';
}

// показать пользователя, события и кнопку выхода в шапке
function headerUserLogged($user)
{
    global $phpProfile, $events_counter;
    // проверим счетчик событий (акций) и установим его в 0, если он пуст
    $events_counter = (isset($events_counter)) ? $events_counter : 0;
    // сформируем хтмл-код счетчика в заголовке
    $events_msg = ($events_counter > 0) ? '<sup class="events_cnt">(' . $events_counter . ")</sup>" : '';
    echo '
<div class="formLogin">Профиль: 
<a href="' . $phpProfile . '">' . $user . '</a>' . $events_msg . '         
<form action="/" method="POST" style="display:inline-block">
<input class="btnLogin" name="logout" type="submit" value="Выход">
</form></div>
';
}

// Функция чтения даты рождения пользователя из базы
function getUserBirth($username) {
    global $dataUsers, $users;
    // TODO сохранить дату в массив users
    $users = json_decode(file_get_contents($dataUsers), true);
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            return $user['birthdate'];
        };       
    };
    return '';
};
// Функция сохранения даты рождения пользователя в базе
function updateUserBirth($date, $username) {
    global $dataUsers, $users, $loginUser;
    // TODO сохранить дату в массив users
    $users = json_decode(file_get_contents($dataUsers), true);
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['birthdate'] = $date;
        };       
    };
    file_put_contents($dataUsers,json_encode($users)); // запишем изменение
};

// Функция сохранения времени последнего логина пользователя
function updateLoginTime($date, $username) {
    global $dataUsers, $users; 
    // TODO сохранить дату в массив users
    $users = json_decode(file_get_contents($dataUsers), true);
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['lastlogin'] = $date;
        };       
    };
    file_put_contents($dataUsers,json_encode($users)); // запишем изменение
};

function checkUserBirthday ($userBirth) {
    if (!isset($userBirth) || $userBirth=='') return false;
    $currentDate = date("m-d");
    list($userYYYY, $userMM, $userDD) = explode("-", $userBirth);
    $userDay =  "$userMM-$userDD";
    if ($userDay == $currentDate) return true;   
}

// проверяем наличие куки discountForLogin
// если есть, то возвращаем ее значение
// если ее не было, или уже нет, то записываем новую куки с текущим временем
// и возвращаем его

function checkDiscountForLogin () {
    if (isset($_COOKIE['discountForLogin'])) {
        return htmlspecialchars($_COOKIE['discountForLogin']);
    } else {
        $currentTime = time();
        setcookie('discountForLogin',$currentTime,time()+60*60*24,"/");
        return $currentTime;
    };
}

// вывод заголовка акций (в профиле всегда, на главной странице - только если есть акции)
function printDiscountsTitle(){
    echo "<h3>Акции и ваши персональные скидки. Все скидки суммируются!</h3>\n";
}

// вывод информации о скидке в День рождения
function printDiscountBirth(){
    global $discountBirth,$userBirthValue;
    if (isset($discountBirth)&&$discountBirth) {
        echo '<div class="discount">🌸 Поздравляем с днем рождения! Дарим вам скидку 50% на любой набор услуг нашего салона! [Активно]</div>';
    } elseif (isset($userBirthValue) && (!$userBirthValue=='')) {
        $daysLeft = '';
        $userBirthUnix = strtotime($userBirthValue);
        $userBirthArr = explode("-",$userBirthValue);
        $thisYear = date("Y");
        $thisYearBirthday = "$thisYear-$userBirthArr[1]-$userBirthArr[2]";
        $thisYearBirthdayUnix = strtotime($thisYearBirthday);
        $currentTime = time();
        // Вычисляем количество секунд до дня рождения
        $secondsToThisYB = $thisYearBirthdayUnix - $currentTime;        
        // если <0 то ДР в этом году уже прошел, надо взять следующий год
        if ($secondsToThisYB<0) {
            $thisYear = date("Y")+1;
            $thisYearBirthday = "$thisYear-$userBirthArr[1]-$userBirthArr[2]";
            $thisYearBirthdayUnix = strtotime($thisYearBirthday);
            $secondsToThisYB = $thisYearBirthdayUnix - $currentTime; 
        }
        $daysLeft = ceil($secondsToThisYB/(60*60*24));
        // print_r($daysLeft);

        echo '<div class="noBirthdiscount">Осталось '.$daysLeft.' дней до вашего дня рождения. Заходите к нам в свой день рождения и вы получите персональную скидку 50%!</div>';
    } else {
        echo '<div class="noBirthdiscount">Сообщите нам день рождения, чтобы получить подарок - СКИДКУ 50%!</div>';
    };
}

// вывод информации о скидке для активных пользователей на 24 часа
function printDiscountForLogin(){
    global $discountForLogin;
    if ($discountForLogin > 0) {
        $secondsLeft = $discountForLogin + 3600*24 - time();
        $hoursLeft = floor($secondsLeft / 3600);
        $minutesLeft = floor(($secondsLeft - $hoursLeft*60*60)/60);
        $secondsTail = $secondsLeft - $hoursLeft*3600 - $minutesLeft*60;
        echo '<div class="discount">🌸 Дополнительная скидка 10% для всех активных клиентов нашего сайта! Спешите воспользоваться, до конца акции осталось '.$hoursLeft. ' часов, ' . $minutesLeft. ' минут и ' .  $secondsTail . ' секунд! [Активно]</div>';
    };

}


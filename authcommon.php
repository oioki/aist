<?php

$role = 'user';

// В базе данных хранятся данные аутентификации

function grantAccess($trole)
{
    global $role;

    // admin
    if ( $role == 'admin' ) return;

    // supervisor
    if ( $role != $trole )
    {
        Header("Location: .");
        exit;
    }
}

function md5crypt($password)
{
    // create a salt that ensures crypt creates an md5 hash
    $base64_alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    .'abcdefghijklmnopqrstuvwxyz0123456789+/';
    $salt='$1$';
    for($i=0; $i<9; $i++) {
        $salt.=$base64_alphabet[rand(0,63)];
    }
    // return the crypt md5 password
    return crypt($password,$salt.'$');
}

function check_credentials($login,$plainpassword)
{
    global $dbh,$role,$tableUsers;

    $sql = "SELECT `id`,`pass`,`role` FROM `$tableUsers` WHERE `login` = ? LIMIT 1";
    $sth = $dbh->prepare($sql);
    $sth->execute(array($login)) or die( $sql );
    $row = $sth->fetch();

    $role = $row['role'];
    $hash = $row['pass'];

    $salt = substr($hash,0,12);
    $password = crypt($plainpassword,$salt);

    return ( $password == $hash ) ? $row['id'] : 0;
}

function create_user($email)
{
    global $dbh, $tableUsers;

    $login  = preg_replace('/@.*/', '', $email);
    $passwd = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );

    $proto = "http" . (isset($_SERVER['HTTPS']) ? 's' : '');
    $host = $_SERVER['HTTP_HOST'];
    $path = $_SERVER['REQUEST_URI'];
    $path = substr($url, 0, strrpos($path, '/'));
    $url = "$proto://$host$path";

    $subj = "Доступ к телефонной статистике";
    $msg  = "Добрый день, коллега!

Высылаю вам доступ к системе телефонной статистики.

$url

Логин:  $login
Пароль: $passwd";
    $headers = 'From: www@cc-stat.fxclub.org'. "\r\n" .
               'Content-type: text/plain; charset=utf-8';
    mail($email, $subj, $msg, $headers);

    $sql = "INSERT INTO `$tableUsers`(`login`,`pass`,`role`) VALUES(?, ?, ?)";
    $args = array($login, md5crypt($passwd), 'user');
    $msg = "Пользователь $login добавлен.";

    $sth = $dbh->prepare($sql);
    if ( !$sth->execute($args) )
        return 'error';

    return 'ok';
}

?>

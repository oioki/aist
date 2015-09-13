<?php

require 'init.php';
require 'auth.php';
grantAccess('admin');

$msg = "";

if ( !empty($_POST['email']) )
{
    $res = create_user($_POST['email']);
    $login  = preg_replace('/@.*/', '', $email);

    if ( $res == 'ok' )
        $msg = "Пользователь $login добавлен.";
    else
        $msg = "Ошибка добавления пользователя $login.";
}

if ( !empty($_POST['login']) )
{
    $plainpass = $_POST['pass'];
    $cryptpass = md5crypt($plainpass);
    $role = $_POST['role'];

    if ( !empty($_POST['oldlogin']) )
    {
        if ( $_POST['action'] == 'delete' )
        {
            $sql = "DELETE FROM `$tableUsers` WHERE login = ?";
            $args = array($_POST['login']);
            $msg = "Пользователь ${_POST['login']} удалён.";
        }
        elseif ( !empty($plainpass) )
        {
            $sql = "UPDATE `$tableUsers` SET login=?, pass=?, role=? WHERE login = ?";
            $args = array($_POST['login'], $cryptpass, $role, $_POST['oldlogin']);
            $msg = "Пароль пользователя ${_POST['login']} изменён.";
        }
        else
        {
            $sql = "UPDATE `$tableUsers` SET login=?, role=? WHERE login = ?";
            $args = array($_POST['login'], $role, $_POST['oldlogin']);
            $msg = "Пользователь ${_POST['login']} изменён.";
        }
    }
}
else
{
    if ( !empty($_POST['oldlogin']) )
        $msg = "Новое имя пользователя должно быть непустым.";
}


if (isset($args))
{
    $sth = $dbh->prepare($sql);
    $sth->execute($args) or die( $sql );
}

$users = array();
$sql = "SELECT `id`,`$tableUsers`.`login`,`pass`,`role`,MAX(`date`) AS `lastlogin` FROM `$tableUsers` LEFT JOIN `logins` ON `$tableUsers`.`login` = `logins`.`login` GROUP BY `login` ORDER BY `role` DESC, lastlogin DESC";
$sth = $dbh->prepare($sql);
$sth->execute() or die( $sql );
while ( $row = $sth->fetch() )
{
    $users[] = $row;
}

$today = date('d.m.Y');
$smarty->assign('from', $today);
$smarty->assign('to',   $today);

$smarty->assign('msg', $msg);
$smarty->assign('users', $users);
$smarty->display('users.tpl');

?>

<?php

require("authcommon.php");

if ( $auth_required )
{
    if ( !isset($_COOKIE['auth']) )
    {
        Header('Location: login.html');
        exit;
    }

    list($login, $pass) = split(":", $_COOKIE['auth']);
    $userid = check_credentials($login, $pass);

    if ( !$userid )
    {
        Header('Location: login.html');
        exit;
    }
}
else
{
    $login = "admin";
    $role = "admin";
}

$smarty->assign('role',$role);
$smarty->assign('login',$login);

?>

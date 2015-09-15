<?php

require 'init.php';
require 'authcommon.php';

function do_login()
{
	global $dbh;
	$login = $_POST['login'];
	$pass = $_POST['pass'];
	$ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

	$sql = "INSERT INTO `logins` VALUES(?,NOW(),?,?)";
	$sth = $dbh->prepare($sql);

	if ( check_credentials($login,$pass) > 0 )
	{
		$sth->execute(array($login,1,$ip)) or die( $sql );
		SetCookie("auth","$login:$pass",time()+3600*24);
		Header("Location: .");
		exit;
	}
	else
	{
		$sth->execute(array($login,0,$ip)) or die( $sql );
		Header("Location: login.html");
		exit;
	}
}

function do_logout()
{
	SetCookie("auth","",time()-3600);
	Header("Location: login.html");
	exit;
}

if ( $_POST['action'] == 'login'  ) do_login();
if ( $_POST['action'] == 'logout' ) do_logout();

Header("Location: login.html");
exit;

?>

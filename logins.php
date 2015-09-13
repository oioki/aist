<?php

require 'init.php';
require 'auth.php';
grantAccess('admin');
require 'aux.php';


if ( !isset($_GET['from']) and !isset($_GET['to']) )
    $_GET['from'] = $_GET['to'] = date('d.m.Y');

$from = datMySQL($_GET['from']);
$to   = datMySQL($_GET['to']);


$logins = array();
$sql = "SELECT date,login,ip,result FROM logins WHERE ".where_time_logins()." ORDER BY date DESC LIMIT 50";
$sth = $dbh->prepare($sql);
$sth->execute() or die( $sql );
while ( $row = $sth->fetch() )
{
    $logins[] = $row;
}

$smarty->assign('from', $_GET['from']);
$smarty->assign('to',   $_GET['to']);
$smarty->assign('mode', "logins");

$smarty->assign('logins', $logins);
$smarty->display('logins.tpl');

?>

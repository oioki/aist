<?php

require 'init.php';
require 'auth.php';
grantAccess('admin');

$msg = "";

if ( !empty($_POST['action']) )
{
    $sql = "CREATE TABLE `$tableExt` (name VARCHAR(80), fullname VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci)";
    $args = array();
    $msg = "Таблица `$tableExt` создана.";
}

if ( !empty($_POST['ext']) )
{
    if ( !empty($_POST['oldext']) )
    {
        $sql = "UPDATE `$tableExt` SET `name` = ?, `fullname` = ? WHERE `name` = ?";
        $args = array($_POST['ext'], $_POST['name'], $_POST['oldext']);
        $msg = "Оператор ${_POST['ext']} (${_POST['name']}) изменён.";
    }
    else
    {
        $sql = "INSERT INTO `$tableExt` VALUES(?, ?)";
        $args = array($_POST['ext'], $_POST['name']);
        $msg = "Оператор ${_POST['ext']} (${_POST['name']}) добавлен.";
    }
}
else
{
    if ( !empty($_POST['oldext']) )
    {
        $sql = "DELETE FROM `$tableExt` WHERE `name` = ?";
        $args = array($_POST['oldext']);
        $msg = "Оператор ${_POST['oldext']} удалён.";
    }
}


if (isset($args))
{
    $sth = $dbh->prepare($sql);
    $sth->execute($args) or die( $sql );
}

$extensions = array();
$sql = "SELECT `name` AS `ext`, `fullname` FROM `$tableExt` ORDER BY `name` ASC";
$sth = $dbh->prepare($sql);
if ( !$sth->execute() )
{
    $ei = $sth->errorInfo();
    if ( $ei[1] == 1146 )
    {
        $smarty->display('extensions.create.tpl');
    }
    exit;
}

while ( $row = $sth->fetch() )
{
    $extensions[] = $row;
}

$today = date('d.m.Y');
$smarty->assign('from', $today);
$smarty->assign('to',   $today);

$smarty->assign('msg', $msg);
$smarty->assign('tableExt', $tableExt);
$smarty->assign('extensions', $extensions);
$smarty->display('extensions.tpl');

?>

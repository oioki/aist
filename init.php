<?php

require('libs/Smarty.class.php');
$smarty = new Smarty;

$smarty->configLoad('app.conf','global');
$pbx_module = $smarty->getConfigVariable('pbx_module');
$auth_required = $smarty->getConfigVariable('auth_required');

$smarty->configLoad('app.conf','database');
$dbhost = $smarty->getConfigVariable('host');
$dbuser = $smarty->getConfigVariable('user');
$dbpass = $smarty->getConfigVariable('pass');
$dbname = $smarty->getConfigVariable('name');

$smarty->configLoad('app.conf','schema');
$table = $smarty->getConfigVariable('cdr');
$tableExt = $smarty->getConfigVariable('extensions');
$tableUsers = $smarty->getConfigVariable('users');

$smarty->configLoad('app.conf','local');
$tzoffset    = $smarty->getConfigVariable('tzoffset');
$ext_length  = $smarty->getConfigVariable('ext_length');
$accountcode = $smarty->getConfigVariable('accountcode');

$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbuser", "$dbpass");
$dbh->query('SET NAMES utf8');

$results = $dbh->query("SHOW TABLES LIKE '$tableExt'");
if ( !$results )
{
    die(print_r($dbh->errorInfo(), TRUE));
}
$extensions_table_exists = ( $results->rowCount() > 0 );

?>

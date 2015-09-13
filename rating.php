<?php

require 'init.php';
require 'auth.php';
require $pbx_module;



######################################################################################
# Parsing GET variables
######################################################################################

if ( !isset($_GET['from']) and !isset($_GET['to']) )
    $_GET['from'] = $_GET['to'] = date('d.m.Y');

$from = datMySQL($_GET['from']);
$to   = datMySQL($_GET['to']);



$rows = getStats($from, $to);

function compare($a, $b)
{
    if ( $a['ext'] == 'AVERAGE' ) return 1;
    return $a['countlong4inbound'] + $a['countlong4outbound'] < $b['countlong4inbound'] + $b['countlong4outbound'];
}

usort($rows, 'compare');



$smarty->assign('from', $_GET['from']);
$smarty->assign('to',   $_GET['to']);
$smarty->assign('mode', 'rating');

$smarty->assign('stat', $rows);
$smarty->display('rating.tpl');

?>

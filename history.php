<?php

require 'init.php';
require 'auth.php';
require $pbx_module;



######################################################################################
# Parsing GET variables
######################################################################################

// current day
$date = $_GET['from'];
$from = $to = datMySQL($date);
preg_match_all('/.*\.(.*)\.(.{4})/', $date, $m);
$currentMonth = $m[1][0];
$currentYear  = $m[2][0];

if ( date('m') == $currentMonth )
    $last_day = date('d');
else
    $last_day = date('t', strtotime($from));

// summary stats will be stored there
$dailyStats = array();

// each day of selected month
for ($day=1; $day<=$last_day; $day++)
{
    $from = $to = "$currentYear-$currentMonth-$day";

    $eachDailyStats = getStats($from, $to);
    unset($eachDailyStats['AVERAGE']);

    $cntMonthly = 0;
    foreach ($eachDailyStats as $ext => $row)
    {
        if ( !isset($dailyStats[$ext]) )
        {
            $dailyStats[$ext]['ext'] = $ext;
            $dailyStats[$ext]['name'] = $eachDailyStats[$ext]['name'];
            $dailyStats[$ext]['cntMonthly'] = 0;
            $dailyStats[$ext]['cntOutbound'] = 0;
            $dailyStats[$ext]['cntSuccessOutbound'] = 0;
            $dailyStats[$ext]['sumTotalOutbound'] = 0;
            $dailyStats[$ext]['cntLongOutbound'] = 0;
            $dailyStats[$ext]['cntLongInbound'] = 0;
            $dailyStats[$ext]['cntLong4Outbound'] = 0;
            $dailyStats[$ext]['cntLong4Inbound'] = 0;
            $dailyStats[$ext]['cntLong6Outbound'] = 0;
            $dailyStats[$ext]['cntLong6Inbound'] = 0;
            $dailyStats[$ext]['cntInbound'] = 0;
            $dailyStats[$ext]['sumInbound'] = 0;
        }
    }

    foreach ($eachDailyStats as $ext => $row)
    {
        $dailyStats[$ext]['cntMonthly'] ++;

        // aggregate statistics
        $dailyStats[$ext]['cntOutbound'] += $row['outbound'];
        $dailyStats[$ext]['cntSuccessOutbound'] += $row['success_outbound'];
        $dailyStats[$ext]['sumTotalOutbound'] += $row['totaloutbound'];

        $dailyStats[$ext]['cntLong4Outbound'] += $row['countlong4outbound'];
        $dailyStats[$ext]['cntLong4Inbound'] += $row['countlong4inbound'];

        $dailyStats[$ext]['cntLong6Outbound'] += $row['countlong6outbound'];
        $dailyStats[$ext]['cntLong6Inbound'] += $row['countlong6inbound'];

        $dailyStats[$ext]['cntInbound'] += $row['longinbound'];
        $dailyStats[$ext]['sumInbound'] += $row['totallonginbound'];
    }

    $cntMonthly ++;
}

// fields names for averaging
$fields = array('cntOutbound', 'cntInbound', 'cntSuccessOutbound', 'sumTotalOutbound', 'cntInbound', 'sumInbound', 'cntLongInbound', 'cntLongOutbound', 'cntLong4Inbound', 'cntLong4Outbound', 'cntLong6Inbound', 'cntLong6Outbound');

$dailyStats['AVGMONTHLY'] = array();
$dailyStats['AVGMONTHLY']['ext'] = 'AVGMONTHLY';
foreach ($fields as $field)
    $dailyStats['AVGMONTHLY'][$field] = 0;


foreach ($dailyStats as $ext => $row)
{
    if ( $ext != 'AVGMONTHLY' )
    {
        // aggregated monthly statistics
        $dailyStats[$ext]['avgLenOutbound'] = ratio($row['sumTotalOutbound'], $row['cntSuccessOutbound']);
        $dailyStats[$ext]['avgLenInbound']  = ratio($row['sumInbound'],       $row['cntInbound']);
        $dailyStats[$ext]['avgLen']         = ratio($row['sumTotalOutbound']+$row['sumInbound'] , $row['cntSuccessOutbound']+$row['cntInbound']);

        $count = $row['cntSuccessOutbound'];
        $total = $row['cntOutbound'];
        $dailyStats[$ext]['percentUnsuccessful'] = ($total==0) ? 0 : round(100 - 100 * $count / $total);

        // average counters
        foreach ($fields as $field)
            $dailyStats['AVGMONTHLY'][$field] += $row[$field];
    }
}

$dailyStats['AVGMONTHLY']['avgLenOutbound'] = ratio($dailyStats['AVGMONTHLY']['sumTotalOutbound'],    $dailyStats['AVGMONTHLY']['cntSuccessOutbound']);
$dailyStats['AVGMONTHLY']['avgLenInbound']  = ratio($dailyStats['AVGMONTHLY']['sumInbound'], $dailyStats['AVGMONTHLY']['cntInbound']);
$dailyStats['AVGMONTHLY']['avgLen']         = ratio( $dailyStats['AVGMONTHLY']['sumTotalOutbound']+$dailyStats['AVGMONTHLY']['sumInbound'] , $dailyStats['AVGMONTHLY']['cntSuccessOutbound']+$dailyStats['AVGMONTHLY']['cntInbound']);
$dailyStats['AVGMONTHLY']['percentUnsuccessful'] = round(100 - 100 * $dailyStats['AVGMONTHLY']['cntSuccessOutbound'] / $dailyStats['AVGMONTHLY']['cntOutbound']);

//averaging monthly
foreach ($fields as $field)
    $dailyStats['AVGMONTHLY'][$field] = round( $dailyStats['AVGMONTHLY'][$field] / count($dailyStats) );

function compare($a, $b)
{
    if ( $a['ext'] == 'AVGMONTHLY' ) return 1;
    return $a['cntLong4Inbound'] + $a['cntLong4Outbound'] < $b['cntLong4Inbound'] + $b['cntLong4Outbound'];
}

usort($dailyStats, 'compare');

$smarty->assign('from', $date);
$smarty->assign('to',   $date);
$smarty->assign('stat', $dailyStats);
$smarty->display('history.tpl');

?>

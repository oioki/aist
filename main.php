<?php

######################################################################################
# Parsing GET variables
######################################################################################

if ( !isset($_GET['from']) or ($_GET['from']=='') ) $_GET['from'] = date('d.m.Y');
if ( !isset($_GET['to']) or ($_GET['to']=='') ) $_GET['to'] = date('d.m.Y');

$from = datMySQL($_GET['from']);
$to   = datMySQL($_GET['to']);
$src  =          isset($_GET['src'])       ? $_GET['src'] : '';
$dst  =          isset($_GET['dst'])       ? $_GET['dst'] : '';
$srcdst    =     isset($_GET['srcdst'])    ? $_GET['srcdst'] : '';
$direction =     isset($_GET['direction']) ? $_GET['direction'] : '';
$status    =     isset($_GET['status'])    ? $_GET['status'] : '';



// calculating WHERE condition
$where = where($src,$dst,$srcdst,$direction,$status);

// index calculation - incoming
//  count  sum  from_internal(0/1)  to_internal(0/1)
$sth_index = $dbh->prepare( sql_index_calls($where) );
$sth_index->execute() or die( sql_index_calls($where) );

$cnt[0][1] = $cnt[1][0] = $cnt[1][1] = 0;
$sum[0][1] = $sum[1][0] = $sum[1][1] = 0;

while( $row = $sth_index->fetch() )
{
    $a = $row[2];
    $b = $row[3];
    $cnt[$a][$b] = $row[0];
    $sum[$a][$b] = $row[1];
}

$c_incoming = $cnt[0][1];
$s_incoming = $sum[0][1];
$c_outbound = $cnt[1][0];
$s_outbound = $sum[1][0];
$c_internal = $cnt[1][1];
$s_internal = $sum[1][1];

if ( $c_incoming == '' ) $c_incoming = 0;
if ( $s_incoming == '' ) $s_incoming = 0;
if ( $c_outbound == '' ) $c_outbound = 0;
if ( $s_outbound == '' ) $s_outbound = 0;
if ( $c_internal == '' ) $c_internal = 0;
if ( $s_internal == '' ) $s_internal = 0;


// index calculation - success outbound
$sth_index = $dbh->prepare( sql_index_outbound($where) );
$sth_index->execute() or die( sql_index_outbound($where)  );
$row = $sth_index->fetch();
$c_soutbound = $row[0];
$s_ringtime  = $row[1];


// index calculation - success inbound
$sth_index = $dbh->prepare( sql_index_inbound($where) );
$sth_index->execute() or die( sql_index_inbound($where) );
$row = $sth_index->fetch();
$c_sinbound = $row[0];



$index = round( ($c_incoming+$c_outbound)*0.5 + $c_soutbound + ($s_incoming+$s_outbound)/60, 2);



// data itself
$sth = $dbh->prepare( sql_calls($where) );
$sth->execute() or die( sql_calls($where) );
//echo sql_calls($where);

$directions = array(
    'internal' => "Внутренний",
    'incoming' => "Входящий",
    'outgoing' => "Исходящий",
    'incorrect'=> "Ошибочный"
);

$oneday = ( $from == $to );

$rows = array();
while ( $row = $sth->fetch() )
{
    // download feature
    $ts = $row['dateTimeOrigination'] - $tzoffset*3600;
    $row['playlink'] = "play.html?".date('Y/m/d/',$ts).$ts."-".$row['callingPartyNumber']."-".$row['finalCalledPartyNumber'].".mp3";
    $row['downloadlink'] = "download/".date('Y/m/d/',$ts).$ts."-".$row['callingPartyNumber']."-".$row['finalCalledPartyNumber'].".mp3";

    $row['ringtime'] = ($row['dateTimeConnect']==0) ? $row['dateTimeDisconnect'] - $row['dateTimeOrigination'] : $row['dateTimeConnect']-$row['dateTimeOrigination'];
    $row['dateTimeOrigination'] = dat($row['dateTimeOrigination'], $oneday);
    $row['dateTimeConnect'] = ($row['dateTimeConnect']==0) ? '-' : dat($row['dateTimeConnect'], $oneday);
    $row['dateTimeDisconnect'] = dat($row['dateTimeDisconnect'], $oneday);

    $row['cause'] = pbx_cause( $row['origCause_value'] , $row['destCause_value'] , $row['dateTimeConnect'] , $row['duration'] );

    $row['duration'] = tim($row['duration']);
    $row['ringtime'] = tim($row['ringtime']);

    if ( preg_match("/^\d{".$ext_length."}$/", $row['callingPartyNumber']) )
    {
        $row['direction'] = "outgoing";
        if ( preg_match("/^\d{".$ext_length."}$/", $row['finalCalledPartyNumber']) )
        {
            $row['direction'] = "internal";
        }
    }
    else
    {
        if ( preg_match("/^\d{".$ext_length."}$/", $row['finalCalledPartyNumber']) )
            $row['direction'] = "incoming";
        else
            $row['direction'] = "incorrect";
    }
    $row['img'] = ($row['direction']=='outgoing') ? "" : $row['direction'];
    $row['direction'] = $directions[$row['direction']];

    $row['class'] = "";
    $row['marker'] = "";

    /* BEGIN custom classes & markers */
    /* END custom classes & markers */

    if ( $row['cause'] != 'Разговор' ) $row['class'] .= ' neg';

    $rows[] = $row;
}


// Prepare for display the rendered page
$smarty->assign('src',    $src);
$smarty->assign('dst',    $dst);
$smarty->assign('srcdst', $srcdst);
$smarty->assign('from',   $_GET['from']);
$smarty->assign('to',     $_GET['to']);
$smarty->assign('direction', $direction);
$smarty->assign('status', $status);

$smarty->assign('c_internal', $c_internal);
$smarty->assign('s_internal', $s_internal);
$smarty->assign('s_hinternal', timHours($s_internal));

$smarty->assign('c_incoming', $c_incoming);
$smarty->assign('s_incoming', $s_incoming);
$smarty->assign('s_hincoming', timHours($s_incoming));

$smarty->assign('c_outbound', $c_outbound);
$smarty->assign('s_outbound', $s_outbound);
$smarty->assign('s_houtbound', timHours($s_outbound));

$smarty->assign('c_soutbound', $c_soutbound);
$smarty->assign('c_sincoming', $c_sinbound);

$smarty->assign('index', $index);

$smarty->assign('s_ringtime', timHours($s_ringtime));

$smarty->assign('extensions_table_exists', $extensions_table_exists?1:0);

$smarty->assign('mode', "calls");
$smarty->assign('calls', $rows);
$smarty->display('calls.tpl');

?>

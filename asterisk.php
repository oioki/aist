<?php

require 'aux.php';

function where_srcdst($src, $dst, $srcdst)
{
    global $dbh;

    if ( $srcdst == '' )
    {
        $where_src = ( $src == '' ) ? "1" : "`src` = ".$dbh->quote($src);
        $where_dst = ( $dst == '' ) ? "1" : "`dst` = ".$dbh->quote($dst);
        return "$where_src AND $where_dst";
    }
    else
    {
        $srcdst = $dbh->quote($srcdst);
        return "(`src` = $srcdst OR `dst` = $srcdst)";
    }
}

function where_direction($dir)
{
    global $ext_length;
    if ( $dir == '' ) return "1";

    $sqls = array(
        'internal'  => "    (LENGTH(`src`) = $ext_length) AND     (LENGTH(`dst`) = $ext_length)",
        'incoming'  => "NOT (LENGTH(`src`) = $ext_length) AND     (LENGTH(`dst`) = $ext_length)",
        'outbound'  => "    (LENGTH(`src`) = $ext_length) AND NOT (LENGTH(`dst`) = $ext_length)",
        'incorrect' => "NOT (LENGTH(`src`) = $ext_length) AND NOT (LENGTH(`dst`) = $ext_length)",
    );

    return $sqls[$dir];
}

function where_time()
{
    global $from, $to, $tzoffset;
    if ( $from != '' )
    {
        $where = ( $to != '' ) ? "`calldate` BETWEEN '$from 00:00:00'-INTERVAL $tzoffset HOUR AND '$to 23:59:59'-INTERVAL $tzoffset HOUR" : "`calldate` >= '$from 00:00:00'-INTERVAL $tzoffset HOUR";
    }
    else
    {
        $where = ( $to != '' ) ? "`calldate` <= '$to 23:59:59'-INTERVAL $tzoffset HOUR" : "1";
    }
    return $where;
}

function where($src,$dst,$srcdst,$direction)
{
    global $table, $accountcode;
    $sql = where_time()." AND ".where_srcdst($src,$dst,$srcdst)." AND ".where_direction($direction);
    if ( $accountcode != '' ) $sql .= " AND `$table`.`accountcode` = $accountcode";
    return $sql;
}

function sql_index_calls($where)
{
    global $table, $ext_length;
    return "SELECT COUNT(1),SUM(billsec),IF(LENGTH(`src`) = $ext_length,1,0) AS a,IF(LENGTH(`dst`) = $ext_length,1,0) AS b FROM `$table`
WHERE $where GROUP BY a,b";
}

function sql_index_outbound($where)
{
    global $table, $ext_length;
    return "SELECT COUNT(1),SUM(duration-billsec) FROM `$table`
WHERE `disposition` = 'ANSWERED' AND (LENGTH(`src`) = $ext_length) AND NOT (LENGTH(`dst`) = $ext_length) AND $where";
}

function sql_index_inbound($where)
{
    global $table, $ext_length;
    return "SELECT COUNT(1) FROM `$table`
WHERE `disposition` = 'ANSWERED' AND NOT (LENGTH(`src`) = $ext_length) AND (LENGTH(`dst`) = $ext_length) AND $where";
}

function sql_calls($where)
{
    global $extensions_table_exists, $table, $tableExt, $tzoffset;
    if ( $extensions_table_exists )
    {
        $srcvalue = "`ts`.`fullname`";
        $dstvalue = "`td`.`fullname`";
        $join = "LEFT JOIN `$tableExt` ts ON `$table`.`src` = `ts`.`name` LEFT JOIN `$tableExt` td ON `$table`.`dst` = `td`.`name`";
    }
    else
    {
        $srcvalue = "NULL";
        $dstvalue = "NULL";
        $join = "";
    }

    return "SELECT
$srcvalue AS srcname, $dstvalue AS dstname,
UNIX_TIMESTAMP(`calldate` + INTERVAL $tzoffset HOUR) AS dateTimeOrigination,
src AS callingPartyNumber,
dst AS originalCalledPartyNumber,
dst AS finalCalledPartyNumber,
UNIX_TIMESTAMP(calldate + INTERVAL $tzoffset HOUR) + duration - billsec AS `dateTimeConnect`,
UNIX_TIMESTAMP(calldate + INTERVAL $tzoffset HOUR) + duration AS `dateTimeDisconnect`,
billsec AS `duration`,
disposition AS `origCause_value`,
disposition AS `destCause_value`
FROM `$table` $join
WHERE $where ORDER BY `calldate` DESC LIMIT 5000";
}

function pbx_cause($oc, $dc, $dateTimeConnect, $duration)
{
    if ( $oc == 'ANSWERED'  ) return "Разговор";
    if ( $oc == 'NO ANSWER' ) return "Без ответа";
    if ( $oc == 'BUSY'      ) return "Занято";
    if ( $oc == 'FAILED'    ) return "Ошибка";
    return "Ошибка $oc";
}


function getStats($from, $to)
{
    global $dbh, $table, $tableExt, $accountcode, $ext_length, $extensions_table_exists;

    // calculating WHERE condition
    $where = "(LENGTH(`src`) = $ext_length) AND NOT (LENGTH(`dst`) = $ext_length) AND ".where_time();
    if ( $accountcode != '' ) $where .= " AND `$table`.`accountcode` = $accountcode";

    // Init total counters
    $totalOutbound = 0;
    $totalInbound  = 0;

    // Outbound statistics
    if ( $extensions_table_exists )
    {
        $namevalue = "fullname";
        $join = "LEFT JOIN `$tableExt` ON `$table`.`src` = `$tableExt`.`name`";
    }
    else
    {
        $namevalue = "NULL";
        $join = "";
    }
    $sql = "SELECT `src`, $namevalue AS `name`, COUNT(1) AS `Outbound`
FROM `$table` $join
WHERE $where
GROUP BY `src`";
    $sth = $dbh->prepare($sql);
    $sth->execute() or die($sql);

    $rows = array();
    while( $row = $sth->fetch() )
    {
        $ext = $row[0];
        $rows[$ext]['ext'] = $ext;
        $rows[$ext]['name'] = $row[1];
        $rows[$ext]['outbound'] = $row[2];

        $rows[$ext]['inbound']          = 0;
        $rows[$ext]['success_outbound'] = 0;
        $rows[$ext]['totalinbound']     = 0;
        $rows[$ext]['totaloutbound']    = 0;
    }
    $rows['AVERAGE']['ext'] = 'AVERAGE';


    // Outbound success statistics
    $sql = "SELECT `src`, COUNT(1) AS `Outbound`, ROUND(SUM(`billsec`)/60,2) AS `TotalOutbound`
FROM `$table`
WHERE $where AND `disposition` = 'ANSWERED'
GROUP BY `src`";
    $sth = $dbh->prepare($sql);
    $sth->execute() or die($sql);

    while( $row = $sth->fetch() )
    {
        $ext = $row[0];
        $rows[$ext]['success_outbound'] = $row[1];
        $rows[$ext]['percentunsuccessful'] = round(100 - 100 * $row[1] / $rows[$row[0]]['outbound']);
        $rows[$ext]['totaloutbound'] = $row[2];
    }
    
    
    
    // Outbound statistics, length >= 4 min
    $where_long = "$where AND `billsec` >= 4*60";
    $sql_long = "SELECT `src`, COUNT(1) AS `Outbound`, SUM(`billsec`) AS `TotalOutbound`
FROM `$table`
WHERE $where_long AND `disposition` = 'ANSWERED'
GROUP BY `src`";

    $sth = $dbh->prepare($sql_long);
    $sth->execute() or die($sql_long);

    while( $row = $sth->fetch() )
    {
        $ext = $row[0];
        $rows[$ext]['countlong4outbound'] = $row[1];
        $rows[$ext]['totallong4outbound'] = $row[2];
    }


    // Outbound statistics, length >= 6 min
    $where_long = "$where AND `billsec` >= 6*60";
    $sql_long = "SELECT `src`, COUNT(1) AS `Outbound`
FROM `$table`
WHERE $where_long AND `disposition` = 'ANSWERED'
GROUP BY `src`";

    $sth = $dbh->prepare($sql_long);
    $sth->execute() or die($sql_long);

    while( $row = $sth->fetch() )
        $rows[$row[0]]['countlong6outbound'] = $row[1];



    // Incoming statistics
    $where = "NOT (LENGTH(`src`) = $ext_length) AND (LENGTH(`dst`) = $ext_length) AND ".where_time();
    if ( $accountcode != '' ) $where .= " AND `$table`.`accountcode` = $accountcode";

    $sql = "SELECT `dst`, COUNT(1) AS `Inbound`, ROUND(SUM(`billsec`)/60,2) AS `TotalInbound`
FROM `$table`
WHERE $where AND `disposition` = 'ANSWERED'
GROUP BY `dst`";

    $sth = $dbh->prepare($sql);
    $sth->execute() or die($sql);

    while( $row = $sth->fetch() )
    {
        $rows[$row[0]]['inbound'] = $row[1];
        $rows[$row[0]]['totalinbound'] = $row[2];
    }

    
    // Incoming statistics, length > 15 sec
    $where_long = "$where AND `billsec` > 15";
    $sql_long = "SELECT `dst`, COUNT(1) AS `Inbound`, ROUND(SUM(`billsec`)/60,2) AS `TotalInbound`
FROM `$table`
WHERE $where_long AND `disposition` = 'ANSWERED'
GROUP BY `dst`";

    $sth = $dbh->prepare($sql_long);
    $sth->execute() or die($sql_long);

    while( $row = $sth->fetch() )
    {
        $rows[$row[0]]['longinbound'] = $row[1];
        $rows[$row[0]]['totallonginbound'] = $row[2];
    }


    // Incoming statistics, length >= 4 min
    $where_long = "$where AND `billsec` >= 4*60";
    $sql_long = "SELECT `dst`, COUNT(1) AS `Inbound`
FROM `$table`
WHERE $where_long AND `disposition` = 'ANSWERED'
GROUP BY `dst`";

    $sth = $dbh->prepare($sql_long);
    $sth->execute() or die($sql_long);

    while( $row = $sth->fetch() )
        $rows[$row[0]]['countlong4inbound'] = $row[1];


    // Incoming statistics, length >= 6 min
    $where_long = "$where AND `billsec` >= 6*60";
    $sql_long = "SELECT `dst`, COUNT(1) AS `Inbound`
FROM `$table`
WHERE $where_long AND `disposition` = 'ANSWERED'
GROUP BY `dst`";

    $sth = $dbh->prepare($sql_long);
    $sth->execute() or die($sql_long);

    while( $row = $sth->fetch() )
        $rows[$row[0]]['countlong6inbound'] = $row[1];

    // fields names for averaging
    $fields = array('outbound', 'inbound', 'success_outbound', 'totaloutbound', 'totalinbound', 'longinbound', 'totallonginbound', 'countlonginbound', 'countlongoutbound');

    
    // INDEX calculation
    foreach ($rows as $ext => $row)
    {
        
        $rows[$ext]['index'] = round(0.5 * ($row['inbound']+$row['outbound']) + $row['success_outbound'] + ($row['totaloutbound']+$row['totalinbound'])/60, 2);
        
        // averaging
        $rows[$ext]['avglenoutbound'] = ratio($row['totaloutbound'],    $row['success_outbound']);
        $rows[$ext]['avglenlongoutbound'] = round(ratio($row['totallong4outbound'], $row['countlong4outbound']));
        $rows[$ext]['avgleninbound']  = ratio($row['totallonginbound'], $row['longinbound']);
        $rows[$ext]['avglen']         = ratio($row['totaloutbound']+$row['totallonginbound'], $row['success_outbound']+$row['longinbound']);
        
        // average counters
        foreach ($fields as $field)
            $rows['AVERAGE'][$field] += $rows[$ext][$field];
    }
    
    // averaging
    foreach ($fields as $field)
       $rows['AVERAGE'][$field] = round( $rows['AVERAGE'][$field] / count($rows) );
        
    $rows['AVERAGE']['avglenoutbound'] = ratio($rows['AVERAGE']['totaloutbound'],    $rows['AVERAGE']['success_outbound']);
    $rows['AVERAGE']['avglenlongoutbound'] = ratio($rows['AVERAGE']['totallong4outbound'],    $rows['AVERAGE']['countlong4outbound']);
    $rows['AVERAGE']['avgleninbound']  = ratio($rows['AVERAGE']['totallonginbound'], $rows['AVERAGE']['longinbound']);
    $rows['AVERAGE']['avglen']         = ratio( $rows['AVERAGE']['totaloutbound']+$rows['AVERAGE']['totallonginbound'] , $rows['AVERAGE']['success_outbound']+$rows['AVERAGE']['longinbound']);
    $rows['AVERAGE']['percentunsuccessful'] = round(100 - 100 * $rows['AVERAGE']['success_outbound'] / $rows['AVERAGE']['outbound']);
    
    return $rows;
}

?>

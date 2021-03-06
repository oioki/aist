<?php

######################################################################################
# WHERE modificators
######################################################################################


function where_time_logins()
{
    global $from, $to, $tzoffset;
    if ( $from != '' )
    {
        $where = ( $to != '' ) ? "`date` BETWEEN '$from 00:00:00'-INTERVAL $tzoffset HOUR AND '$to 23:59:59'-INTERVAL $tzoffset HOUR" : "`date` >= '$from 00:00:00' - INTERVAL $tzoffset HOUR";
    }
    else
    {
        $where = ( $to != '' ) ? "`date` <= UNIX_TIMESTAMP('$to 23:59:59')" : "1";
    }
    return $where;
}





######################################################################################
# Data conversion
######################################################################################

function ip($origIP)
{
  $origIP = long2ip($origIP);
  $ar=explode(".",$origIP);
  return "$ar[3].$ar[2].$ar[1].$ar[0]";
}

function dat($date, $oneday)
{
    if ( $oneday )
        return date("H:i:s", $date);
    else
        return date("Y-m-d H:i:s", $date);
}

function datMySQL($d)
{
    $arr = explode(".", $d);
    return ( $d == '' ) ? "" : $arr[2]."-".$arr[1]."-".$arr[0];
}

function tim($time)
{
    $mins = (int)($time / 60);
    if ( $mins < 10 ) $mins = "0$mins";
    $secs = $time % 60;
    if ( $secs < 10 ) $secs = "0$secs";
    return "$mins:$secs";
}

function timHours($time)
{
    $hours = (int)($time / 3600);
    if ( $hours < 10 ) $hours = "0$hours";
    $mins = (int)(($time%3600) / 60);
    if ( $mins < 10 ) $mins = "0$mins";
    $secs = $time % 60;
    if ( $secs < 10 ) $secs = "0$secs";
    return "$hours:$mins:$secs";
}

function cause($str)
{
    $causes = array(
0 => "-",//"Нет ошибок",
1 =>"Неназначенный номер",
2 =>"Отсутствует маршрут для указанной транзитной сети (национального применения)",
3 =>"Отсутствует маршрут к указанному номеру",
4 =>"Послать определенный информационный тон",
5 =>"Неправильно набран транковый префикс",
6 =>"Недопустимый канал",
7 =>"Вызов назначен на занятый канал",
8 =>"внеочередное занятие линии",
9 =>"внеочередное занятие линии – цепь зарезервирована для повторного использования",
16 =>"OK",//"Нормальное завершение вызова",
17 =>"Абонент занят",
18 =>"Нет реакции абонента",
19 =>"Нет ответа от абонента (абонент уведомлен)",
20 =>"Абонент отсутствует",
21 =>"Вызов забракован",
22 =>"Номер изменен",
26 =>"Разъединение недоступного абонента",
27 =>"Номер назначения вызова вне допустимого диапазона",
28 =>"Неправильный формат номера (незаконченный адрес)",
29 =>"Услуга отклонена",
30 =>"Отклик для Status Enquiry (проблемное состояние)",
31 =>"Нормальный (точно не установленный)",
34 =>"Нет доступных цепей/каналов",
38 =>"Сеть вне допустимого диапазона",
39 =>"Постоянное соединение режима передачи данных вне обслуживания",
40 =>"Постоянное соединение режима передачи данных в эксплуатации",
41 =>"Временный сбой",
42 =>"Перегрузка коммутационного оборудования",
43 =>"Информация о доступе отвергнута",
44 =>"Запрашиваемая цепь/канал недоступна",
46 =>"Заблокирована очередность выполнения заданий вызова",
47 =>"Ресурсы недоступны или неопределены",
49 =>"Недоступное качество обслуживания",
50 =>"Затребованная услуга не была ранее заказана",
53 =>"Нарушена операция обслуживания",
54 =>"Входящие вызовы запрещены",
55 =>"Входящие вызовы запрещены в Закрытой Группе Абонентов (Closed User Group - CUG)",
57 =>"Не авторизированная возможность несущей",
58 =>"Возможность несущей в текущий момент не доступна",
62 =>"Несовместимость в назначенной исходящей информации доступа и классом абонента",
63 =>"Сервис или возможность не доступны",
65 =>"Возможность несущей не выполнима",
66 =>"Невыполнимый тип канала",
69 =>"Затребованное оборудование не осуществимо",
70 =>"Только зарегистрированная цифровая информация возможности несущей доступна",
79 =>"Невыполнимый сервис или функция",
81 =>"Неверная величина ссылки на вызов",
82 =>"Идентифицируемый канал отсутствует",
83 =>"Приостановленный вызов существует, но невозможна его идентификация",
84 =>"Идентификация вызова уже используется",
85 =>"Нет отложенных вызовов",
86 =>"Вызов, имеющий затребованный идентификатор вызова, был прекращен",
87 =>"Абонент не является членом Закрытой Группы Абонентов",
88 =>"Несовместимый номер назначения",
90 =>"Номер назначения отсутствует и DC не подписан",
91 =>"Неправильный выбор транзитной сети",
95 =>"Неверное сообщение, точно не установлено",
96 =>"Отсутствует обязательный информационный элемент",
97 =>"Тип сообщения отсутствует или не реализован",
98 =>"Сообщение не соответствует состоянию вызова или тип сообщения отсутствует или не реализован",
99 =>"Информационный элемент или параметр отсутствует",
100 =>"Неверное содержимое информационного элемента",
101 =>"Сообщение не соответствует состоянию вызова",
102 =>"Вызов прерван, когда истек таймер, и выполнена восстановительная подпрограмма для восстановления из ошибочного состояния",
103 =>"Отсутствует или не реализован параметр",
110 =>"Сообщение с нераспознанным параметром не учитывается",
111 =>"Ошибка протокола, точно не установлена",
122 =>"Превышен приоритетный уровень (это специфичный для Cisco код)",
123 =>"Устройство не возможно выгрузить (это специфичный для Cisco код)",
124 =>"Конференция заполнена (это специфичный для Cisco код)",
125 =>"Превышена полоса пропускания (это специфичный для Cisco код)",
126 =>"Вызов разделен. Это специфичный для Cisco код применяется тогда, когда вызов прерывается во время передачи вызова, т.к. он отделяется и прекращается (не та часть, которая является оконечной частью трансферного вызова).",
127 =>"обеспечение межсетевого обмена, точно не установлено",
128 =>"Функция сброса любого абонента/сброса последнего абонента конференции",
129 =>"Приоритет вне полосы пропускания",

    );

    return (isset($causes[$str])) ? $causes[$str] : "Unknown $str";
}


function ratio($a, $b)
{
    return ($b==0) ? 0 : round($a / $b, 1);
}


?>

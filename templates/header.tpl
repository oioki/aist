{config_load file="app.conf" section="global"}
<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="st/{#style#}"/>
<link rel="stylesheet" type="text/css" href="st/jquery-ui.css"/>
<link id="page_favicon" href="st/favicon.png" rel="icon" type="image/x-icon" />
<TITLE>{#sitename#} - {$title}</TITLE>
<script src="st/jquery.min.js" type="text/javascript"></script>
<script src="st/jquery-ui.min.js" type="text/javascript"></script>
<script src="st/jquery-ui-i18n.min.js" type="text/javascript"></script>
</HEAD>
<BODY bgcolor="#ffffff">
<script>
$(function() {
 $( "#date_from" ).datepicker();
 $( "#date_from" ).datepicker("option", $.datepicker.regional["ru"] );
 $( "#date_to" ).datepicker();
 $( "#date_to" ).datepicker("option", $.datepicker.regional["ru"] );
 {if $from}$( "#date_from" ).datepicker("setDate", "{$from}");{/if}
 {if $to}  $( "#date_to"   ).datepicker("setDate", "{$to}");{/if}
});
</script>
<center><h1>
<a href=".?from={$from}&to={$to}">Звонки</a> &bullet; 
<a href="rating.php?from={$from}&to={$to}">Статистика за день</a> &bullet; 
<a href="history.php?from={$from}">Статистика за месяц</a>
{if $role eq 'admin'} &bullet;
<a href="extensions.php">Внутренние номера</a> &bullet;
<a href="users.php">Пользователи</a> &bullet;
<a href="logins.php?from={$from}&to={$to}">Журнал входов</a>
{/if}
</h1></center>

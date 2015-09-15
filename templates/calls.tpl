{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Звонки"}

{if $extensions_table_exists eq '0'}
<div class="warning">Рекомендуется <a href="extensions.php">заполнить таблицу внутренних номеров</a>.</div>
{/if}

<script type="text/javascript">
function play(href)
{
    window.open(href, 'player', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=450, height=60');
    return false;
}
</script>

{include file="filter.tpl"}

<table class="info" style="width:25%">
<thead><td></td><td>Звонки</td><td>Время</td><td>Секунды</td></thead>
<tr><td>Исходящие успешные</td><td align="right">{$c_soutbound}</td><td align="center">{$s_houtbound}</td><td align="right">{$s_outbound}</td></tr>
<tr><td>Исходящие всего</td><td align="right">{$c_outbound}</td><td align="center">&mdash;</td><td align="center">&mdash;</td></tr>
<tr><td>Входящие успешные</td><td align="right">{$c_sincoming}</td><td align="center">{$s_hincoming}</td><td align="right">{$s_incoming}</td></tr>
<tr><td>Входящие всего</td><td align="right">{$c_incoming}</td><td align="center">&mdash;</td><td align="center">&mdash;</td></tr>
<tr><td>Внутренние</td><td align="right">{$c_internal}</td><td align="center">{$s_hinternal}</td><td align="right">{$s_internal}</td></tr>
<tr><td>ВСЕГО</td><td align="right">{$c_outbound+$c_incoming+$c_internal}</td><td align="center"></td><td align="right">{$s_outbound+$s_incoming+$s_internal}</td></tr>
</table>
Суммарное время дозвона: {$s_ringtime}<br/>

<br/>
Фильтр по результату:
<button type="button" onclick='$(".neg").css("display", "");$(".suc").css("display", "");'>Все</button>
<button type="button" onclick='$(".neg").css("display", "none");$(".suc").css("display", "");'>Разговор</button>
<button type="button" onclick='$(".neg").css("display", "");$(".suc").css("display", "none");'>Неуспешные</button>
<br/><br/>

Выбрано звонков: {$calls|@count}{if $calls|@count eq 5000} <font color="red"><b>(достигнут предел выборки)</b></font>{/if}<br/>

<table class="info">
<thead>
{if $role eq 'supervisor' or $role eq 'admin'}<td></td>{/if}
<td>Время (МСК)</td>
<td>Направление</td>
<td>Источник</td>
<td>Получатель</td>
<td>Время начала</td>
<td>Время конца</td>
<td>Дозвон</td>
<td>Длительность</td>
<td>Результат</td>
</thead>
<tbody>
{foreach from=$calls item=item}
<tr class='{$item.class}'>{if $role eq 'supervisor' or $role eq 'admin'}<td>{if $item.cause eq 'Разговор'}<a href="{$item.downloadlink}" download={$item.downloadlabel}><img src='st/download.png'/></a><a href="{$item.playlink}" onclick="return play(this.href);"><img src='st/play.png'/></a>{/if}</td>{/if}<td>{$item.dateTimeOrigination}</td><td>{$item.direction}{if $item.img}&nbsp;<img src='st/{$item.img}.png'/>{/if}</td><td>{$item.callingPartyNumber}{if $item.srcname} - {$item.srcname}{/if}</td><td>{$item.finalCalledPartyNumber}{if $item.marker}<font color="{$item.marker}">*</font>{/if}{if $item.dstname} - {$item.dstname}{/if}</td><td>{$item.dateTimeConnect}</td><td>{$item.dateTimeDisconnect}</td><td>{$item.ringtime}</td><td>{$item.duration}</td><td>{$item.cause}</td></tr>
{/foreach}
</tbody>
</table>

<hr>

{include file="footer.tpl"}

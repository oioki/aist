{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Статистика за день"}
<meta http-equiv="refresh" content="300">

{include file="filter.tpl"}

<i>
Internal - любые попытки дозвона (исходящие и входящие)<br/>
Outbound - успешные исходящие дозвоны<br/>
TotalOutbound - общая длительность исходящих вызовов, в минутах<br/>
TotalInbound - общая длительность входящих вызовов, в минутах<br/>
Сортировка выполняется по полю <b>Count of calls ≥ 4 minutes</b>
</i>

<table class="info" style="width:100%">
<thead>
    <td>Оператор</td>
    <td>Internal</td>
    <td>Outbound</td>
    <td>TotalOutbound, min</td>
    <td>TotalInbound, min</td>
    <td>Avg. length<br/>of outbound, min</td>
    <td>Avg. length<br/>of outbound (≥4min), sec</td>
    <td>Avg. length<br/>of inbound (≥15sec), min</td>
    <td>Avg. length<br/>of all calls, min</td>
    <td>Count of outbound<br/>calls ≥ 4 minutes</td>
    <td>Count of inbound<br/>calls ≥ 4 minutes</td>
    <td>Count of<br/>calls ≥ 4 minutes</td>
    <td>Count of outbound<br/>calls ≥ 6 minutes</td>
    <td>Count of inbound<br/>calls ≥ 6 minutes</td>
    <td>% of outbound calls<br/>unsuccessful</td>
</thead>
{foreach from=$stat item=item}
{if $item.ext eq 'AVERAGE'}
<tr>
    <td class="x">AVERAGE</td>
    <td class="x">{$item.outbound+$item.inbound} ({$item.outbound} ; {$item.inbound})</td>
    <td class="x">{$item.success_outbound}</td>
    <td class="x">{$item.totaloutbound}</td>
    <td class="x">{$item.totalinbound}</td>
    <td class="x">{$item.avglenoutbound}</td>
    <td class="x">{$item.avglenlongoutbound}</td>
    <td class="x">{$item.avgleninbound}</td>
    <td class="x">{$item.avglen}</td>
    <td class="x">{$item.countlong4outbound}</td>
    <td class="x">{$item.countlong4inbound}</td>
    <td class="x">{$item.countlong4inbound+$item.countlong4outbound}</td>
    <td class="x">{$item.countlong6outbound}</td>
    <td class="x">{$item.countlong6inbound}</td>
    <td class="x">{$item.percentunsuccessful} %</td>
</tr>
{else}
<tr>
    <td>{$item.ext}{if $item.name} - {$item.name}{/if}</td>
    <td>{$item.outbound+$item.inbound} ({$item.outbound} + {$item.inbound})</td>
    <td>{$item.success_outbound}</td>
    <td>{$item.totaloutbound}</td>
    <td>{$item.totalinbound}</td>
    <td class="x">{$item.avglenoutbound}</td>
    <td class="x">{$item.avglenlongoutbound}</td>
    <td class="x">{$item.avgleninbound}</td>
    <td class="x">{$item.avglen}</td>
    <td class="x">{$item.countlong4outbound}</td>
    <td class="x">{$item.countlong4inbound}</td>
    <td class="x">{$item.countlong4inbound+$item.countlong4outbound}</td>
    <td class="x">{$item.countlong6outbound}</td>
    <td class="x">{$item.countlong6inbound}</td>
    <td class="x">{$item.percentunsuccessful} %</td>
</tr>
{/if}
{/foreach}
</table>

<hr>

{include file="footer.tpl"}

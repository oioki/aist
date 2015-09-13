{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Статистика за месяц"}

Выберите день интересующего месяца:
{include file="filter-oneday.tpl"}

<i>Сортировка выполняется по полю <b>Monthly count of calls ≥ 4 minutes</b></i>

<br/><br/>
<table class="info" style="width:100%">
<thead>
    <td>Оператор</td>
    <td>Monthly Avg. length<br/>of outbound, min</td>
    <td>Monthly Avg. length<br/>of inbound (≥15sec), min</td>
    <td>Monthly Avg. length<br/>of all calls, min</td>
    <td>Monthly count of outbound<br/>calls ≥ 4 minutes</td>
    <td>Monthly count of inbound<br/>calls ≥ 4 minutes</td>
    <td>Monthly count<br/>of calls ≥ 4 minutes</td>
    <td>Monthly count of outbound<br/>calls ≥ 6 minutes</td>
    <td>Monthly count of inbound<br/>calls ≥ 6 minutes</td>
    <td>Monthly % of outbound calls<br/>unsuccessful</td>
</thead>
{foreach from=$stat item=item}
{if $item.ext eq 'AVGMONTHLY'}
<tr>
    <td class="x">AVERAGE</td>
    <td class="x">{$item.avgLenOutbound}</td>
    <td class="x">{$item.avgLenInbound}</td>
    <td class="x">{$item.avgLen}</td>
    <td class="x">{$item.cntLong4Outbound}</td>
    <td class="x">{$item.cntLong4Inbound}</td>
    <td class="x">{$item.cntLong4Inbound+$item.cntLong4Outbound}</td>
    <td class="x">{$item.cntLong6Outbound}</td>
    <td class="x">{$item.cntLong6Inbound}</td>
    <td class="x">{$item.percentUnsuccessful} %</td>
</tr>
{else}
<tr>
    <td>{$item.ext}{if $item.name} - {$item.name}{/if}</td>
    <td class="x">{$item.avgLenOutbound}</td>
    <td class="x">{$item.avgLenInbound}</td>
    <td class="x">{$item.avgLen}</td>
    <td class="x">{$item.cntLong4Outbound}</td>
    <td class="x">{$item.cntLong4Inbound}</td>
    <td class="x">{$item.cntLong4Inbound+$item.cntLong4Outbound}</td>
    <td class="x">{$item.cntLong6Outbound}</td>
    <td class="x">{$item.cntLong6Inbound}</td>
    <td class="x">{$item.percentUnsuccessful} %</td>
</tr>
{/if}
{/foreach}
</table>

<hr>

{include file="footer.tpl"}

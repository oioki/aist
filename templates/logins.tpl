{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Журнал входов"}

{include file="filter.tpl"}

<table class="info" style="width:500px">
<thead><td>Дата и время (MSK)</td><td>Менеджер</td><td>IP-адрес</td></thead>
{foreach from=$logins item=login}
<tr class="{if $login.result eq '0'}missed{/if}">
    <td>{$login.date}</td>
    <td>{$login.login}</td>
    <td>{$login.ip}</td>
</tr>
{/foreach}
</table>

<hr>

{include file="footer.tpl"}

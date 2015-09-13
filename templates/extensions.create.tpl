{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Внутренние номера"}

<div class="question">В вашей базе данных нет таблицы {#extensions#}.
<form action="extensions.php" method="POST">
<input type="hidden" name="action" value="createtable">
<input type="submit" value="СОЗДАТЬ">
</form>
</div>

<hr>

{include file="footer.tpl"}

{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Внутренние номера"}

<script type="text/javascript">
function submitForm(element)
{
        element.type = 'hidden';
        
        while(element.className != 'form')
                element = element.parentNode;
        
        var form = document.getElementById('poster');
        
        var inputs = element.getElementsByTagName('input');
        while(inputs.length > 0)
                form.appendChild(inputs[0]);
        
        var selects = element.getElementsByTagName('select');
        while(selects.length > 0)
                form.appendChild(selects[0]);
        
        var textareas = element.getElementsByTagName('textarea');
        while(textareas.length > 0)
                form.appendChild(textareas[0]);
        
        element.innerHTML = '<tr><td colspan="4"><center>Updating...</center></td></tr>';
        
        form.submit();
}
</script>

<form id="poster" action="extensions.php" method="POST" style="display: none;"></form>

{if $msg}<i>{$msg}</i><br/><br/>{/if}

<table class="info" style="width:400px">
<thead><td>Номер</td><td>Фамилия Имя</td><td></td></thead>
{foreach from=$extensions item=i}
<tr class="form" style="height:20px">
    <td><input type="hidden" name="oldext" value="{$i.ext}">
    <input type="text" name="ext" size="4" value="{$i.ext}"></td>
    <td><input type="text" name="name" size="40" value="{$i.fullname}"></td>
    <td><input type="submit" value="OK" onclick="submitForm(this);"></td>
</tr>
{/foreach}
</table>

<br/>
Добавить нового оператора:
<table class="info" style="width:500px">
<thead width="100%"><td>Номер</td><td>Фамилия Имя</td><td></td></thead>
<tr class="form" style="height:20px">
    <td><input type="text" name="ext" value=""></td>
    <td><input type="text" name="name" value=""></td>
    <td><input type="submit" value="OK" onclick="submitForm(this);"></td>
</tr>
</table>

<hr>

{include file="footer.tpl"}

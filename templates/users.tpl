{config_load file="app.conf" section="global"}
{include file="header.tpl" title="Пользователи"}

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
        
        element.innerHTML = '<tr><td colspan="6"><center>Updating...</center></td></tr>';
        
        form.submit();
}
</script>

Добавить нового менеджера и отправить ему email:
<table class="info" style="width:350px">
<thead><td>Email</td><td></td></thead>
<tr class="form" style="height:20px">
    <td><center><input type="text" name="email" size="30" value=""></center></td>
    <td><input type="submit" value="OK" onclick="submitForm(this);"></td>
</tr>
</table>
<br/>

<i>
<b>Пользователь</b> может просматривать звонки и дневную/месячную статистику.<br/>
<b>Супервизор</b>, в дополнение к этому, может прослушивать записи звонков.<br/>
<b>Администратор</b>, ко всему перечисленному, может добавлять/удалять extensions, пользователей и просматривать журнал входов.<br/>
</i>
<br/>
<form id="poster" action="users.php" method="POST" style="display: none;"><input type="hidden" id="action" name="action" value=""/></form>

{if $msg}<b><i>{$msg}</i></b><br/><br/>{/if}

<table class="info" style="width:800px">
<thead><td>Логин</td><td>Смена пароля</td><td>Роль</td><td></td><td>Последний&nbsp;вход</td></thead>
{foreach from=$users item=i}
<tr class="form" style="height:20px">
    <td><input type="hidden" name="oldlogin" value="{$i.login}"><input type="text" name="login" value="{$i.login}"></td>
    <td><input type="password" name="pass" value=""></td>
    <td><select name="role">
      <option value="user">Пользователь</option>
      <option value="supervisor"{if $i.role eq 'supervisor'} selected{/if}>Супервизор</option>
      <option value="admin"{if $i.role eq 'admin'} selected{/if}>Администратор</option>
    </select></td>
    <td><input type="submit" value="OK" onclick="submitForm(this);"><button type="submit" value="delete" onclick="$('#action')[0].value='delete';submitForm(this);">Удалить</button></td>
    <td>{$i.lastlogin}</td>
</tr>
{/foreach}
</table>

<hr>
<br/>

{include file="footer.tpl"}

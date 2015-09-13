<FORM ACTION="" METHOD="GET">
<table>

{if $mode eq 'calls'}
<tr><td>Источник:</td><td><input type="text" name="src" value="{$src}"/></td></tr>
<tr><td>Получатель:</td><td><input type="text" name="dst" value="{$dst}"/></td></tr>
<tr><td>Оператор:</td><td><input type="text" name="srcdst" value="{$srcdst}"/> <font color="gray">независимо от направления звонка</font></td></tr>
<tr><td>Направление:</td><td><select name="direction">
 <option value=""></option>
 <option {if $direction eq 'outbound'}selected{/if} value="outbound">Исходящий</option>
 <option {if $direction eq 'incoming'}selected{/if} value="incoming">Входящий</option>
 <option {if $direction eq 'internal'}selected{/if} value="internal">Внутренний</option>
 <option {if $direction eq 'incorrect'}selected{/if} value="incorrect">Некорректный</option>
</select></td></tr>
{/if}

<tr><td colspan="2">
С <input type="text" id="date_from" name="from" size="10" onchange="$('#date_to').datepicker('setDate', $('#date_from').datepicker('getDate'));"/>
до <input type="text" id="date_to"   name="to"   size="10"/>
</td></tr>
<tr><td colspan="2">
<INPUT TYPE="submit" VALUE="ОК">
</td></tr>
</FORM>

<?php
function select($var,$name,$value)
{
	$string="<select name={$name}_id>";
	foreach ($var->result() as $v) 
	{
		$string.="<option value={$v->id} ".($v->id==$value?" selected ":"")." >{$v->name}</option>";
	}
	$string.="</select><br>";
	echo $string;
}
?>

<b>Редактирование счетчика</b>
<br>
<?php echo form_open('billing/changing_counter/'.$counter->id);?>

<table>
    <tr>
        <td align="right">Коэффициент трансформации</td>
        <td><input name="transform" value="<?php echo $counter->transform; ?>"></td>
    </tr>
    <tr>
        <td align="right">Разрядность</td>
        <td><input name="digit_count" value=""<?php echo $counter->digit_count; ?>"></td>
    </tr>
    <tr>
        <td align="right">Гос номер</td>
        <td><input name="gos_nomer" value="<?php echo $counter->gos_nomer; ?>"></td>
    </tr>
    <tr>
        <td align="right">Пломбы</td>
        <td><input name="seal" value="<?php echo $counter->seal; ?>"></td>
    </tr>
    <tr>
        <td align="right">Дата гос проверки</td>
        <td><input name="data_gos_proverki" value="<?php echo $counter->data_gos_proverki; ?>"></td>
    </tr>
    <tr>
        <td align="right">Тип счетчика</td>
        <td><?php select($counter_type, 'type', $counter->type_id); ?></td>
    </tr>
</table>


<br>
<input type=submit value='Обновить'>
</form>
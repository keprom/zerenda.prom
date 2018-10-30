<?php echo form_open('billing/svod');

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
Начальный период : <?php select($period,'start_period',-1); ?> <br>
Конечный период : <?php select($period,'finish_period',-1); ?> <br>
<br>
<select name=org_type >
<option value=0>без деления</option>
<option value=1>ТОО</option>
<option value=2>ИП ЧП</option>
</select><br><br>
<input type=submit value='Выдать'>

</form>
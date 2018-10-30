<?php echo form_open('billing/ktpwki');

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
<select name=period_id>
<?php foreach ($periods->result() as $period):?>
	<option value=<?php echo $period->id;?>><?php echo $period->name;?></option>
<?php endforeach;?>
</select>
<br>
КТП : 
<select name=tp_id >

<?php foreach ($tp->result() as $t):?>
	<option value=<?php echo $t->id;?>><?php echo $t->name;?></option>
<?php endforeach;?>
<br><br>
<input type=submit value='Выдать'>

</form>
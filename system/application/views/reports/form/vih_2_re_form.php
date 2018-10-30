<?php echo form_open("billing/vih_2_re"); ?>
<select name=period_id>
<?php foreach ($periods->result() as $period):?>
	<option value=<?php echo $period->id;?>><?php echo $period->name;?></option>
<?php endforeach;?>
</select>
<br>
<br>
<select name=ture_id>
<option value=-1 selected >Выдать всех </option>
<?php foreach ($ture->result() as $t):?>
	<option value=<?php echo $t->id;?>><?php echo $t->name;?></option>
<?php endforeach;?>
</select>
<br>
<input type=submit value='Отчет 2-РЭ' />
</form>

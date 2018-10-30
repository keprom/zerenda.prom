<?php echo form_open("billing/vih_7_re"); ?>
<select name=period_id>
<?php foreach ($periods->result() as $period):?>
	<option value=<?php echo $period->id;?>><?php echo $period->name;?></option>
<?php endforeach;?>
</select>
<br>
<select name=type >
<option value=1 >Выдать всех</option>
<option value=2 >Выдать дебиторов</option>
<option value=3 >Выдать кредиторов</option>
</select>
<br>
<br>
<input type=submit value='Отчет 7-РЭ' />
</form>

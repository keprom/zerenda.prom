<table>
<tr><td><b>Название тп</b></td></tr>
<?php foreach($query->result() as $row): ?>
<tr><td><?php echo $row->name; ?></td></tr>
<?php endforeach;?>
</table>

<h4>добавить ТП</h4>
<?php echo form_open("billing/adding_tp"); ?>
Наименование тп <input name="name" />
<br/><br/>
<input type=submit value="добавить тп" />
</form>

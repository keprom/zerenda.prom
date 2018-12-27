<?php
$r=$r->row();
echo form_open("billing/schetfactura");
echo "<input type=hidden name=firm_id value=".$firm_id." >";
echo "<br>Выдать счетфактуру <input type=checkbox name=new_schetfactura1  ><br>";
echo "Выдача накладной <input type=checkbox name=nakladnaya><br>";
echo "<input type=hidden name=period_id value=".$period_id." >";
function if_then($var,$then)
{
	if (strlen(trim($var))==0)
	{
		return $then;
	}
	else
	{
		return $var;
	}
}
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['2'].'.'.$d['1'].'.'.$d['0'];
}
?>
Номер счета фактуры: <input name=number_schet value='' ><br>
Последний номер счета-фактуры <input type="text" disabled value="<?php echo $max_schet_number; ?>"><br><br>
Другая дата выдачи: <input name=data_schet value='<?php echo datetostring($r->date) ;?>' > <br><br>
Условия оплаты по договору: <input name=edit1 value='<?php echo if_then($r->edit1,"договор"); ?>' > <br><br>
Пункт назначения поставляемых товаров (работ, услуг): 
<input name=edit2 value='<?php echo if_then($r->edit2,""); ?>'> <br> <br>
Поставка товаров осуществлена по доверености: 
<input name=edit3 value='<?php echo if_then($r->edit3,"договор"); ?>'> <br> <br>
Способ отправления: <input name=edit4  value='<?php echo if_then($r->edit4,"транзит"); ?>'> <br> <br>
Грузоотправитель:<input name=edit5  value='<?php echo if_then($r->edit5,$org_info->org_name." РНН ".$org_info->rnn); ?>'> <br> <br>
Грузополучатель:<input name=edit6  value='<?php echo if_then($r->edit6,$firm->name." РНН ".$firm->rnn); ?>'> <br> <br>

<?php

echo "<input type=submit value='Открыть счетфактуру' />";
echo "</form>";


?>
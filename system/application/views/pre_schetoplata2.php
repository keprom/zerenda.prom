<?php
echo form_open("billing/schetoplata");
echo "<input type=hidden name=firm_id value=".$firm_id." >";
echo "<input type=hidden name=period_id value=".$period_id." >";
echo "<input type=hidden name=tariff_count value=".$tariffs->num_rows()." >";
echo "Номер счета фактуры: <input name=number_schet value='' ><br>";
echo "Другая дата <input  name=data_schet value='' ><br>";
echo "Другое название товара <input  name=tovar_name value='' ><br>";
echo "<input type=hidden name=type value='by_tenge' >";
echo "Выдать счет фактурой <input type=checkbox name=schet  ><br>";

$i=0;
foreach ($tariffs->result() as  $tariff)
{
	echo "Сумма тенге <input name=tariff[{$i}] > по тарифу {$tariff->tariff_value}<br>";
	echo "<input type=hidden name=tariff_value[{$i}] value='{$tariff->tariff_value}' >";
	$i++;
}
echo "<input type=submit value='Открыть счет на оплату' />";
echo "</form>";


echo form_open("billing/schetoplata");
echo "<input type=hidden name=firm_id value=".$firm_id." >";
echo "<input type=hidden name=type value='by_kvt' >";
echo "<input type=hidden name=period_id value=".$period_id." >";
echo "<input type=hidden name=tariff_count value=".$tariffs->num_rows()." >";
echo "Номер счета фактуры: <input name=number_schet value='' ><br>";
echo "Другая дата <input  name=data_schet value='' ><br>";
echo "Другое название товара <input  name=tovar_name value='' ><br>";

echo "Выдать счет фактурой <input type=checkbox name=schet   ><br>";

$i=0;
foreach ($tariffs->result() as  $tariff)
{
	echo "Кол-во кВт <input name=tariff[{$i}] > по тарифу {$tariff->tariff_value}<br>";
	echo "<input type=hidden name=tariff_value[{$i}] value='{$tariff->tariff_value}' >";
	$i++;
}
echo "<input type=submit value='Открыть счет на оплату' />";
echo "</form>";
?>
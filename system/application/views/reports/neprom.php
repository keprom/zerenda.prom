<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>

<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var);
}
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['0'].'.'.$d['1'].'.'.$d['2'];
}
echo "<table>";
echo "<tr>
			<td>Договор</td>
		<td>Наименование предприятия, относящиеся к группе потребителей Непром</td>
		<td>Наименование счетчика</td>
		<td>Дата госпроверки</td>
		<td>Номер счетчика</td>
		</tr>";
		$j=1;
foreach ($prom->result() as $n)
{
	echo "<tr>
		
		<td>{$n->dogovor}</td>
		<td>{$n->name}</td>
		<td>{$n->counter_name}</td>
		<td>{$n->data_gos_proverki}</td>
		<td>{$n->gos_nomer}</td>
		</tr>";
}
?>
</table>
</html>
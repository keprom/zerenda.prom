<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var);
}?>
<html>
<head>
<title>Информация о точках учета </title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<center>Информация по точкам учета</center><br />
<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
<tr>

<td>№</td>
<td>№ договора</td>
<td>Наименование предприятия</td>
<td>
Наименование обьекта 
</td>
<td>
место установки учета
</td>
<td>
Тип счетчика
</td>
<td>
Номер счетчика
</td>
<td>
Дата выпуска
</td>
<td>
Коэфициент трансформации
</td>
<td>
Установленная мощность эл. установок
</td>
<td>
Кол-во часов работы в сутки
</td>

</tr>

<?php $j=1;foreach($info->result() as $i ):?>
<tr>
<td><?php echo $j++;?></td>
<td>
<?php echo $i->firm_dogovor;?>
</td>
<td>
<?php echo $i->firm_name;?>
</td>
<td>
<?php echo $i->billing_point_name;?>
</td>
<td>
<?php echo $i->billing_point_address;?>
</td>
<td>
<?php echo $i->counter_type_name;?>
</td>
<td>
&nbsp;<?php echo $i->gos_nomer;?>
</td>
<td>
&nbsp;<?php echo $i->crafted_year;?>
</td>
<td>
&nbsp;<?php echo $i->transform;?>
</td>
<td>
&nbsp;<?php echo $i->power;?>
</td>
<td>
&nbsp;<?php echo $i->time_in_day;?>
</td>


</tr>
<?php endforeach;?>
</table>
</body>
</html>
